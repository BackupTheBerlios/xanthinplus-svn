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
	function xm_getPermissionDescriptors()
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
	function xm_contentFactory($path)
	{
		switch($path->m_base_path)
		{
			case 'admin/items':
				return new xContentAdminItems($path);
			case 'item/page/create':
				return new xContentItemPageCreate($path);
			case 'item/page/view':
				return new xContentItemPageView($path);
			case 'admin/itemtypes':
				return new xContentAdminItemtypes($path);
		}
		
		return NULL;
	}
	
};

xModule::registerDefaultModule(new xModuleItem());










/**
 * @internal
 */
class xContentItemPageCreate extends xContent
{	
	function xContentItemPageCreate($path)
	{
		$this->xContent($path);
	}

	// DOCS INHERITHED  ========================================================
	function onCheckPermission()
	{
		if(! xAccessPermission::checkCurrentUserPermission('item','create','page'))
		{
			if(isset($this->m_path->m_vars['subtype']))
			{
				$subtype = $this->m_path->m_vars['subtype'];
				
				if(!xAccessPermission::checkCurrentUserPermission('item','create','page/'.$subtype))
				{
					return FALSE;
				}
			}
			else
			{
				return FALSE;
			}
		}
		
		//check for cathegory permission
		if(! xAccessPermission::checkCurrentUserPermission('cathegory','insert_item'))
		{
			if(isset($this->m_path->m_vars['cathegory']))
			{
				$cathegory = $this->m_path->m_vars['cathegory'];
				
				if(! xAccessPermission::checkCurrentUserPermission('cathegory','insert_item',$cathegory))
				{
					return FALSE;
				}
			}
			else
			{
				return FALSE;
			}
		}
		return TRUE;
	}
	
	
	
	// DOCS INHERITHED  ========================================================
	function onCreate()
	{
		//check for type permission
		$subtype = NULL;
		
		if(isset($this->m_path->m_vars['subtype']))
			$subtype = $this->m_path->m_vars['subtype'];
				
		
		//check for cathegory permission
		$cathegory = NULL;
		if(isset($this->m_path->m_vars['cathegory']))
			$cathegory = $this->m_path->m_vars['cathegory'];
				

		//create form
		$form = new xForm('?p=' . $this->m_path->m_full_path);
		
		if($subtype === NULL)
		{
			//item page subtype
			$form->m_elements[] = xItemPageType::getFormItemPageTypeChooser('subtype','Choose subtype','','',TRUE);
		}
		
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
				
				if($cathegory === NULL)
				{
					$cathegory = $ret->m_valid_data['cathegory'];
				}
				
				$item = new xItemPage(-1,$ret->m_valid_data['title'],'page','autore',
					$ret->m_valid_data['body'],$ret->m_valid_data['filter'],$cathegory,NULL,NULL,$subtype,
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
				
				xContent::_set("Create new item page",'','','');
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

		xContent::_set("Create new item page",$form->render(),'','');
		return TRUE;
	}
};









/**
 * @internal
 */
class xContentAdminItems extends xContent
{	
	function xContentAdminItems($path)
	{
		$this->xContent($path);
	}

	// DOCS INHERITHED  ========================================================
	function onCheckPermission()
	{
		return xAccessPermission::checkCurrentUserPermission('item','admin');
	}
	
	
	// DOCS INHERITHED  ========================================================
	function onCreate()
	{
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
		
		xContent::_set("Admin items",$output,'','');
		return TRUE;
	}
};








/**
 * @internal
 */
class xContentAdminItemtypes extends xContent
{	
	function xContentAdminItemtypes($path)
	{
		$this->xContent($path);
	}

	// DOCS INHERITHED  ========================================================
	function onCheckPermission()
	{
		return xAccessPermission::checkCurrentUserPermission('itemtype','admin');
	}
	
	
	// DOCS INHERITHED  ========================================================
	function onCreate()
	{
		$types = xItemType::findAll();
		
		$output = 
		'<table class="admin-table">
		<tr><th>Name</th><th>Operations</th></tr>
		';
		foreach($types as $type)
		{
			$output .= '<tr><td>' . $type->m_name . '</td><td>Edit</td></tr>';
		}
		$output .= "</table>\n";
		
		xContent::_set("Admin item types",$output,'','');
		return TRUE;
	}
};




/**
 * @internal
 */
class xContentItemPageView extends xContent
{	
	var $m_item;
	
	
	function xContentItemPageView($path)
	{
		$this->xContent($path);
		$this->m_item = NULL;
	}

	// DOCS INHERITHED  ========================================================
	function onCheckPermission()
	{
		$this->m_item = NULL;
		if(isset($this->m_path->m_vars['id']))
		{
			$this->m_item = xItemPage::dbLoad($this->m_path->m_vars['id']);
			
			if($this->m_item != NULL)
				return xAccessPermission::checkCurrentUserPermission('item','view',
					$this->m_item->m_type . '/' . $this->m_item->m_subtype);
		}
		
		return TRUE;
	}
	
	
	// DOCS INHERITHED  ========================================================
	function onCreate()
	{
		if($this->m_item === NULL)
		{
			return new xContentNotFound();
		}
		
		xContent::_set($this->m_item->m_title,$this->m_item->render(),$this->m_item->m_meta_description,
			$this->m_item->m_meta_keywords);
		return TRUE;
	}
};



	
?>
