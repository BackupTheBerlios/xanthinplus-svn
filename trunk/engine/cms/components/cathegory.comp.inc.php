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
* Module responsible of cathegory management
*/
class xModuleCathegory extends xModule
{
	function xModuleCathegory()
	{
		$this->xModule();
	}
	
	// DOCS INHERITHED  ========================================================
	function xm_contentFactory($path)
	{
		switch($path->m_base_path)
		{
			case 'admin/cathegory':
				return new xContentAdminCathegory($path);
			case 'cathegory/create':
				return new xContentCathegoryCreate($path);
			case 'cathegory_type/create':
				return new xContentCathegoryTypeCreate($path);
		}
		
		return NULL;
	}
	
	
	/**
	 * @see xDummyModule::getPermissionDescriptors()
	 */ 
	function xm_getPermissionDescriptors()
	{
		$descr = array(new xAccessPermissionDescriptor('cathegory','insert_item','Insert an item in any cathegory'));
		$descr[] = new xAccessPermissionDescriptor('cathegory','create_inside','Create cathegory inside any other');
		$descr[] = new xAccessPermissionDescriptor('cathegory','admin','View admin cathegory');
				
		$cathegories = xCathegory::findAll();
		foreach($cathegories as $cathegory)
		{
			$descr[] = new xAccessPermissionDescriptor('cathegory','insert_item',
				'Insert item in cathegory "'. $cathegory->m_name .'"',$cathegory->m_id);
				
			$descr[] = new xAccessPermissionDescriptor('cathegory','create_inside',
				'Create cathegory inside "'. $cathegory->m_name .'"',$cathegory->m_id);
		}
		
		$descr[] = new xAccessPermissionDescriptor('cathegory','create','Create cathegory of any type');
		$types = xCathegoryType::findAll();
		foreach($types as $type)
		{
			$descr[] = new xAccessPermissionDescriptor('cathegory','create',
				'Create cathegory of type "'. $type->m_name .'"',$type->m_name);
		}

		return $descr;
	}
	
};

xModule::registerDefaultModule(new xModuleCathegory());








/**
 *
 *
 * @internal
 */
class xContentAdminCathegory extends xContent
{

	function xContentAdminCathegory($path)
	{
		xContent::xContent($path);
	}
	
	
	// DOCS INHERITHED  ========================================================
	function onCheckPermission()
	{
		return xAccessPermission::checkCurrentUserPermission('cathegory','admin');
	}
	
	// DOCS INHERITHED  ========================================================
	function onCheckPreconditions()
	{
		return TRUE;
	}
	
	// DOCS INHERITHED  ========================================================
	function onCreate()
	{
		$cathegories = xCathegory::findAll();
		
		$output = 
		'<table class="admin-table">
		<tr><th>id</th><th>Name</th><th>Type</th><th>Parent</th><th>Operations</th></tr>
		';
		foreach($cathegories as $cathegory)
		{
			$output .= '<tr><td>' . $cathegory->m_id . '</td><td>' . $cathegory->m_name . 
				'</td><td>' . $cathegory->m_type . '</td><td>' . $cathegory->m_parent_cathegory . 
				'</td><td>Edit</td></tr>';
		}
		$output .= "</table>\n";
		
		xContent::_set("Admin cathegories",$output,'','');
		return TRUE;
	}
}




/**
 *
 *
 * @internal
 */
class xContentCathegoryCreate extends xContent
{

	function xContentCathegoryCreate($path)
	{
		xContent::xContent($path);
	}
	
	
	// DOCS INHERITHED  ========================================================
	function onCheckPermission()
	{
		if(! isset($this->m_path->m_vars['type']))
			return true;
		
		$manager = xCathegoryManager::getCathegoryManager($this->m_path->m_vars['type']);
		
		return $manager->onContentCheckPermissionCreate($this->m_path);
	}
	
	// DOCS INHERITHED  ========================================================
	function onCheckPreconditions()
	{
		return TRUE;
	}
	
	// DOCS INHERITHED  ========================================================
	function onCreate()
	{
		if(! isset($this->m_path->m_vars['type']))
		{
			//let user to choose the cathegory type from a list
			$types = xCathegoryType::findAll();
			
			$output = 'Choose a cathegory type: 
			<ul>';
			foreach($types as $type)
			{
				$output .= '<li><a href="?p=cathegory/create//type['.$type->m_name.']">'.$type->m_name.'</a></li>';
			}
			$output .= '</ul>';
			
			$this->_set('Create cathegory: choose a type',$output,'','');
			return true;
		}
		else
		{
			$manager = xCathegoryManager::getCathegoryManager($this->m_path->m_vars['type']);
			return $manager->onContentCreate($this->m_path,$this);
		}
	}
}




/**
 *
 *
 * @internal
 */
class xContentCathegoryTypeCreate extends xContent
{

	function xContentCathegoryTypeCreate($path)
	{
		xContent::xContent($path);
	}
	
	
	// DOCS INHERITHED  ========================================================
	function onCheckPermission()
	{
		//check for type permission
		return xAccessPermission::checkCurrentUserPermission('cathegory_type','create');
	}
	
	// DOCS INHERITHED  ========================================================
	function onCheckPreconditions()
	{
		return TRUE;
	}
	
	// DOCS INHERITHED  ========================================================
	function onCreate()
	{
		//create form
		$form = new xForm('?p=' . $this->m_path->m_full_path);
		
		//name
		$form->m_elements[] = xCathegoryType::getFormNameInput('name','Name','','',TRUE);
		//description
		$form->m_elements[] = xCathegoryType::getFormDescriptionInput('description','Description','','',FALSE);
		//item types
		$form->m_elements[] = xItemType::getFormItemTypeChooser('item_types','Item types','','',TRUE,TRUE);
		
		//submit buttom
		$form->m_elements[] = new xFormSubmit('submit','Create');
		
		$ret = $form->validate();
		if(isset($ret->m_valid_data['submit']))
		{
			if(empty($ret->m_errors))
			{
				
				$cattype = new xCathegoryType($ret->m_valid_data['name'],$ret->m_valid_data['description'],
					$ret->m_valid_data['item_types']);
					
				if($cattype->dbInsert())
				{
					xNotifications::add(NOTIFICATION_NOTICE,'New cathegory type successfully created');
				}
				else
				{
					xNotifications::add(NOTIFICATION_ERROR,'Error: Cathegory type was not created');
				}
				
				
				xContent::_set("Create cathegory type",'','','');
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

		xContent::_set("Create new cathegory type",$form->render(),'','');
		return TRUE;
	}
}




	
?>

