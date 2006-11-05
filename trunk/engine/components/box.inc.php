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
*
*/
class xBoxType
{
	/**
	 * @var string
	 * @access public
	 */
	var $m_name;
	
	/**
	 * @var string
	 * @access public
	 */
	var $m_description;
	
	
	/**
	 * Contructor
	 */
	function xBoxType($name,$description)
	{
		$this->m_description = $description;
		$this->m_name = $name;
	}
	
	/**
	 * Delete this object from db
	 *
	 * @return bool FALSE on error
	 */
	function delete()
	{
		return  xBoxTypeDAO::delete($this);
	}
	
	/**
	 * Insert this object into db
	 *
	 * @return bool FALSE on error
	 */
	function insert()
	{
		return xBoxTypeDAO::insert($this);
	}
	
	/**
	 * Retrieve all box types from db
	 *
	 * @return array(xBox)
	 */
	function findAll()
	{
		return xBoxTypeDAO::findAll();
	}
};




/**
* Represent box visual element. The box id is a string.
* @abstract
*/
class xBox extends xElement
{
	/**
	 * @var string
	 * @access public
	 */
	var $m_name;
	
	/**
	 * The type of the box
	 *
	 * @var string
	 * @access public
	 */
	var $m_type;
	
	/**
	 * @var int
	 * @access public
	 */
	var $m_weight;
	
	/**
	 * @var xShowFilter
	 * @access public
	 */
	var $m_show_filter;
	
	
	/**
	* Contructor
	*/
	function xBox($name,$type,$weight,$show_filter)
	{
		$this->xElement();
		
		$this->m_weight = (int) $weight;
		$this->m_name = $name;
		$this->m_type = $type;
		$this->m_show_filter = $show_filter;
	}
	
	// DOCS INHERITHED  ========================================================
	function render()
	{
		//abstract method
		assert(FALSE);
	}
	
	/**
	 * Check if the box can be rendered.In particular checks show filters.
	 * If you override this method, please call xBox::onCheckPreconditions()
	 * before doing your checks.
	 *
	 * @return bool Boolean TRUE if the content can be created, FALSE otherwise.
	 */
	function onCheckPreconditions()
	{
		$path = xPath::getCurrent();
		return $this->m_show_filter->check($path);
	}
	
	/**
	 * Delete this object from db
	 *
	 * @return bool FALSE on error
	 */
	function delete()
	{
		return  xBoxDAO::delete($this);
	}
	
	/**
	 * Update
	 *
	 * @return bool FALSE on error
	 */
	function update()
	{
		return  xBoxDAO::update($this);
	}
	
	
	/**
	 * Return the builtin box relative to given name.
	 */
	function registerBoxTypeClass($type,$class_name)
	{
		global $xanth_builtin_boxes;
		$xanth_builtin_boxes[$type] = $class_name;
			
		return NULL;
	}
	
	
	/**
	 * Return the builtin box relative to given name.
	 */
	function getBoxTypeClass($type)
	{
		global $xanth_builtin_boxes;
		if(isset($xanth_builtin_boxes[$type]))
			return $xanth_builtin_boxes[$type];
			
		return NULL;
	}
	
	/**
	 *
	 */
	function findBoxGroups()
	{
		return xBoxGroupDAO::findBoxGroups($this->m_name);
	}
	
	
	/**
	 *
	 */
	function find($name = NULL,$type = NULL)
	{
		return xBoxDAO::find($name,$type);
	}
	
	/**
	 * @return array(xOperation)
	 */
	function getOperations()
	{
		return array
			(
				new xOperation('edit','Edit properties',''),
				new xOperation('delete','Delete','')
			);
	}
};
	


/**
* Represent an internationalized box.
* @abstract
*/
class xBoxI18N extends xBox
{
	/**
	 * @var string
	 * @access public
	 */
	var $m_title;
	
	/**
	 * The type of the box
	 *
	 * @var string
	 * @access public
	 */
	var $m_lang;
	
	/**
	 * Contructor
	 */
	function xBoxI18N($name,$type,$weight,$show_filter,$title,$lang)
	{
		$this->xBox($name,$type,$weight,$show_filter);
		
		$this->m_title = $title;
		$this->m_lang = $lang;
	}

	/**
	 * Fetch a specific box object given the name and type.
	 *
	 * @return xBox A specific xBox child object or NULL if not found.
	 * @static
	 */
	function fetchBox($boxname,$type,$lang,$flexible_lang = TRUE)
	{
		$class_name = xBox::getBoxTypeClass($type);
		if($class_name === NULL)
		{
			xLog::log(LOG_LEVEL_ERROR,'Cannot retrieve box type class name: "'.$type.'"',__FILE__,__LINE__);
			return NULL;
		}
		
		return reset(call_user_func(array( $class_name,'find'),$boxname,$type,$lang,$flexible_lang));
	}
	
	
	/**
	 *
	 */
	function insert()
	{
		return xBoxI18NDAO::insert($this);
	}
	
	/**
	 *
	 */
	function existsTranslation($name,$lang)
	{
		return xBoxI18NDAO::existsTranslation($name,$lang);
	}
	
	/**
	 * Retrieve all boxes from db
	 *
	 * @return array(xBoxI18N)
	 */
	function find($name = NULL,$type = NULL,$lang = NULL,$flexible_lang = true)
	{
		return xBoxI18NDAO::find($name,$type,$lang,$flexible_lang);
	}
	
	
	/**
	 * @return array(xOperation)
	 */
	function getOperations()
	{
		$prev = xBox::getOperations();
		return array_merge($prev,
			array
			(
				new xOperation('edit_translation','Edit translation',''),
				new xOperation('delete_translation','Delete translation','')
			)
		);
	}
};

	
	
	

/**
 * Represent a custom user box.
 */
class xBoxCustom extends xBoxI18N
{
	/**
	 * @var string
	 * @access public
	 */
	var $m_content;
	
	/**
	 * @var string
	 * @access public
	 */
	var $m_content_filter;

	/**
	* Contructor
	*
	* @param string $name
	* @param string $title
	* @param string $type
	* @param string $content
	* @param string $content_filter
	* @param string $area
	*/
	function xBoxCustom($name,$type,$weight,$show_filter,$title,$lang,$content,$content_filter)
	{
		xBoxI18N::xBoxI18N($name,$type,$weight,$show_filter,$title,$lang);
		
		$this->m_content = $content;
		$this->m_content_filter = $content_filter;
	}
	
	
	// DOCS INHERITHED  ========================================================
	function render()
	{
		$error = '';
		$title = xContentFilterController::applyFilter('notags',$this->m_title,$error);
		$content = xContentFilterController::applyFilter($this->m_content_filter,$this->m_content,$error);
		
		return xTheme::render('renderBox',array($this->m_name,$this->m_title,$this->m_content));
	}
	
	/**
	 * Insert this object into db
	 *
	 * @return bool FALSE on error
	 */
	function insert()
	{
		return xBoxCustomDAO::insert($this);
	}
	
	/**
	 * Insert as a new box translation
	 *
	 * @return bool FALSE on error
	 */
	function insertTranslation()
	{
		return xBoxCustomDAO::insertTranslation($this);
	}
	
	
	/**
	 * Delete this object from db
	 *
	 * @return bool FALSE on error
	 */
	function deleteTranslation()
	{
		return xBoxCustomDAO::deleteTranslation($this->m_name,$this->m_lang);
	}
	
	/**
	 * Retrieve all boxes from db
	 *
	 * @return array(xBoxI18N)
	 */
	function find($name = NULL,$type = 'custom',$lang = NULL,$flexible_lang = true)
	{
		return xBoxCustomDAO::find($name,$type,$lang,$flexible_lang);
	}
};
xBox::registerBoxTypeClass('custom','xBoxCustom');


/**
 * Represent a dynamic. A dynamic box is generated dynamically from a module.
 * @abstract
 */
class xBoxBuiltin extends xBoxI18N
{
	/**
	 * Contructor
	 */
	function xBoxBuiltin($name,$type,$weight,$show_filter,$title,$lang,$content,$content_filter)
	{
		xBoxI18N::xBoxI18N($name,$type,$weight,$show_filter,$title,$lang,$content,$content_filter);
	}
	
	
	/**
	 * Retrieve all boxes from db
	 *
	 * @return array(xBoxI18N)
	 */
	function find($name = NULL,$type = 'builtin',$lang = NULL,$flexible_lang = true)
	{
		$boxes = xBoxI18N::find($name,$type,$lang,$flexible_lang);
		$ret = array();
		foreach($boxes as $box)
			$ret[] = callWithSingleResult2('xm_fetchBuiltinBox',$name,$lang,$flexible_lang);
		
		return ret;
	}
};
xBox::registerBoxTypeClass('builtin','xBoxBuiltin');



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
			return new xResult(new xPageContentBoxAdmin($path));
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
		return new xResult($descrs);
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
			
			$out .= '</td><td>';
			$ops = call_user_func(array(xBox::getBoxTypeClass($box->m_type),'getOperations'));
			foreach($ops as $op)
			{
				$out .= '<a href="'.$op->getLink('box',$box->m_type,$box->m_name,$this->m_path->m_lang).
					'">'.$op->m_name.'</a> - ';
			}
			$out .= '</td></tr>';
		}
		
		$out  .= "</table></div>\n";
		
		xPageContent::_set("Admin box",$out,'','');
		return true;
	}
}



/**
 *
 */
class xPageContentBoxEditTranslation extends xPageContent
{
	var $m_box = NULL;
	
	function xPageContentBoxEditTranslation($path)
	{
		xPageContent::xPageContent($path);
	}
	
	/**
	 * Checks action permission, Box existence, box type, load and check box existence.
	 */
	function onCheckPreconditions()
	{
		//check action permission
		if(!xAccessPermission::checkCurrentUserPermission('box',$this->m_path->m_type,NULL,'edit_translation'))
			return new xPageContentNotAuthorized($this->m_path);
		
		//check box translation existence
		if(! xBoxI18N::existsTranslation($this->m_path->m_id,$this->m_path->m_lang))
			return new xPageContentError($this->m_path,'A translation of this node does not exists');
		
		//load and check box type existence
		$class_name = xBox::getBoxTypeClass($this->m_path->m_type);
		assert($class_name !== NULL);
		$this->m_box = reset(call_user_func(array( $class_name,'find'),$this->m_path->m_id,$this->m_path->m_type,
			$this->m_path->m_lang,true));
		if($this->m_box === NULL)
			return new xPageContentError($this->m_path,'Box does not exists');
			
		return true;
	}
	
	
	// DOCS INHERITHED  ========================================================
	function onCreate()
	{
		return new xPageContentNotFound($this->m_path);
	}
}



/**
 *
 */
class xPageContentBoxTranslate extends xPageContent
{
	var $m_box = NULL;
	
	function xPageContentBoxTranslate($path)
	{
		xPageContent::xPageContent($path);
	}
	
	/**
	 * Checks action permission, Box existence, box type, load and check box existence.
	 */
	function onCheckPreconditions()
	{
		//check action permission
		if(!xAccessPermission::checkCurrentUserPermission('box',$this->m_path->m_type,NULL,'translate'))
			return new xPageContentNotAuthorized($this->m_path);
		
		//check box translation existence
		if(xBoxI18N::existsTranslation($this->m_path->m_id,$this->m_path->m_lang))
			return new xPageContentError($this->m_path,'A translation of this node already exists');
		
		//load and check box type existence
		$class_name = xBox::getBoxTypeClass($this->m_path->m_type);
		assert($class_name !== NULL);
		$this->m_box = reset(call_user_func(array( $class_name,'find'),$this->m_path->m_id,$this->m_path->m_type,
			$this->m_path->m_lang,true));
		if($this->m_box === NULL)
			return new xPageContentError($this->m_path,'Box does not exists');
			
		return true;
	}
	
	// DOCS INHERITHED  ========================================================
	function onCreate()
	{
		return new xPageContentNotFound($this->m_path);
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
		$form = new xForm('box_create',$this->m_path->getLink());
		
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
					xNotifications::add(NOTIFICATION_NOTICE,'New box successfully created');
				else
					xNotifications::add(NOTIFICATION_ERROR,'Error: box was not created');
				
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
		$form = new xForm('box_edit',$this->m_path->getLink());
		
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



/**
 * A module for box
 */
class xModuleBoxCustom extends xModule
{
	function xModuleBoxCustom()
	{
		xModule::xModule();
	}


	/**
	 * @see xDummyModule::xm_fetchContent()
	 */ 
	function xm_fetchContent($path)
	{
		if($path->m_resource === 'box' && $path->m_action === 'create' && $path->m_type === 'custom')
		{
			return new xResult(new xPageContentAdminBoxCreateCustom($path));
		}
		
		return NULL;
	}
};

xModule::registerDefaultModule(new xModuleBoxCustom());





/**
 *
 */
class xPageContentAdminBoxCreateCustom extends xPageContent
{

	function xPageContentAdminBoxCreateCustom($path)
	{
		xPageContent::xPageContent($path);
	}
	
	// DOCS INHERITHED  ========================================================
	function onCheckPreconditions()
	{
		if(!xAccessPermission::checkCurrentUserPermission('admin/box',$this->m_path->m_type,NULL,'create'))
			return new xPageContentNotAuthorized($this->m_path);
			
		return TRUE;
	}
	
	// DOCS INHERITHED  ========================================================
	function onCreate()
	{
		//create form
		$form = new xForm('create_box_custom',xanth_relative_path($this->m_path->m_full_path));
		
		//box name
		$form->m_elements[] = new xFormElementTextField('name','Name','','',true,new xInputValidatorTextNameId(32));
		
		//box title
		$form->m_elements[] = new xFormElementTextField('title','Title','','',true,new xInputValidatorText(128));
		
		//box content
		$form->m_elements[] = new xFormElementTextArea('content','Content','','',true,
			new xDynamicInputValidatorApplyContentFilter(0,'filter'));
			
			
		//box content filter
		$filters = xContentFilterController::getCurrentUserAvailableFilters();
		$content_filter_radio_group = new xFormRadioGroup('Content filter');
		foreach($filters as $filter)
		{
			$content_filter_radio_group->m_elements[] = new xFormElementRadio('filter',$filter['name'],
				$filter['description'],$filter['name'],false,TRUE,new xInputValidatorContentFilter(64));
		}
		$form->m_elements[] = $content_filter_radio_group;
		
		//show filter type
		$show_filter_radio = new xFormRadioGroup('Show filter type');
		$show_filter_radio->m_elements[] = new xFormElementRadio('show_filter_type','Inclusive filter',
				'',XANTH_SHOW_FILTER_INCLUSIVE,false,TRUE,new xInputValidatorInteger(1,3));
		$show_filter_radio->m_elements[] = new xFormElementRadio('show_filter_type','Exclusive filter',
				'',XANTH_SHOW_FILTER_EXCLUSIVE,false,TRUE,new xInputValidatorInteger(1,3));
		$show_filter_radio->m_elements[] = new xFormElementRadio('show_filter_type','PHP filter',
				'',XANTH_SHOW_FILTER_PHP,false,TRUE,new xInputValidatorInteger(1,3));
		$form->m_elements[] = $show_filter_radio;
		
		//show filter
		$form->m_elements[] = new xFormElementTextArea('show_filter','Show filter','','',false,
			new xInputValidatorText());
		
		//todo weight
		
		//submit buttom
		$form->m_elements[] = new xFormSubmit('submit','Create');
		
		$ret = $form->validate();
		if(! $ret->isEmpty())
		{
			if(empty($ret->m_errors))
			{
				$node = new xBoxCustom($ret->m_valid_data['name'],'custom',0,
					new xShowFilter($ret->m_valid_data['show_filter_type'],$ret->m_valid_data['show_filter']),
					$ret->m_valid_data['title'],$this->m_path->m_lang,
					$ret->m_valid_data['content'],$ret->m_valid_data['filter']);
				
				if($node->insert())
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
}


?>
