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
		$descr = array(new xAccessPermissionDescriptor('item','create','Create item of any type'));
		
		$itemtypes = xItemType::findAll();
		foreach($itemtypes as $itemtype)
		{
			$descr[] = new xAccessPermissionDescriptor('item','create',
				'Create item of type "'. $itemtype->m_name .'"',$itemtype->m_name);
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
			case 'item/page/create':
				return $this->_getContentItemPageCreate($path);
			case 'item/page/view':
				return $this->_getContentItemPageView($path);
			case 'admin/itemtype':
				return $this->_getContentAdminItemType();
		}
		
		return NULL;
	}
	
	/**
	 * @access private
	 */
	function _getContentItemPageCreate($path)
	{
		//check for type permission
		$subtype = NULL;
		$typecheck = 'page';
		
		if(! xAccessPermission::checkCurrentUserPermission('item','create','page'))
		{
			if(isset($path->m_vars['subtype']))
			{
				$subtype = $path->m_vars['subtype'];
				
				if(!xAccessPermission::checkCurrentUserPermission('item','create','page/'.$subtype))
				{
					return new xContentNotAuthorized();
				}
			}
			else
			{
				return new xContentNotAuthorized();
			}
		}
		
		//check for cathegory permission
		if(! xAccessPermission::checkCurrentUserPermission('cathegory','insert'))
		{
			$cat = 0;
			if(isset($path->m_vars['cathegory']))
			{
				$cat = $path->m_vars['cathegory'];
				
				if(! xAccessPermission::checkCurrentUserPermission('cathegory','insert',$cat))
				{
					return new xContentNotAuthorized();
				}
			}
			else
			{
				return new xContentNotAuthorized();
			}
		}

		
		//create form
		$form = new xForm('?p=' . $path->m_full_path);
		
		if($subtype === NULL)
		{
			//item page subtype
			$form->m_elements[] = xItemPageType::getFormItemPageTypeChooser('subtype','Choose subtype','','',TRUE);
		}
		
		//item title
		$form->m_elements[] = xItem::getFormTitleInput('title','',true);
		//item body
		$form->m_elements[] = xItem::getFormBodyInput('body','Body','','',true,
			new xInputValidatorDynamicContentFilter(0,'filter'));
		//item filter
		$form->m_elements[] = xContentFilterController::getFormContentFilterChooser('filter','html',TRUE);
		
		
		$group = new xFormGroup('Parameters');
		//item published
		$group->m_elements[] = xItemPage::getFormPublishedCheck('published','Published','',false);
		//item approved
		$group->m_elements[] = xItemPage::getFormApprovedCheck('approved','Approved','',false);
		//item sticky
		$group->m_elements[] = xItemPage::getFormStickyCheck('sticky','Sticky','',false);
		//item accept replies
		$group->m_elements[] = xItemPage::getFormAcceptRepliesCheck('accept_replies','Accept Replies','',false);
		$form->m_elements[] = $group;
		
		$group = new xFormGroup('Metadata');
		//item description
		$group->m_elements[] = xItemPage::getFormDescriptionInput('description','Description','','',false);
		//item keywords
		$group->m_elements[] = xItemPage::getFormKeywordsInput('keywords','Keywords','','',false);
		$form->m_elements[] = $group;
		
		//submit buttom
		$form->m_elements[] = new xFormSubmit('submit','Create');
		
		$ret = $form->validate();
		if(! $ret->isEmpty())
		{
			if(empty($ret->m_errors))
			{
				if($subtype === NULL)
				{
					$subtype = $ret->m_valid_data['subtype'];
				}
				
				$item = new xItemPage(-1,$ret->m_valid_data['title'],'page','autore',
					$ret->m_valid_data['body'],$ret->m_valid_data['filter'],NULL,NULL,$subtype,
					$ret->m_valid_data['published'],$ret->m_valid_data['sticky'],$ret->m_valid_data['accept_replies'],
					$ret->m_valid_data['approved'],0,$ret->m_valid_data['description'],$ret->m_valid_data['keywords']);
				if($item->dbInsert())
				{
					xNotifications::add(NOTIFICATION_NOTICE,'New item successfully created');
				}
				else
				{
					xNotifications::add(NOTIFICATION_ERROR,'Error: Item was not created');
				}
				
				return new xContentSimple("Create new item page",'','','');
			}
			else
			{
				foreach($ret->m_errors as $error)
				{
					xNotifications::add(NOTIFICATION_WARNING,$error);
				}
			}
		}
		
		return new xContentSimple("Create new item page",$form->render(),'','');
	}
	
	
	/**
	 * @access private
	 */
	function _getContentAdminItem()
	{
		if(!xAccessPermission::checkCurrentUserPermission('item','admin'))
		{
			return new xContentNotAuthorized();
		}
		
		$items = xItem::find();
		
		$error = '';
		
		$output = 
		'<table class="admin-table">
		<tr><th>ID</th><th>Title</th><th>Operations</th></tr>
		';
		foreach($items as $item)
		{
			$output .= '<tr><td>' . $item->m_id . '</td><td>' . 
				xContentFilterController::applyFilter('notags',$item->m_title,$error) . '</td>'
			. '<td>Edit <a href="?p=item/' .$item->m_type. '/view//id[' . $item->m_id . ']">View</a></td></tr>';
		}
		$output .= "</table>\n";
		
		return new xContentSimple("Admin items",$output,'','');
		
	}
	
	
	/**
	 * @access private
	 */
	function _getContentItemPageView($path)
	{
		if(!isset($path->m_vars['id']))
		{
			return new xContentNotFound();
		}
		
		$item = xItemPage::dbLoad($path->m_vars['id']);
		
		if($item === NULL)
		{
			return new xContentNotFound();
		}
		
		//check access
		if(!xAccessPermission::checkCurrentUserPermission('item','view',$item->m_type . '/' . $item->m_subtype))
		{
			return new xContentNotAuthorized();
		}
		
		return new xContentSimple($item->m_title,$item->render(),$item->m_meta_description,$item->m_meta_keywords);
	}
	
	/**
	 * @access private
	 */
	function _getContentAdminItemType()
	{
		if(!xAccessPermission::checkCurrentUserPermission('itemtype','admin'))
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
