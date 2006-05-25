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
	
	/**
	 * @see xDummyModule::getPermissionDescriptors()
	 */ 
	function getPermissionDescriptors()
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
	
	
	// DOCS INHERITHED  ========================================================
	function getContent($path)
	{
		switch($path->m_base_path)
		{
			case 'admin/cathegory':
				return $this->_getContentAdminCathegory();
			case 'cathegory/create':
				return $this->_getContentCathegoryCreate($path);
		}
		
		return NULL;
	}
	
	/**
	 * @access private
	 */
	function _getContentAdminCathegory()
	{
		if(!xAccessPermission::checkCurrentUserPermission('cathegory','admin'))
		{
			return new xContentNotAuthorized();
		}
		
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
		
		return new xContentSimple("Admin cathegories",$output,'','');
	}
	
	
	/**
	 * @access private
	 */
	function _getContentCathegoryCreate($path)
	{
		//check for type permission
		$type = NULL;
		if(! xAccessPermission::checkCurrentUserPermission('cathegory','create'))
		{
			if(isset($path->m_vars['type']))
			{
				$type = $path->m_vars['type'];
				
				if(!xAccessPermission::checkCurrentUserPermission('cathegory','create',$type))
				{
					return new xContentNotAuthorized();
				}
			}
			else
			{
				return new xContentNotAuthorized();
			}
		}
		
		
		//check for parent permission
		$parentcat = NULL;
		if(! xAccessPermission::checkCurrentUserPermission('cathegory','create_inside'))
		{
			if(isset($path->m_vars['parentcat']))
			{
				$parentcat = $path->m_vars['parentcat'];
				
				if(!xAccessPermission::checkCurrentUserPermission('cathegory','create_inside',$parentcat))
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
		
		if($type === NULL)
		{
			//type
			$form->m_elements[] = xCathegoryType::getFormCathegoryTypeChooser('type','Type','','',TRUE);
		}
		
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
				
				if($type === NULL)
				{
					$type = $ret->m_valid_data['type'];
				}
				
				
				$cat = new xCathegory(0,$ret->m_valid_data['name'],$type,$ret->m_valid_data['description'],
					$parentcat,NULL);
					
				if($cat->dbInsert())
				{
					xNotifications::add(NOTIFICATION_NOTICE,'New cathegory successfully created');
				}
				else
				{
					xNotifications::add(NOTIFICATION_ERROR,'Error: Cathegory was not created');
				}
				
				return new xContentSimple("Create cathegory",'New cathegory was created with id: ','','');
			}
			else
			{
				foreach($ret->m_errors as $error)
				{
					xNotifications::add(NOTIFICATION_WARNING,$error);
				}
			}
		}
		
		return new xContentSimple("Create new cathegory",$form->render(),'','');
	}
	
};

xModule::registerDefaultModule(new xModuleCathegory());

	
?>

