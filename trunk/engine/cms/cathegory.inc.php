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
 * An items cathegory.
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
	 * @var string
	 * @access public
	 */
	var $m_parent_cathegory;
	
	/**
	 *
	 */
	function xCathegory($id,$name,$type,$description,$parent_cathegory)
	{
		$this->xElement();
		
		$this->m_id = $id;
		$this->m_type = $type;
		$this->m_name = $name;
		$this->m_description = $description;
		$this->m_parent_cathegory = $parent_cathegory;
	}
	
	
	// DOCS INHERITHED  ========================================================
	function onRender()
	{
		
	}
	
	
	/** 
	 * Inserts this into db
	 *
	 * @return bool FALSE on error
	 */
	function dbInsert()
	{
		return xCathegoryDAO::insert($this);
	}
	
	/** 
	 * Delete this cathegory from db
	 *
	 * @return bool FALSE on error
	 */
	function dbDelete()
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
	function dbDeleteById($catid)
	{
		return xCathegoryDAO::delete($catid);
	}
	
	/**
	 * Update this in db
	 *
	 * @return bool FALSE on error
	 */
	function dbUpdate()
	{
		return xCathegoryDAO::update($this);
	}
	
	/**
	 * Retrieve a specific cathegory from db
	 *
	 * @return xCathegory
	 * @static
	 */
	function dbLoad($id)
	{
		return xCathegoryDAO::load($id);
	}
	
	/**
	 * Retrieves all cathegories.
	 *
	 * @return array(xCathegory)
	 * @static
	 */
	function findAll()
	{
		return xCathegoryDAO::findAll();
	}
	
	/**
	 * Check if a cathegory supports an item type
	 *
	 * @return bool
	 * @static
	 */
	function cathegorySupportItemType($catid,$item_type)
	{
		return xCathegoryDAO:: cathegorySupportItemType($catid,$item_type);
	}
	
	/**
	 * Retrieves all cathegories that supports a specific item type
	 *
	 * @return array(xCathegory)
	 * @static
	 */
	function findBySupportedItemType($item_type)
	{
		return xCathegoryDAO::findBySupportedItemType($item_type);
	}
	
	
	/**
	 * Return a form element for asking for name input
	 *
	 * @param string $var_name The name of the form element
	 * @param string $value
	 * @param bool $mandatory True if this input is manadtory
	 * @return xFormElement
	 * @static
	 */
	function getFormCathegoryChooser($var_name,$label,$description,$value,$multiple,$mandatory,$item_type = NULL)
	{
		if($item_type == NULL)
		{
			$cathegories = xCathegory::findAll();
		}
		else
		{
			$cathegories = xCathegory::findBySupportedItemType($item_type);
		}
		
		$options = array();
		if(!$mandatory)
		{
			$options[''] = 0;
		}
		foreach($cathegories as $cathegory)
		{
			$options[$cathegory->m_name] = $cathegory->m_id;
		}
		
		return new xFormElementOptions($var_name,$label,$description,$value,$options,$multiple,$mandatory,
			new xInputValidatorInteger());
	}
	
	
	/**
	 * Return a form element for asking for name input
	 *
	 * @param string $var_name The name of the form element
	 * @param string $value
	 * @param bool $mandatory True if this input is manadtory
	 * @return xFormElement
	 * @static
	 */
	function getFormNameInput($var_name,$label,$description,$value,$mandatory)
	{
		return new xFormElementTextField($var_name,$label,$description,$value,$mandatory,new xInputValidatorText(32));
	}
	
	/**
	 * Return a form element for asking for Description input
	 *
	 * @param string $var_name The name of the form element
	 * @param string $value
	 * @param bool $mandatory True if this input is manadtory
	 * @return xFormElement
	 * @static
	 */
	function getFormDescriptionInput($var_name,$label,$description,$value,$mandatory)
	{
		return new xFormElementTextField($var_name,$label,$description,$value,$mandatory,new xInputValidatorText(32));
	}
};



/**
 * Root class of a hieararchy that manage view,creation,deletion,modification of cathegory types.
 */
class xCathegoryManager
{
	function xCathegoryManager()
	{}
	
	
	/**
	 * Register a cathegory manager as the object in charge of managing create,view,edit,permission, 
	 * delete of a specific cathegory type.
	 *
	 * @param xCathegoryManager $manager
	 * @param string $cattype
	 * @static
	 */
	function registerCathegoryManager($manager,$cattype)
	{
		global $g_xanth_cathegory_managers;
		
		$g_xanth_cathegory_managers[$cattype] = $manager;
	}
	
	
	/**
	 * Gets the object in charge of managing create,view,edit,permission, delete of a specific 
	 * item type.
	 *
	 * @param string $cattype
	 * @return string
	 * @static
	 */
	function getCathegoryManager($cattype)
	{
		global $g_xanth_cathegory_managers;
		
		if(isset($g_xanth_cathegory_managers[$cattype]))
		{
			return $g_xanth_cathegory_managers[$cattype];
		}
		
		return new xCathegoryManager();
	}
	
	
	
	/**
	 * Retrieve a specific cathegory from db.
	 *
	 * @return xCathegory
	 * @static
	 */
	function dbLoad($id)
	{
		return xCathegory::dbLoad($id);
	}
	
	/**
	 * Create content to fill the cathegory/create page. You need to fill the provided $content
	 * object with your data.
	 *
	 * @param xXanthPath $path
	 * @param xContentItem $content
	 * @return bool True if the creation of content succeeded
	 * @todo filter types and cathegories.
	 */
	function onContentCreate($path,&$content)
	{
		//check for type
		$type = $path->m_vars['type'];
		
		//check for parent
		$parentcat = NULL;
		if(isset($path->m_vars['parentcat']))
			$parentcat = $path->m_vars['parentcat'];
			
		//create form
		$form = new xForm('?p=' . $path->m_full_path);
		
		//name
		$form->m_elements[] = xCathegory::getFormNameInput('name','Name','','',TRUE);
		//description
		$form->m_elements[] = xCathegory::getFormDescriptionInput('description','Description','','',FALSE);
		
		if($parentcat === NULL)
		{
			//parent cathegory
			$form->m_elements[] = xCathegory::getFormCathegoryChooser('parent','Parent Cathegory','','',FALSE,FALSE);
		}
		
		//submit buttom
		$form->m_elements[] = new xFormSubmit('submit','Create');
		
		$ret = $form->validate();
		if(isset($ret->m_valid_data['submit']))
		{
			if(empty($ret->m_errors))
			{
				if($parentcat === NULL)
				{
					$parentcat = $ret->m_valid_data['parent'];
				}
				
				
				$cat = new xCathegory(0,$ret->m_valid_data['name'],$type,$ret->m_valid_data['description'],
					$parentcat);
					
				if($cat->dbInsert())
				{
					xNotifications::add(NOTIFICATION_NOTICE,'New cathegory successfully created');
				}
				else
				{
					xNotifications::add(NOTIFICATION_ERROR,'Error: Cathegory was not created');
				}
				
				
				$content->_set("Create cathegory",'New cathegory was created with id: ','','');
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

		$content->_set("Create new cathegory",$form->render(),'','');
		return TRUE;
	}
	
	/**
	 * Check permission for creating a cathegory. 
	 *
	 * @param xXanthPath $path
	 * @return bool
	 */
	function onContentCheckPermissionCreate($path)
	{
		//check for type
		if(isset($path->m_vars['type']))
		{
			$type = $path->m_vars['type'];
			
			if(!xAccessPermission::checkCurrentUserPermission('cathegory','create',$type))
			{
				return FALSE;
			}
		}
		
		//check for create inside
		if(isset($path->m_vars['parentcat']))
		{
			$parentcat = $path->m_vars['parentcat'];
			
			if(!xAccessPermission::checkCurrentUserPermission('cathegory','create_inside',$parentcat))
			{
				return FALSE;
			}
		}
		
		return TRUE;
	}
	
	/**
	 * Create content to fill the cathegory/view page. You need to fill the provided $content
	 * object with your data.
	 *
	 * @param xXanthPath $path
	 * @param xContentItem $content
	 * @param xCathegory $cathegory The item to display
	 * @return bool True if the creation of content succeeded
	 */
	function onContentView($path,$cathegory,&$content)
	{
		$content->_set('','','','');
		return TRUE;
	}
	
	/**
	 * Check permission for viewing an item. 
	 *
	 * @param xCathegory $cathegory
	 * @return bool
	 */
	function onContentCheckPermissionView($path,$cathegory)
	{
		return xAccessPermission::checkCurrentUserPermission('cathegory','view',$cathegory->m_type);
	}

}




?>
