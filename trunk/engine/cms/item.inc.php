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


$g_xanth_item_managers = array();

/**
 * Represent an item in the CMS. An item can be an article, a blog entry, a forum post.
 */
class xItem extends xElement
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
	var $m_title;
	
	/**
	 * @var string
	 * @access public
	 */
	var $m_type;
	
	/**
	 * @var string
	 * @access public
	 */
	var $m_author;
	
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
	 * @var int
	 * @access public
	 */
	var $m_cathegory;
	
	/**
	 * @var timestamp
	 * @access public
	 */
	var $m_creation_time;
			
	/**
	 * @var timestamp Can be NULL
	 * @access public
	 */
	var $m_lastedit_time;
	
	
	/**
	 *
	 */
	function xItem($id,$title,$type,$author,$content,$content_filter,$cathegory = NULL,$creation_time = NULL,$lastedit_time = NULL)
	{
		$this->xElement();
		
		$this->m_id = $id;
		$this->m_title = $title;
		$this->m_type = $type;
		$this->m_author = $author;
		$this->m_content = $content;
		$this->m_content_filter = $content_filter;
		$this->m_cathegory = $cathegory;
		$this->m_creation_time = $creation_time;
		$this->m_lastedit_time = $lastedit_time;
	}
	
	
	// DOCS INHERITHED  ========================================================
	function onRender()
	{
		$error = '';
		$content = xContentFilterController::applyFilter($this->m_content_filter,$this->m_content,$error);
		$title = xContentFilterController::applyFilter('notags',$this->m_title,$error);
		return xTheme::render3('renderItem',$this->m_type,$title,$content);
	}
	
	/**
	 * Render the item in a compact form with at least a title and optionally a sumary of the content
	 *
	 * @return string
	 */
	function renderSummary()
	{
		$this->onRenderSummary();
	}
	
	/**
	 * Render the item in a compact form with at least a title and optionally a sumary of the content
	 * Override this on your implementation.
	 *
	 * @return string
	 */
	function onRenderSummary()
	{
		$error = '';
		$content = xContentFilterController::applyFilter($this->m_content_filter,$this->m_content,$error);
		$title = xContentFilterController::applyFilter('notags',$this->m_title,$error);
		return xTheme::render2('renderSummaryItem',$title,$content);
	}
	
	
	/** 
	 * Delete an item from db using its id
	 *
	 * @param int $catid
	 * @return bool FALSE on error
	 * @static
	 */
	function dbDeleteById($id)
	{
		return xItemDAO::delete($id);
	}
	
	
	/**
	 * Retrieve a specific item from db.
	 *
	 * @return xItem
	 * @static
	 */
	function dbLoad($id)
	{
		return xItemDAO::load($id);
	}
	
	
	/**
	 * Retrieves all items.
	 *
	 * @param string $type Exact search
	 * @param string $title Like search
	 * @param int $parentid If you specify also a type the search will be restricted to the only type of replies 
	 * suppported by the specified item type, this allow great performance gain.
	 * @param string $author Exact search
	 * @param string $content Like search
	 * @param int $cathegory Exact search on category id
	 * @param int $nelementpage Number of elements per page
	 * @param int $npage Number of page (starting from 1).
	 * @return array(xItem)
	 * @static
	 */
	function find($type = NULL,$parentid = NULL,$title = NULL,$author = NULL,$content = NULL,$cathegory = NULL,$nelementpage = 0,$npage = 0)
	{
		if(!empty($type))
		{
			if($item->m_type == "page")
			{
				return xItemPage::find($parentid,$title,$author,$content,$cathegory,$nelementpage,$npage);
			}
			if($item->m_type == "comment")
			{
				return xItemComment::find($parentid,$title,$author,$content,$cathegory,$nelementpage,$npage);
			}
			else
			{
				return xModule::callWithSingleResult1('xm_findSpecificItems',$type,array($parentid,$title,$author,$content,$cathegory,$nelementpage,$npage));
			}
			
			return array();
		}
		
		return xItemDAO::find($parentid,$title,$author,$content,$cathegory,$nelementpage,$npage);
	}
	
	
	/**
	 * Return a form element for asking for title input
	 *
	 * @param string $var_name The name of the form element
	 * @param string $value
	 * @param bool $mandatory True if this input is manadtory
	 * @return xFormElement
	 * @static
	 */
	function getFormTitleInput($var_name,$value,$mandatory)
	{
		return new xFormElementTextField($var_name,'Title','',$value,$mandatory,new xInputValidatorText(256));
	}
	
	
	/**
	 * Return a form element for asking for body input
	 *
	 * @param string $var_name The name of the form element
	 * @param string $value
	 * @param bool $mandatory True if this input is manadtory
	 * @return xFormElement
	 * @static
	 */
	function getFormBodyInput($var_name,$label,$description,$value,$mandatory,$input_validator)
	{
		return new xFormElementTextArea($var_name,$label,$description,$value,$mandatory,$input_validator);
	}
};


/**
 * Root class of a hieararchy that manage view,creation,deletion,modification of item types.
 */
class xItemManager
{
	function xItemManager()
	{}
	
		/**
	 * Register an item class as the class in charge of managing create,view,edit,permission, delete of a specific 
	 * item type.
	 *
	 * @param string $classname
	 * @param string $item_type
	 * @static
	 */
	function registerItemManager($manager,$itemtype)
	{
		global $g_xanth_item_managers;
		
		$g_xanth_item_managers[$itemtype] = $manager;
	}
	
	
	/**
	 * Gets the class in charge of managing create,view,edit,permission, delete of a specific 
	 * item type.
	 *
	 * @param string $item_type
	 * @return string
	 * @static
	 */
	function getItemManager($itemtype)
	{
		global $g_xanth_item_managers;
		
		if(isset($g_xanth_item_managers[$itemtype]))
		{
			return $g_xanth_item_managers[$itemtype];
		}
		
		return new xItemManager();
	}
	
	/**
	 * Retrieve a specific item from db.
	 *
	 * @return xItem
	 * @static
	 */
	function dbLoad($id)
	{
		return xItem::dbLoad($id);
	}
	
	/**
	 * Create content to fill the item/create page. You need to fill the provided $content
	 * object with your data.
	 *
	 * @param xXanthPath $path
	 * @param xContentItem $content
	 * @return bool True if the creation of content succeeded
	 * @todo filter types and cathegories.
	 */
	function onContentCreate($path,&$content)
	{
		//type
		$type = $path->m_vars['type'];
				
		//cathegory
		$cathegory = NULL;
		if(isset($path->m_vars['cathegory']))
			$cathegory = $path->m_vars['cathegory'];
				

		//create form
		$form = new xForm('?p=' . $this->m_path->m_full_path);
		
		if($cathegory === NULL)
		{
			//parent cathegory
			$form->m_elements[] = xCathegory::getFormCathegoryChooser('cathegory','Cathegory','','',FALSE,FALSE);
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
					$cathegory = $ret->m_valid_data['cathegory'];
				}
				
				$item = new xItem(-1,$ret->m_valid_data['title'],'page','autore',
					$ret->m_valid_data['body'],$ret->m_valid_data['filter'],$cathegory,NULL,NULL);
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
	
	/**
	 * Check permission for creating an item. 
	 *
	 * @param xXanthPath $path
	 * @return bool
	 */
	function onContentCheckPermissionCreate($path)
	{
		if(isset($path->m_vars['type']))
		{
			$type = $path->m_vars['type'];
			
			if(!xAccessPermission::checkCurrentUserPermission('item','create',$type))
			{
				return FALSE;
			}
		}
		
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
	
	/**
	 * Create content to fill the item/view page. You need to fill the provided $content
	 * object with your data.
	 *
	 * @param xXanthPath $path
	 * @param xContentItem $content
	 * @param xItem $item The item to display
	 * @return bool True if the creation of content succeeded
	 */
	function onContentView($path,$item,&$content)
	{
		$content->_set($item->m_title,$item->render(),'','');
		return TRUE;
	}
	
	/**
	 * Check permission for viewing an item. 
	 *
	 * @param xItem $item
	 * @return bool
	 */
	function onContentCheckPermissionView($path,$item)
	{
		return xAccessPermission::checkCurrentUserPermission('item','view',$item->m_type);
	}
}


?>
