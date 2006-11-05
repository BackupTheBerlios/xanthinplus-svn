<?php
/*
* This file is part of the xanthin+ project.
*
* Copyright (C) 2006  Mario Casciaro <xshadow [at] email (dot) it>
*
* Licensed under: 
*   - Apache License, Version 2.0 or
*   - GNU General Public License (GPL)
* You should have received at least one copy of them along with this program.
*
* THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS" 
* AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, 
* THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR 
* PURPOSE ARE DISCLAIMED.SEE YOUR CHOOSEN LICENSE FOR MORE DETAILS.
*/


/**
 * Represent a menu item.
 */
class xMenuItem extends xElement
{
	/**
	 * @var int
	 */
	var $m_id;
	
	/**
	 * @var string
	 */
	var $m_lang;
	
	/**
	 * @var string
	 * @access public
	 */
	var $m_label;
	
	/**
	 * @var string
	 * @access public
	 */
	var $m_link;
	
	/**
	 * @var int
	 * @access public
	 */
	var $m_weight;
	
	/**
	 * @var array(xMenuItem)
	 * @access public
	 */
	var $m_subitems;
	
	/**
	 * Contructor
	 *
	 */
	function xMenuItem($id,$label,$link,$weight,$lang,$subitems = array())
	{
		$this->m_id = $id;
		$this->m_label = $label;
		$this->m_link = $link;
		$this->m_weight = $weight;
		$this->m_subitems = $subitems;
		$this->m_lang = $lang;
	}
	
	// DOCS INHERITHED  ========================================================
	function render()
	{
		usort($this->m_subitems, "_objWeightCompare");
		$subitems = xTheme::render('renderMenuItems',array($this->m_subitems));

		$error = '';
		$label = xContentFilterController::applyFilter('notags', $this->m_label, $error);
		$link = xContentFilterController::applyFilter('notags', $this->m_link, $error);
		return xTheme::render('renderMenuItem',array($label,$link,$subitems));
	}
};



/**
 * Represent a simple link menu.
 */
class xMenu extends xBoxI18N
{	
	/**
	 * @var string
	 */
	var $lang;
	
	
	/**
	 * @var array(xMenuItem)
	 */
	var $m_items;
	
	/**
	* Contructor
	*
	* @param string $name
	* @param string $title
	* @param string $type
	* @param string $area
	*/
	function xMenu($name,$type,$weight,$show_filter,$title,$lang,$items = array())
	{
		$this->xBoxI18N($name,$type,$weight,$show_filter,$title,$lang);
		$this->m_items = $items;
	}
	
	// DOCS INHERITHED  ========================================================
	function render()
	{
		usort($this->m_items, "_objWeightCompare");
		$content = xTheme::render('renderMenuItems',array($this->m_items));
		
		return xTheme::render('renderBox',array($this->m_name,$this->m_title,$content));
	}
	
	/**
	 * Insert this object into db
	 *
	 * @return bool FALSE on error
	 */
	function insert()
	{
		return xMenuDAO::insert($this);
	}
	
	/**
	 * 
	 */
	function update()
	{
		return xMenuDAO::update($this);
	}
	
	/**
	 * @access private
	 */
	function _addItem($items,$item,$parent)
	{
		$ret = array();
		if(! empty($items))
		{
			foreach($items as $ite)
			{
				$ite->m_subitems = $this->_addItem($ite->m_subitems,$item,$parent);
				if($ite->m_id == $parent)
					$ite->m_subitems[] = $item;	
				$ret[] = $ite;
			}
		}	
		return $ret;
	}
	
	/**
	 *
	 */
	function addItem($item,$parent = NULL)
	{
		if(empty($parent))
			$this->m_items[] = $item;
		else
			$this->m_items = $this->_addItem($this->m_items,$item,$parent);
	}
	
	/**
	 * Insert this object into db
	 *
	 * @return bool FALSE on error
	 */
	function insertTranslation()
	{
		return xMenuDAO::insertTranslation($this);
	}
	
	/**
	 * Delete this menu translation from db.
	 *
	 * @return bool FALSE on error
	 */
	function deleteTranslation()
	{
		return xMenuDAO::deleteTranslation($this->m_name,$this->m_lang);
	}
	
	/**
	 * @return array(xOperation)
	 */
	function find($name = NULL,$type = 'menu',$lang = NULL,$flexible_lang = TRUE)
	{
		return xMenuDAO::find($name,$type,$lang,$flexible_lang);
	}
};
xBox::registerBoxTypeClass('menu','xMenu');





/**
 * A module for box
 */
class xModuleMenu extends xModule
{
	function xModuleMenu()
	{
		xModule::xModule();
	}


	/**
	 * @see xDummyModule::xm_fetchContent()
	 */ 
	function xm_fetchContent($path)
	{
		if($path->m_resource === 'box' && $path->m_action === 'edit' && $path->m_type === 'menu' 
			&& $path->m_id !== NULL)
		{
			return new xResult(new xPageContentBoxEdit($path));
		}
		elseif($path->m_resource === 'box' && $path->m_action === 'create' && $path->m_type === 'menu')
		{
			return new xResult(new xPageContentBoxCreateMenu($path));
		}
		elseif($path->m_resource === 'box' && $path->m_action === 'translate' && $path->m_type === 'menu'
			&& $path->m_id !== NULL)
		{
			return new xResult(new xPageContentBoxTranslateMenu($path));
		}
		elseif($path->m_resource === 'box' && $path->m_action === 'edit_translation' && $path->m_type === 'menu'
			&& $path->m_id !== NULL)
		{
			return new xResult(new xPageContentBoxEditTranslationMenu($path));
		}
		
		return NULL;
	}
	
	/**
	 * @see xDummyModule::xm_fetchPermissionDescriptors()
	 */ 
	function xm_fetchPermissionDescriptors()
	{
	}
};

xModule::registerDefaultModule(new xModuleMenu());




/**
 * 
 */
class xPageContentBoxCreateMenu extends xPageContentBoxCreate
{
	function xPageContentBoxCreateMenu($path)
	{
		$this->xPageContentBoxCreate($path);
	}
	
	/**
	 *
	 */
	function onCreate()
	{
		//create form
		$form = new xForm('menu_create',$this->m_path->getLink());
		
		//box name
		$form->m_elements[] = new xFormElementTextField('name','Name','','',true,new xInputValidatorTextNameId(32));
		
		//box title
		$form->m_elements[] = new xFormElementTextField('title','Title','','',true,new xInputValidatorText(128));
		
		//weight
		$options = array();
		for($i = -12;$i <= 12; $i++)
			$options[$i] = $i;
		$form->m_elements[] = new xFormElementOptions('weight','Weight','',0,$options,false,TRUE,
				new xInputValidatorInteger(-12,12));
				
		//show filter type
		$show_filter_radio = new xFormRadioGroup('Show filter type');
		$show_filter_radio->m_elements[] = new xFormElementRadio('show_filter_type','Exclusive filter',
				'',XANTH_SHOW_FILTER_EXCLUSIVE,true,TRUE,new xInputValidatorInteger(1,3));
		$show_filter_radio->m_elements[] = new xFormElementRadio('show_filter_type','Inclusive filter',
				'',XANTH_SHOW_FILTER_INCLUSIVE,false,TRUE,new xInputValidatorInteger(1,3));
		$show_filter_radio->m_elements[] = new xFormElementRadio('show_filter_type','PHP filter',
				'',XANTH_SHOW_FILTER_PHP,false,TRUE,new xInputValidatorInteger(1,3));
		$form->m_elements[] = $show_filter_radio;
		
		//show filter
		$form->m_elements[] = new xFormElementTextArea('show_filter','Show filter','','',false,
			new xInputValidatorText());
		
		//submit buttom
		$form->m_elements[] = new xFormSubmit('submit','Create');
		
		$ret = $form->validate();
		if(! $ret->isEmpty())
		{
			if(empty($ret->m_errors))
			{
				$box = new xBoxI18N($ret->m_valid_data['name'],$this->m_path->m_type,$ret->m_valid_data['weight'],
					new xShowFilter($ret->m_valid_data['show_filter_type'],$ret->m_valid_data['show_filter']),
					$ret->m_valid_data['title'],$this->m_path->m_lang);
				
				if($box->insert())
				{
					xNotifications::add(NOTIFICATION_NOTICE,'New box successfully created',2);
					$this->m_headers[] = 'Location: ' . xPath::renderLink($this->m_path->m_lang,
						'box','edit_translation',$this->m_path->m_type,$box->m_name);
				}
				else
				{
					xNotifications::add(NOTIFICATION_ERROR,'Error: box was not created');
				}
				
				$this->_set("Create new box",'','','');
				return TRUE;
			}
			else
			{
				foreach($ret->m_errors as $error)
					xNotifications::add(NOTIFICATION_WARNING,$error);
			}
		}

		$this->_set("Create new box page",$form->render(),'','');
		return TRUE;
	}
};



/**
 *
 */
class xPageContentBoxTranslateMenu extends xPageContentBoxTranslate
{

	function xPageContentBoxTranslateMenu($path)
	{
		xPageContentBoxTranslate::xPageContentBoxTranslate($path);
	}
	
	// DOCS INHERITHED  ========================================================
	function onCreate()
	{
		//create form
		$form = new xForm('translate_menu',$this->m_path->getLink());
		
		//box title
		$form->m_elements[] = new xFormElementTextField('title','Title','',$this->m_box->m_title,
			true,new xInputValidatorText(128));
		
		//submit buttom
		$form->m_elements[] = new xFormSubmit('submit','Create');
		
		$ret = $form->validate();
		if(! $ret->isEmpty())
		{
			if(empty($ret->m_errors))
			{
				$menu = $this->m_box;
				$menu->m_title = $ret->m_valid_data['title'];
				$menu->m_lang = $this->m_path->m_lang;
				$menu->m_items = array();
				
				if($menu->insertTranslation())
					xNotifications::add(NOTIFICATION_NOTICE,'New box successfully created');
				else
					xNotifications::add(NOTIFICATION_ERROR,'Error: box was not created');
				
				$this->_set("Translate box",'','','');
				return TRUE;
			}
			else
			{
				foreach($ret->m_errors as $error)
					xNotifications::add(NOTIFICATION_WARNING,$error);
			}
		}

		$this->_set("Translate box",$form->render(),'','');
		return TRUE;
	}
}




/**
 *
 */
class xPageContentBoxEditTranslationMenu extends xPageContentBoxEditTranslation
{
	var $m_box = NULL;
	
	function xPageContentBoxEditTranslationMenu($path)
	{
		xPageContentBoxEditTranslation::xPageContentBoxEditTranslation($path);
	}
	
	
	// DOCS INHERITHED  ========================================================
	function _renderItems($items,$level)
	{
		$out = '';
		$lv = str_repeat("-", $level);
		foreach($items as $item)
		{
			$out .= '<tr><td>'.$lv.' '.$item->m_label.'</td><td>'.$item->m_link.'</td><td>'.
				$item->m_weight.'</td><td>actions</td></tr>'."\n";
			if($item->m_subitems !== NULL)
				$out .= xPageContentBoxEditTranslationMenu::_renderItems($item->m_subitems,$level + 1);
		}
		
		return $out;
	}
	
	
	// DOCS INHERITHED  ========================================================
	function _generateOptions($items,$level)
	{
		$options = array();
		$lv = str_repeat("-", $level);
		foreach($items as $item)
		{
			$options[$lv . $item->m_label] = $item->m_id;
			if($item->m_subitems !== NULL)
				$options = array_merge($options,
					xPageContentBoxEditTranslationMenu::_generateOptions($item->m_subitems,$level + 1));
		}
		
		return $options;
	}
	
	// DOCS INHERITHED  ========================================================
	function onCreate()
	{
		//create form
		$form = new xForm('box_params',$this->m_path->getLink());
		
		$group = new xFormGroup('Box Params');
		
		//box title
		$group->m_elements[] = new xFormElementTextField('title','Title','',$this->m_box->m_title,
			true,new xInputValidatorText(128));
		
		//submit buttom
		$group->m_elements[] = new xFormSubmit('submit','Create');
		
		$form->m_elements[] = $group;
		
		//////////////////////////////////////////////////////////////
		
		//create form
		$form2 = new xForm('new_item',$this->m_path->getLink());
		
		$group2 = new xFormGroup('Add menu item');
		
		//label
		$group2->m_elements[] = new xFormElementTextField('label','Label','','',
			true,new xInputValidatorText(128));
			
		//link
		$group2->m_elements[] = new xFormElementTextField('link','Link','','',
			true,new xInputValidatorText(128));
			
		//weight
		$options = array();
		for($i = -12;$i <= 12; $i++)
			$options[$i] = $i;
		$group2->m_elements[] = new xFormElementOptions('weight','Weight','',0,$options,false,TRUE,
				new xInputValidatorInteger(-12,12));
		
		//parent
		$options = array_merge(array('No parent' => 0),
			xPageContentBoxEditTranslationMenu::_generateOptions($this->m_box->m_items,0));
		$group2->m_elements[] = new xFormElementOptions('parent','Parent','',0,$options,false,false,
				new xInputValidatorInteger);
				
				
		//submit buttom
		$group2->m_elements[] = new xFormSubmit('submit','Add');
		
		$form2->m_elements[] = $group2;
		
		
		//now display items
		$out = '<div><table>';
		$out .= '<tr><th>Label</th><th>Link</th><th>Weight</th><th>Actions</th></tr>';
		$out .= xPageContentBoxEditTranslationMenu::_renderItems($this->m_box->m_items,0);
		$out .= "</table></div>\n";
		
		$ret = $form->validate();
		$ret2 = $form2->validate();
		if(! $ret->isEmpty())
		{
			if(empty($ret->m_errors))
			{
				if(true)
					xNotifications::add(NOTIFICATION_NOTICE,'New box successfully created');
				else
					xNotifications::add(NOTIFICATION_ERROR,'Error: box was not created');
				
				$this->_set("Create new box",'','','');
				return TRUE;
			}
			else
			{
				foreach($ret->m_errors as $error)
					xNotifications::add(NOTIFICATION_WARNING,$error);
			}
		}
		elseif(! $ret2->isEmpty())
		{
			if(empty($ret2->m_errors))
			{
				$item = new xMenuItem(-1,$ret2->m_valid_data['label'],$ret2->m_valid_data['link'],
					$ret2->m_valid_data['weight'],$this->m_path->m_lang,array());
				
				//now find in menu and update
				$menu = $this->m_box;
				$menu->addItem($item,$ret2->m_valid_data['parent']);
				
				if($menu->update())
					xNotifications::add(NOTIFICATION_NOTICE,'New box successfully created',2);
				else
					xNotifications::add(NOTIFICATION_ERROR,'Error: box was not created',2);
				
				$this->_set("Create new box",'','','');
				$this->m_headers[] = 'Location: '. $this->m_path->getLink();
				return TRUE;
			}
			else
			{
				foreach($ret2->m_errors as $error)
					xNotifications::add(NOTIFICATION_WARNING,$error,2);
				$this->m_headers[] = 'Location : '.$this->m_path->getLink();
			}
		}

		$this->_set("Create new box page",$form->render(). $form2->render() . $out,'','');
		return TRUE;
	}
}	


?>
