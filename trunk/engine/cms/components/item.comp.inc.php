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
* Module responsible of item management
*/
class xModuleItem extends xModule
{
	function xModuleItem()
	{
		$this->xModule();
	}
	
	/**
	 * @see xDummyModule::getPermissionDescriptors()
	 */ 
	function getPermissionDescriptors()
	{
		$descr = array(new xAccessPermissionDescriptor('item',0,'create','Create item of any type'));
		
		$itemtypes = xItemType::fildAll();
		foreach($itemtypes as $itemtype)
		{
			$descr[] = new xAccessPermissionDescriptor('item',$itemtype->m_id,'create',
				'Create item of type "'. $itemtype->m_name .'"');
		}
		
		return $descr;
	}
	
	
	/**
	 * @see xDummyModule::getContent()
	 */ 
	function getContent($path)
	{
		switch($path->m_base_path)
		{
			case 'admin/item':
				return $this->_getContentAdminItem();
			case 'item/create':
				return $this->_getContentAdminItemCreate($path);
			case 'item/view':
				return $this->_getContentViewItem($path);
			case 'admin/itemtype':
				return $this->_getContentAdminItemType();
		}
		
		return NULL;
	}
	
	/**
	 * @access private
	 */
	function _getContentAdminItemCreate($path)
	{
		//check for type permission
		$type = 0;
		if(isset($path->m_vars['type']))
		{
			$type = $path->m_vars['type'];
		}
		if(!xAccessPermission::checkCurrentUserPermission('item',$type,'create'))
		{
				return new xContentNotAuthorized();
		}
	
		//check for cathegory permission
		$cat = 0;
		if(isset($path->m_vars['cathegory']))
		{
			$cat = $path->m_vars['cathegory'];
		}
		if(!xAccessPermission::checkCurrentUserPermission('cathegory',$cat,'insert'))
		{
			return new xContentNotAuthorized();
		}

		
		//create form
		$form = new xForm('?p=' . $path->m_full_path);
		
		if($type === 0)
		{
			//item type
			$form->m_elements[] = xItemType::getFormTypeChooser('type','',true);
		}
		
		//item title
		$form->m_elements[] = xItem::getFormTitleInput('title','',true);
		//item body
		$form->m_elements[] = xItem::getFormBodyInput('body','',true);
		//item filter
		$form->m_elements[] = xContentFilterController::getFormContentFilterChooser('filter','html',TRUE);
		
		$group = new xFormGroup('Parameters');
		//item published
		$group->m_elements[] = xItem::getFormPublishedCheck('published',false);
		//item approved
		$group->m_elements[] = xItem::getFormApprovedCheck('approved',false);
		//item sticky
		$group->m_elements[] = xItem::getFormStickyCheck('sticky',false);
		//item accept replies
		$group->m_elements[] = xItem::getFormAcceptRepliesCheck('accept_replies',false);
		$form->m_elements[] = $group;
		
		$group = new xFormGroup('Metadata');
		//item description
		$group->m_elements[] = xItem::getFormDescriptionInput('description','',false);
		//item keywords
		$group->m_elements[] = xItem::getFormKeywordsInput('keywords','',false);
		$form->m_elements[] = $group;
		
		//submit buttom
		$form->m_elements[] = new xFormSubmit('submit','Create');
		
		$ret = $form->validate();
		if(isset($ret->m_valid_data['submit']))
		{
			if(empty($ret->m_errors))
			{
				if($type === 0)
				{
					$type = $ret->m_valid_data['type'];
				}
				
				$item = new xItem(0,$ret->m_valid_data['title'],$type,'autore',
					$ret->m_valid_data['body'],$ret->m_valid_data['filter'],$ret->m_valid_data['published'],
					$ret->m_valid_data['approved'],$ret->m_valid_data['accept_replies'],$ret->m_valid_data['sticky'],
					0,$ret->m_valid_data['description'],$ret->m_valid_data['keywords']);
				$item->dbInsert();
				return new xContentSimple("Create new item (generic)",'New item was created with id: ','','');
			}
			else
			{
				foreach($ret->m_errors as $error)
				{
					xLog::log(LOG_LEVEL_USER_MESSAGE,$error);
				}
			}
		}
		
		return new xContentSimple("Create new item (generic)",$form->render(),'','');
	}
	
	
	/**
	 * @access private
	 */
	function _getContentAdminItem()
	{
		if(!xAccessPermission::checkCurrentUserPermission('item',0,'admin'))
		{
			return new xContentNotAuthorized();
		}
		
		$items = xItem::find();
		
		$output = 
		'<table class="admin-table">
		<tr><th>ID</th><th>Title</th><th>Operations</th></tr>
		';
		foreach($items as $item)
		{
			$output .= '<tr><td>' . $item->m_id . '</td><td>' . $item->m_title . '</td>'
			. '<td>Edit <a href="?p=item/view//id[' . $item->m_id . ']">View</a></td></tr>';
		}
		$output .= "</table>\n";
		
		return new xContentSimple("Admin items",$output,'','');
	}
	
	
	/**
	 * @access private
	 */
	function _getContentViewItem($path)
	{
		if(!isset($path->m_vars['id']))
		{
			return new xContentNotFound();
		}
		
		$item = xItem::dbLoad($path->m_vars['id']);
		
		//here we will provide a check for access filter.
		if(!xAccessPermission::checkCurrentUserPermission('item',$item->m_type_id,'create'))
		{
			return new xContentNotAuthorized();
		}
		
		return new xContentSimple($item->m_title,$item->render(),$item->m_description,$item->m_keywords);
	}
	
	/**
	 * @access private
	 */
	function _getContentAdminItemType()
	{
		if(!xAccessPermission::checkCurrentUserPermission('itemtype',0,'admin'))
		{
			return new xContentNotAuthorized();
		}
		
		$types = xItemType::findAll();
		
		$output = 
		'<table class="admin-table">
		<tr><th>Id</th><th>Name</th><th>Operations</th></tr>
		';
		foreach($types as $type)
		{
			$output .= '<tr><td>' . $type->m_id . '</td><td>' . $type->m_name . '</td><td>Edit</td></tr>';
		}
		$output .= "</table>\n";
		
		return new xContentSimple("Admin item types",$output,'','');
	}
	
	
};

xModule::registerDefaultModule(new xModuleItem());

	
?>
