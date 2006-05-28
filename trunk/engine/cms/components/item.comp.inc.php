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
	 * @see xDummyModule::getContent()
	 */ 
	function xm_contentFactory($path)
	{
		switch($path->m_base_path)
		{
			case 'admin/items':
				return new xContentAdminItems($path);
			case 'admin/itemtypes':
				return new xContentAdminItemtypes($path);
			case 'item/create':
				return new xContentItemCreate($path);
			case 'item/view':
				return new xContentItemView($path);
		}
		
		return NULL;
	}
	
};

xModule::registerDefaultModule(new xModuleItem());






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
			. '<td>Edit <a href="?p=item/view//id[' . $item->m_id . ']//type['.$item->m_type.']">View</a></td></tr>';
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
class xContentItemCreate extends xContent
{	
	function xContentItemCreate($path)
	{
		$this->xContent($path);
	}

	// DOCS INHERITHED  ========================================================
	function onCheckPermission()
	{
		if(! isset($this->m_path->m_vars['type']))
			return TRUE;
		
		$manager = xItemManager::getItemManager($this->m_path->m_vars['type']);
		
		return $manager->onContentCheckPermissionCreate($this->m_path);
	}
	
	// DOCS INHERITHED  ========================================================
	function onCreate()
	{
		if(! isset($this->m_path->m_vars['type']))
		{
			//let user to choose the item type from a list
			$types = xItemType::findAll();
			
			$output = 'Choose an item type: 
			<ul>';
			foreach($types as $type)
			{
				$output .= '<li><a href="?p=item/create//type['.$type->m_name.']">'.$type->m_name.'</a></li>';
			}
			$output .= '</ul>';
			
			$this->_set('Create item: choose a type',$output,'','');
			return true;
		}
		else
		{
			$manager = xItemManager::getItemManager($this->m_path->m_vars['type']);
			return $manager->onContentCreate($this->m_path,$this);
		}
	}
};




/**
 * @internal
 */
class xContentItemView extends xContent
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
			$type = '';
			if(isset($this->m_path->m_vars['type']))
				$type = $this->m_path->m_vars['type'];
				
			$manager = xItemManager::getItemManager($type);
			
			$this->m_item = $manager->dbLoad($this->m_path->m_vars['id']);
			
			if($this->m_item !== NULL)
			{
				return $manager->onContentCheckPermissionView($this->m_path,$this->m_item);
			}
		}
		
		return TRUE;
	}
	
	
	// DOCS INHERITHED  ========================================================
	function onCreate()
	{
		if($this->m_item === NULL)
		{
			return new xContentNotFound($this->m_path);
		}
		
		$type = '';
		if(isset($this->m_path->m_vars['type']))
			$type = $this->m_path->m_vars['type'];
			
		$manager = xItemManager::getItemManager($type);
		
		return $manager->onContentView($this->m_path,$this->m_item,$this);
	}
};



	
?>
