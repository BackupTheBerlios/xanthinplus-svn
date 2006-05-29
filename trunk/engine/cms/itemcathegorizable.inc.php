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
 * Represent an item that can be inserted in a cathagory
 */
class xItemCathegorizable extends xItem
{
	/**
	 * @var timestamp
	 * @access public
	 */
	var $m_cathegory;
	
	
	/**
	 *
	 */
	function xItemCathegorizable($id,$title,$type,$author,$content,$content_filter,$creation_time,$cathegory)
	{
		$this->xCathegory($id,$title,$type,$author,$content,$content_filter,$creation_time);
		
		$this->m_cathegory = $cathegory;
	}

	// DOCS INHERITHED  ========================================================
	function dbInsert()
	{
		$this->m_id = xItemCathegorizableDAO::insert($this);
		return $this->m_id;
	}
	
	// DOCS INHERITHED  ========================================================
	function dbLoad($id)
	{
		return xItemCathegorizableDAO::load($id);
	}
};


/**
 * Root class of a hieararchy that manage view,creation,deletion,modification of item types.
 */
class xItemCathegorizableManager extends xItemManager
{
	function xItemCathegorizableManager()
	{}
	
	// DOCS INHERITHED  ========================================================
	function dbLoad($id)
	{
		return xItemCathegorizable::dbLoad($id);
	}
	
	// DOCS INHERITHED  ========================================================
	function onContentCheckPermissionCreate($path)
	{
		if(! xItemManager::onContentCheckPermissionCreate($path))
			return FALSE;
		
		//check for cathegory permission
		if(isset($path->m_vars['cathegory']))
		{
			$cathegory = $path->m_vars['cathegory'];
			if(! xAccessPermission::checkCurrentUserPermission('cathegory','insert_item',$cathegory))
			{
				return FALSE;
			}
		}
		
		return TRUE;
	}
	
	
	// DOCS INHERITHED  ========================================================
	function onContentCreate($path,&$content)
	{
		//type
		$type = $path->m_vars['type'];
				
		//cathegory
		$cathegory = NULL;
		if(isset($path->m_vars['cathegory']))
			$cathegory = $path->m_vars['cathegory'];
				

		//create form
		$form = new xForm('?p=' . $path->m_full_path);
		
		if($cathegory === NULL)
		{
			//parent cathegory
			$form->m_elements[] = xCathegory::getFormCathegoryChooser('cathegory','Cathegory','','',FALSE,TRUE,$type);
		}
		
		//item title
		$form->m_elements[] = xItem::getFormTitleInput('title','',true);
		//item body
		$form->m_elements[] = xItem::getFormBodyInput('body','Body','','',true,
			new xInputValidatorDynamicContentFilter(0,'filter'));
		//item filter
		$form->m_elements[] = xContentFilterController::getFormContentFilterChooser('filter','html',TRUE);
		
		//submit buttom
		$form->m_elements[] = new xFormSubmit('submit','Create');
		
		$ret = $form->validate();
		if(! $ret->isEmpty())
		{
			if(empty($ret->m_errors))
			{
				if($cathegory === NULL)
				{
					if(! xCathegory::cathegorySupportItemType($path->m_vars['cathegory'],$path->m_vars['type']))
						return FALSE;
						
					$cathegory = $ret->m_valid_data['cathegory'];
				}
				
				$item = new xItemCathegorizable(-1,$ret->m_valid_data['title'],$type,'autore',
					$ret->m_valid_data['body'],$ret->m_valid_data['filter'],NULL,$cathegory);
				if($item->dbInsert())
				{
					xNotifications::add(NOTIFICATION_NOTICE,'New item successfully created');
				}
				else
				{
					xNotifications::add(NOTIFICATION_ERROR,'Error: Item was not created');
				}
				
				$content->_set("Create new item",'','','');
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

		$content->_set("Create new item",$form->render(),'','');
		return TRUE;
	}
	
	// DOCS INHERITHED  ========================================================
	function onContentCheckPermissionView($path,$item)
	{
		if(! xAccessPermission::checkCurrentUserPermission('item','view',$item->m_type))
			return FALSE;
			
		return xAccessPermission::checkCurrentUserPermission('cathegory','view',$item->m_cathegory);
	}
}


?>
