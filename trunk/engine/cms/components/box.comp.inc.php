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
 * A module for box
 */
class xModuleBox extends xModule
{
	function xModuleBox()
	{
		xModule::xModule();
	}


	/**
	 * @see xDummyModule::xm_fetchContent()
	 */ 
	function xm_fetchContent($path)
	{
		if($path->m_resource === 'box' && $path->m_action === 'admin' && $path->m_type === NULL)
		{
			return new xPageContentBoxAdmin($path);
		}
		
		return NULL;
	}
	
	/**
	 * @see xDummyModule::xm_fetchPermissionDescriptors()
	 */ 
	function xm_fetchPermissionDescriptors()
	{
		$descrs = array();
		$descr[] = new xAccessPermissionDescriptor('admin/box',NULL,NULL,'create','Create a custom box of any type');
		return $descrs;
	}
};

xModule::registerDefaultModule(new xModuleBox());




/**
 *
 */
class xPageContentBoxAdmin extends xPageContent
{

	function xPageContentBoxAdmin($path)
	{
		xPageContent::xPageContent($path);
	}
	
	// DOCS INHERITHED  ========================================================
	function onCheckPreconditions()
	{
		if(!xAccessPermission::checkCurrentUserPermission('box',NULL,NULL,'admin'))
			return new xPageContentNotAuthorized($this->m_path);
			
		return true;
	}
	
	
	/**
	 * @access private
	 */
	function _groupBoxes($boxes)
	{
		$out = array();
		foreach($boxes as $box)
		{
			$out[$box->m_name][$box->m_lang] = $box;
		}
		
		return $out;
	}
	
	
	// DOCS INHERITHED  ========================================================
	function onCreate()
	{
		$boxes = xBoxI18N::find();
		$boxes = $this->_groupBoxes($boxes);
		$out = '<a href="'.xPath::renderLink($this->m_path->m_lang,'box','create','menu').'">Create menu</a><br/><br/>';
		$out .= '<div class = "admin"><table>';
		$out .= '<tr><th>Name</th><th>Type</th><th>Title</th><th>Groups</th><th>In your lang?</th><th>Translated in</th><th>Translate in</th><th>Actions</th></tr>';
		$langs = xLanguage::findNames();
		foreach($boxes as $name => $box_array)
		{
			$box = NULL;
			
			if(isset($box_array[$this->m_path->m_lang])) 				//select current language node
			{
				$box = $box_array[$this->m_path->m_lang];
			}
			elseif(isset($box_array[xSettings::get('default_lang')]))	//select default language node
			{
				$box = $box_array[xSettings::get('default_lang')];
			}
			else														//select first found language node
			{
				$box = reset($box_array);
			}
			$error = '';
			$out .= '<tr><td>'.$name.'</td><td>'.$box->m_type.'</td><td>' .
				xContentFilterController::applyFilter('notags',$box->m_title,$error) . '</td>
				<td>';
				
			$groups	 = $box->findBoxGroups();
			foreach($groups as $group)
			{
				$out .= $group->m_name . ' ';
			}
			
			$out .= '</td><td>';
				
			if($box->m_lang == $this->m_path->m_lang)
				$out .= 'Yes';
			else			
				$out .= 'No';
			
			$out .= '</td><td>';
			foreach($box_array as $lang => $ignore)
			{
				$out .= $lang . '  ';
			}
			$out .= '</td><td>';
			
			
			foreach($langs as $lang)
			{
				if(!array_key_exists($lang, $box_array))
				{
					$out .= '<a href="'. 
						xPath::renderLink($lang,'box','translate',$box->m_type,$name) . 
						'">' . $lang . '</a>';
				}
			}
			
			$out .= '<td>';
			$ops = call_user_func(array(xBox::getBoxTypeClass($box->m_type),'getOperations'));
			foreach($ops as $op)
			{
				$out .= '<a href="'.$op->getLink('box',$box->m_type,$box->m_name,$this->m_path->m_lang).
					'">'.$op->m_name.'</a> - ';
			}
			$out .= '</td>';
		}
		
		$out  .= "</table></div>\n";
		
		xPageContent::_set("Admin box",$out,'','');
		return true;
	}
}



/**
 * 
 */
class xPageContentBoxCreate extends xPageContent
{
	function xPageContentBoxCreate($path)
	{
		$this->xPageContent($path);
	}
	
	/**
	 * Checks action permission, Box existence, box type.
	 */
	function onCheckPreconditions()
	{
		//check action permission
		if(!xAccessPermission::checkCurrentUserPermission('box',$this->m_path->m_type,NULL,'create'))
			return new xPageContentNotAuthorized($this->m_path);
		
		return true;
	}
	
	
	/**
	 *
	 */
	function onCreate()
	{
		//create form
		$form = new xForm($this->m_path->getLink());
		
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
					xNotifications::add(NOTIFICATION_NOTICE,'New box successfully created');
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
				{
					xNotifications::add(NOTIFICATION_WARNING,$error);
				}
			}
		}

		$this->_set("Create new box page",$form->render(),'','');
		return TRUE;
	}
};



/**
 * 
 */
class xPageContentBoxEdit extends xPageContent
{
	var $m_box;
	
	function xPageContentBoxEdit($path)
	{
		$this->xPageContent($path);
	}
	
	/**
	 * Checks action permission, Box existence, box type.
	 */
	function onCheckPreconditions()
	{
		assert($this->m_path->m_id != NULL);
		
		//check action permission
		if(!xAccessPermission::checkCurrentUserPermission('box',$this->m_path->m_type,NULL,'edit'))
			return new xPageContentNotAuthorized($this->m_path);
		
		//check box existence
		$class_name = xBox::getBoxTypeClass($this->m_path->m_type);
		if(empty($class_name))
			return new xPageContentNotFound($this->m_path);
			
		if(($this->m_box = reset(call_user_func(array($class_name,'find'),$this->m_path->m_id))) === FALSE)
			return new xPageContentNotFound($this->m_path);
		
		return true;
		
	}
	
	
	/**
	 *
	 */
	function onCreate()
	{
		//create form
		$form = new xForm($this->m_path->getLink());
		
		//weight
		$options = array();
		for($i = -12;$i <= 12; $i++)
			$options[$i] = $i;
		$form->m_elements[] = new xFormElementOptions('weight','Weight','',$this->m_box->m_weight,$options,false,TRUE,
				new xInputValidatorInteger(-12,12));
		
		//show filter type
		$show_filter_radio = new xFormRadioGroup('Show filter type');
		$show_filter_radio->m_elements[] = new xFormElementRadio('show_filter_type','Exclusive filter',
				'',XANTH_SHOW_FILTER_EXCLUSIVE,$this->m_box->m_show_filter->m_type == XANTH_SHOW_FILTER_EXCLUSIVE,
				TRUE,new xInputValidatorInteger(1,3));
		$show_filter_radio->m_elements[] = new xFormElementRadio('show_filter_type','Inclusive filter',
				'',XANTH_SHOW_FILTER_INCLUSIVE,$this->m_box->m_show_filter->m_type == XANTH_SHOW_FILTER_INCLUSIVE,
				TRUE,new xInputValidatorInteger(1,3));
		$show_filter_radio->m_elements[] = new xFormElementRadio('show_filter_type','PHP filter',
				'',XANTH_SHOW_FILTER_PHP,$this->m_box->m_show_filter->m_type == XANTH_SHOW_FILTER_PHP,
				TRUE,new xInputValidatorInteger(1,3));
		$form->m_elements[] = $show_filter_radio;
		
		//show filter
		$form->m_elements[] = new xFormElementTextArea('show_filter','Show filter','',
			$this->m_box->m_show_filter->m_filters,false,new xInputValidatorText());
		
		//submit buttom
		$form->m_elements[] = new xFormSubmit('submit','Create');
		
		$ret = $form->validate();
		if(! $ret->isEmpty())
		{
			if(empty($ret->m_errors))
			{
				$box = new xBox($this->m_box->m_name,$this->m_box->m_type,$ret->m_valid_data['weight'],
					new xShowFilter($ret->m_valid_data['show_filter_type'],$ret->m_valid_data['show_filter']));
				
				if($box->update())
				{
					xNotifications::add(NOTIFICATION_NOTICE,'Box successfully updated');
				}
				else
				{
					xNotifications::add(NOTIFICATION_ERROR,'Error: box was not updated');
				}
				
				$this->_set("Edit box",'','','');
				return TRUE;
			}
			else
			{
				foreach($ret->m_errors as $error)
				{
					xNotifications::add(NOTIFICATION_WARNING,$error);
				}
			}
		}

		$this->_set("Edit box",$form->render(),'','');
		return TRUE;
	}
};



?>
