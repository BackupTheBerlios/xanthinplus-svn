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


$g_xanth_cathegory_managers = array();


/**
 * A cathegory.
 */
class xCathegory extends xElement
{
	/**
	 * @var int
	 * @access public
	 */
	var $m_id;
	
	/**
	 * @var string
	 * @access public
	 */
	var $m_type;
	
	/**
	 * @var int
	 * @access public
	 */
	var $m_parent_cathegory;
	
	/**
	 *
	 */
	function xCathegory($id,$type,$parent_cathegory)
	{
		$this->xElement();
		
		$this->m_id = (int) $id;
		$this->m_type = $type;
		$this->m_parent_cathegory = $parent_cathegory;
	}
	
	/** 
	 * Delete this cathegory from db
	 *
	 * @return bool FALSE on error
	 */
	function delete()
	{
		return xCathegoryDAO::delete($this->m_id);
	}
	
	/** 
	 * Delete a cathegory from db using its id
	 *
	 * @param int $catid
	 * @return bool FALSE on error
	 * @static
	 */
	function deleteById($catid)
	{
		return xCathegoryDAO::delete($catid);
	}
	
	/**
	 * Retrieve a specific cathegory from db
	 *
	 * @return xCathegory
	 * @static
	 */
	function load($id)
	{
		return xCathegoryDAO::load($id);
	}
	
	/**
	 * Retrieves generic xCathegory objects by different search parameters
	 *
	 * @return array(xCathegory)
	 * @static
	 */
	function find($type = NULL,$parent_cathegory = NULL)
	{
		return xCathegoryDAO::find($type,$parent_cathegory);
	}
	
	/**
	 * Check recursively an access permission relative to this cathegory
	 *
	 * @return bool
	 * @static
	 */
	function checkCurrentUserPermissionRecursive($action)
	{
		//first check permission in this cathegory
		if(!xAccessPermission::checkCurrentUserPermission('cathegory',NULL,$this->m_id,'view'))
			return FALSE;
		
		//now load parent and check parent
		if($this->m_parent_cathegory != NULL)
		{
			$parent = xCathegory::load($this->m_parent_cathegory);
			if(! $parent->checkCurrentUserPermissionRecursive($action))
				return false;
		}
		
		return true;
	}
	
	
	/**
	 * @static
	 */
	function registerCathegoryTypeClass($cat_type,$class_name)
	{
		global $xanth_cathegory_type_classes;
		$xanth_cathegory_type_classes[$cat_type] = $class_name;
	}
	
	
	/**
	 * @static
	 */
	function getCathegoryTypeClass($cat_type)
	{
		global $xanth_cathegory_type_classes;
		if(isset($xanth_cathegory_type_classes[$cat_type]))
			return $xanth_cathegory_type_classes[$cat_type];
		
		return NULL;
	}
	
	/**
	 * @abstract
	 * @return array(xOperation)
	 */
	function getOperations()
	{
		assert(false);
	}
};



/**
 * An internationalized cathegory.
 */
class xCathegoryI18N extends xCathegory
{
	var $m_lang;
	
	/**
	 * @var string
	 * @access public
	 */
	var $m_title;
	
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
	 *
	 */
	function xCathegoryI18N($id,$type,$parent_cathegory,$name,$title,$description,$lang)
	{
		$this->xCathegory($id,$type,$parent_cathegory);
		
		$this->m_name = $name;
		$this->m_lang = $lang;
		$this->m_title = $title;
		$this->m_description = $description;
	}
	
	
	// DOCS INHERITHED  ========================================================
	function render()
	{
		$error = '';
		$description = xContentFilterController::applyFilter('html',$this->m_description,$error);
		$title = xContentFilterController::applyFilter('notags',$this->m_title,$error);
		
		$nodes = call_user_func(
			array(xNode::getNodeTypeClass($this->m_type), 'find'),NULL,$this->m_id,NULL,$this->m_lang);
		
		//format operations
		$ops = $this->getOperations();
		$formatted = array();
		foreach($ops as $op)
			$formatted[$op->m_name] = array('link' => $op->getLink('cathegory',$this->m_type,$this->m_id,$this->m_lang),
				'description' => $op->m_description);
		
		$operations = xTheme::render1('renderCathegoryOperations',$formatted);
		
		return xTheme::render4('renderCathegory',$title,$description,$nodes,$operations);
	}
	
	/** 
	 * Inserts this into db
	 *
	 * @return bool FALSE on error
	 */
	function insert()
	{
		return xCathegoryI18NDAO::insert($this);
	}
	
	/**
	 * Update this in db
	 *
	 * @return bool FALSE on error
	 */
	function update()
	{
		return xCathegoryI18NDAO::update($this);
	}
	
	
		/** 
	 * Inserts this into db
	 *
	 * @return bool FALSE on error
	 */
	function insertTranslation()
	{
		return xCathegoryI18NDAO::insertTranslation($this);
	}
	
	
	/** 
	 * Delete this cathegory from db
	 *
	 * @return bool FALSE on error
	 */
	function deleteTranslation()
	{
		return xCathegoryI18NDAO::deleteTranslation($this->m_id,$this->m_lang);
	}
	
	
	/**
	 * Retrieve a specific cathegory from db
	 *
	 * @return xCathegory
	 * @static
	 */
	function load($id,$lang)
	{
		if(is_numeric($id))
		{
			return xCathegoryI18NDAO::load((int) $id,$lang);
		}
		
		return xCathegoryI18NDAO::loadByName($id,$lang);
	}	
	
	/**
	 * Retrieves generic xCathegory objects by different search parameters
	 *
	 * @return array(xCathegory)
	 * @static
	 */
	function find($type = NULL,$parent_cathegory = NULL,$name = NULL,$lang = NULL)
	{
		return xCathegoryI18NDAO::find($type,$parent_cathegory,$name,$lang);
	}
	
	/**
	 * @return array(xOperation)
	 */
	function getOperations()
	{
		return array
			(
				new xOperation('edit_translation','Edit translation',''),
				new xOperation('delete_translation','Delete translation',''),
				new xOperation('delete_cathegory','Delete cathegory','')
			);
	}
};


/**
 *
 */
class xCathegoryPage extends xCathegoryI18N
{
	/**
	 *
	 */
	function xCathegoryPage($id,$type,$parent_cathegory,$name,$title,$description,$lang)
	{
		$this->xCathegoryI18N($id,$type,$parent_cathegory);
	}
	
	/**
	 * Retrieves generic xCathegory objects by different search parameters
	 *
	 * @return array(xCathegory)
	 * @static
	 */
	function find($parent_cathegory = NULL,$name = NULL,$lang = NULL)
	{
		return xCathegoryI18NDAO::find('page',$parent_cathegory,$name,$lang);
	}
};
xCathegory::registerCathegoryTypeClass('page','xCathegoryPage');



/**
 * Checks for cathegory existence, for permissions and for type matching.
 */
class xCreateIntoCathegoryValidator extends xInputValidatorInteger
{
	var $m_type;
	
	function xCreateIntoCathegoryValidator($type)
	{
		xInputValidatorInteger::xInputValidatorInteger();
		$this->m_type = $type;
	}
	
	// DOCS INHERITHED  ========================================================
	function isValid($input)
	{
		if(empty($input))
			return true;
			
		if(!xInputValidatorInteger::isValid($input))
			return FALSE;
		
		$cathegory = xCathegory::load($input);
		if($cathegory === NULL)
		{
			echo "here";
			$this->m_last_error = 'Cathegory not found';
			return false;
		}
		if($cathegory->m_type != $this->m_type)
		{
			$this->m_last_error = 'Child and parent type does not match';
			return false;
		}
		if(! $cathegory->checkCurrentUserPermissionRecursive('create_inside'))
		{
			$this->m_last_error = 'You are not authorized to insert inside this cathegory';
			return false;
		}
		return true;
	}
}




/**
* Cathegory module
*/
class xModuleCathegory extends xModule
{
	function xModuleCathegory()
	{
		$this->xModule();
	}


	/**
	 * @see xDummyModule::xm_fetchContent()
	 */ 
	function xm_fetchContent($path)
	{
		if($path->m_resource === "cathegory" && $path->m_type === NULL && $path->m_action == 'admin')
		{
			return new xResult(new xContentCathegoryAdmin($path));
		}
		
		return NULL;
	}
	
	/**
	 * @see xDummyModule::xm_fetchPermissionDescriptors()
	 */ 
	function xm_fetchPermissionDescriptors()
	{
		$descrs = array();
		
		$types = xNodeType::findAll();
		foreach($types as $type)
		{
			$descrs[] = new xAccessPermissionDescriptor('cathegory',$type->m_name,NULL,'view',
				'View cathegory '.$type->m_name);
		}
		
		foreach($types as $type)
		{
			$descrs[] = new xAccessPermissionDescriptor('cathegory',$type->m_name,NULL,'create',
				'Create cathegory '.$type->m_name);
		}
		
		return new xResult($descrs);
	}
	
};

xModule::registerDefaultModule(new xModuleCathegory());


/**
 * 
 */
class xContentCathegoryView extends xContent
{	
	/**
	 * @var xCathegory
	 */
	var $m_cat;
	
	function xContentCathegoryView($path,$cat)
	{
		$this->xContent($path);
		$this->m_cat = $cat;
	}
	
	/**
	 * Checks that cat exists, checks cathegory and type view permission.
	 * If you inherit the xContentCathegoryView clas and override this member, remember
	 * to call the xContentCathegoryView::onCheckPreconditions() before your checks.
	 */
	function onCheckPreconditions()
	{
		assert($this->m_cat != NULL);
		
		//check type permission
		if(!xAccessPermission::checkCurrentUserPermission('cathegory',$this->m_cat->m_type,NULL,'view'))
			return new xContentNotAuthorized($this->m_path);
		
		if(!empty($this->m_cat->m_parent_cathegory))
		{
			$cathegory = xCathegory::load($this->m_cat->m_parent_cathegory);
			if(! $cathegory->checkCurrentUserPermissionRecursive('view'))
					return new xContentNotAuthorized($this->m_path);
		}
		
		return TRUE;
	}
	
	
	/**
	 * Do nothing. Only asserts cat != NULL and returns true.
	 */
	function onCreate()
	{
		assert($this->m_cat != NULL);
		return true;
	}
}



/**
 *
 */
class xContentCathegoryAdmin extends xContent
{

	function xContentCathegoryAdmin($path)
	{
		xContent::xContent($path);
	}
	
	// DOCS INHERITHED  ========================================================
	function onCheckPreconditions()
	{
		return TRUE;
	}
	
	// DOCS INHERITHED  ========================================================
	function onCreate()
	{
		$types = xNodeType::findAll();
		
		$out = "Choose type:\n <ul>\n";
		foreach($types as $type)
		{
			$out .= "<li><a href=\"".xPath::renderLink($this->m_path->m_lang,'cathegory','admin',$type->m_name) 
				."\">" . $type->m_name . "</a></li>\n";
		}
		
		$out  .= "</ul>\n";
		
		xContent::_set("Admin cathegory: choose type",$out,'','');
		return true;
	}
}


/**
 * Base class for all cathegory creation pages
 */
class xContentCathegoryCreate extends xContent
{

	function xContentCathegoryCreate($path)
	{
		xContent::xContent($path);
	}
	
	/**
	 * Checks parent cathegory and type create permission.
	 * If you inherit the xContentCathegoryCreate class and override this member, remember
	 * to call the xContentNodeCreate::onCheckPreconditions() before your checks.
	 */
	function onCheckPreconditions()
	{
		if(!xAccessPermission::checkCurrentUserPermission('cathegory',$this->m_path->m_type,NULL,'create'))
			return new xContentNotAuthorized($this->m_path);
			
		$cathegory = NULL;
		if($this->m_path->m_id != NULL)
		{
			$cathegory = xCathegory::load($this->m_path->m_id);
			if($cathegory == NULL)
				return new xContentNotFound($this->m_path);
			
			//check for matching node type and cathegory type
			if($this->m_path->m_type !== $cathegory->m_type)
				return new xContentError($this->m_path,'Node type and parent cathegory type does not match');
			
			//check cathegories permission
			if(! $cathegory->checkCurrentUserPermissionRecursive('create_inside'))
				return new xContentNotAuthorized($this->m_path);
		}
		
		return TRUE;
	}
	
	/**
	 * @abstract
	 */
	function onCreate()
	{
		assert(false);
	}
}


/**
* Cathegory module
*/
class xModuleCathegoryPage extends xModule
{
	function xModuleCathegoryPage()
	{
		$this->xModule();
	}


	/**
	 * @see xDummyModule::xm_fetchContent()
	 */ 
	function xm_fetchContent($path)
	{
		if($path->m_resource === "cathegory" && $path->m_type === 'page' && $path->m_action == 'admin')
		{
			return new xResult(new xContentCathegoryAdminPage($path));
		}
		elseif($path->m_resource === "cathegory" && $path->m_type == 'page' && $path->m_action == 'create')
		{
			return new xResult(new xContentCathegoryCreatePage($path));
		}
		elseif($path->m_resource === "cathegory" && $path->m_type == 'page' && $path->m_action == 'view')
		{
			$cat = xCathegoryPage::load($path->m_id,$path->m_lang);
			if($cat === NULL)
			{
				return new xResult(new xContentNotFound($path));
			}
			return new xResult(new xContentCathegoryViewPage($path,$cat));
		}
		return NULL;
	}
};

xModule::registerDefaultModule(new xModuleCathegoryPage());



/**
 * 
 */
class xContentCathegoryViewPage extends xContentCathegoryView
{	
	function xContentCathegoryViewPage($path,$cat)
	{
		xContentCathegoryView::xContentCathegoryView($path,$cat);
	}
	
	
	/**
	 * Fill this object with node properties by calling xNode->render(). Only metadata are not filled-id, 
	 * so override this funciton in your node type implementation.
	 */
	function onCreate()
	{
		$res = xContentCathegoryView::onCreate();
		if($res !== TRUE)
			return $res;
		
		$error = '';
		$title = xContentFilterController::applyFilter('notags',$this->m_cat->m_title,$error);
		
		xContent::_set($title,$this->m_cat->render(),'','');
		
		return true;
	}
}




/**
 * 
 */
class xContentCathegoryAdminPage extends xContent
{	
	function xContentCathegoryAdminPage($path)
	{
		$this->xContent($path);
	}
	
	/**
	 * Check node admin type permission
	 */
	function onCheckPreconditions()
	{
		if(!xAccessPermission::checkCurrentUserPermission('cathegory',$this->m_path->m_type,NULL,'admin'))
			return new xContentNotAuthorized($this->m_path);
		
		return true;
	}
	
	/**
	 * @access private
	 */
	function _groupCathegory($cats)
	{
		$out = array();
		foreach($cats as $cat)
		{
			$out[$cat->m_id][$cat->m_lang] = $cat;
		}
		
		return $out;
	}
	
	/**
	 * 
	 */
	function onCreate()
	{
		$cats = xCathegoryI18N::find(NULL);
		$cats = $this->_groupCathegory($cats);
		$out = '<a href="'.xPath::renderLink($this->m_path->m_lang,'cathegory','create','page').
			'">Create new cathegory page</a><br/><br/>';
		$out .= "<div class = 'admin'><table>\n";
		$out .= "<tr><th>ID</th><th>Title</th><th>Parent</th><th>In your lang?</th><th>Translated in</th><th>Translate in</th></tr>\n";
		$langs = xLanguage::findNames();
		foreach($cats as $id => $cat_array)
		{
			$cat = NULL;
			
			if(isset($cat_array[$this->m_path->m_lang])) 				//select current language node
			{
				$cat = $cat_array[$this->m_path->m_lang];
			}
			elseif(isset($cat_array[xSettings::get('default_lang')]))	//select default language node
			{
				$cat = $cat_array[xSettings::get('default_lang')];
			}
			else														//select first found language node
			{
				$cat = reset($cat_array);
			}
			$error = '';
			$out .= '<tr><td>'.$id.'</td><td><a href="'.
				xPath::renderLink($cat->m_lang,'cathegory','view',$cat->m_type,$cat->m_id) . '">'.
				xContentFilterController::applyFilter('notags',$cat->m_title,$error) . '</a></td>
				<td>'.$cat->m_parent_cathegory.'</td><td>';
				
			if($cat->m_lang == $this->m_path->m_lang)
				$out .= 'Yes';
			else			
				$out .= 'No';
			
			$out .= '</td><td>';
			foreach($cat_array as $lang => $ignore)
			{
				$out .= $lang . '  ';
			}
			$out .= '</td><td>';
			
			
			foreach($langs as $lang)
			{
				if(!array_key_exists($lang, $cat_array))
				{
					$out .= '<a href="'. 
						xPath::renderLink($lang,'cathegory','translate',$cat->m_type,$id) . 
						'">' . $lang . '</a>';
				}
			}
			$out .= '</td></tr>';
		}
		
		$out  .= "</table></div>\n";
		
		xContent::_set("Admin cathegory page",$out,'','');
		return true;
	}
}




/**
 *
 */
class xContentCathegoryCreatePage extends xContentCathegoryCreate
{

	function xContentCathegoryCreatePage($path)
	{
		xContentCathegoryCreate::xContentCathegoryCreate($path);
	}
	
	// DOCS INHERITHED  ========================================================
	function onCreate()
	{
		//create form
		$form = new xForm('create_page_cathegory',xanth_relative_path($this->m_path->m_full_path));
		
		//no cathegory in path so let user choose according to its permissions
		if($this->m_path->m_id == NULL)
		{
			$cathegories = xCathegoryI18N::find($this->m_path->m_type);
			
			$options = array();
			foreach($cathegories as $cathegory)
			{
				$options[$cathegory->m_name] = $cathegory->m_id;
			}
			
			$form->m_elements[] = new xFormElementOptions('parent_cathegory','Parent cathegory','','',$options,FALSE,
				TRUE,new xCreateIntoCathegoryValidator($this->m_path->m_type));
		}
		
		//cat name
		$form->m_elements[] = new xFormElementTextField('name','Unique Name','','',true,new xInputValidatorTextNameId(32));
		
		//cat title
		$form->m_elements[] = new xFormElementTextField('title','Title','','',true,new xInputValidatorText(128));
		
		//cat description
		$form->m_elements[] = new xFormElementTextArea('description','Description','','',false,
			new xInputValidatorText());
			
		//submit buttom
		$form->m_elements[] = new xFormSubmit('submit','Create');
		
		$ret = $form->validate();
		if(! $ret->isEmpty())
		{
			if(empty($ret->m_errors))
			{
				$cathegory = array();
				if($this->m_path->m_id != NULL)
					$cathegory = $this->m_path->m_id;
				else
					$cathegory = $ret->m_valid_data['parent_cathegory'];
					
				$cat = new xCathegoryI18N(-1,$this->m_path->m_type,$cathegory,$ret->m_valid_data['name'],
					$ret->m_valid_data['title'],$ret->m_valid_data['description'],$this->m_path->m_lang);
				
				if($cat->insert())
				{
					xNotifications::add(NOTIFICATION_NOTICE,'New cathegory successfully created');
				}
				else
				{
					xNotifications::add(NOTIFICATION_ERROR,'Error: cathegory was not created');
				}
				
				$this->_set("Create new cathegory page",'','','');
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

		$this->_set("Create new cathegory page",$form->render(),'','');
		return TRUE;
	}
}
?>