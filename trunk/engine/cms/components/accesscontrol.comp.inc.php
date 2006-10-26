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
* Module responsible of access filters and permission+
*
* @todo optimize access permission
*/
class xModuleAccessControl extends xModule
{
	function xModuleAccessControl()
	{
		$this->xModule();
	}
	
	// DOCS INHERITHED  ========================================================
	function xm_fetchContent($path)
	{
		if($path->m_resource === 'accesspermissions' && $path->m_action === 'admin')
		{
			return new xPageContentAdminAccesspermissions($path);
		}
		
		return NULL;
	}
};

xModule::registerDefaultModule(new xModuleAccessControl());







/**
 * @internal
 */
class xFormAccessPermission extends xForm
{
	var $_m_permissions;
	var $_m_roles;
	
	
	function xFormAccessPermission($target)
	{
		xForm::xForm($target);
		
		$this->_m_permissions = xModule::callWithArrayResult0('xm_fetchPermissionDescriptors');
		$this->_m_permissions = xFormAccessPermission::_accessPermissionGroupArray($this->_m_permissions);
		$this->_m_roles = xRole::findAll();
		
		//remove administrator
		array_shift($this->_m_roles);
	}
	
	/**
	 * @access private
	 * @return array(resourcename(string) => array(typename(string) => array(array("operation" => string ,"description" => string)))
	 */
	function _accessPermissionGroupArray($permissions_not_grouped)
	{
		$ordered_permissions = array();
		foreach($permissions_not_grouped as $perm1)
		{
			if($perm1->m_resource_id == NULL)
			{
				$ordered_permissions[$perm1->m_resource][$perm1->m_resource_type][] = 
					array("action" => $perm1->m_action, "description" => $perm1->m_description);
			}
		}
		
		return $ordered_permissions;
	}
	
	
	// DOCS INHERITHED  ========================================================
	function validate()
	{
		foreach($this->_m_permissions as $perm_resource => $perm_types)
		{
			foreach($perm_types as $perm_typename => $perm_acts)
			{
				foreach($perm_acts as $perm_act)
				{
					foreach($this->_m_roles as $role)
					{
						$input_name = 'permission['.$perm_resource.']['.$perm_act['action'].']['.$role->m_name.']';
						if($perm_typename != NULL)
						{
							$input_name .= '['.$perm_typename.']';
						}
						
						$this->m_elements[] = new xFormElementCheckbox($input_name,'','',1,FALSE,FALSE,
							new xInputValidatorInteger());
					}
				}
			}
		}
	
		return xForm::validate();
	}
	
	

	// DOCS INHERITHED  ========================================================
	function render()
	{
		$output = xForm::_renderFormHeader();
		$output .= '<table><tr><th></th><th>Description</th>';
		foreach($this->_m_roles as $role)
		{
			$output .= '<th>' . $role->m_name .'</th>';
		}
		
		foreach($this->_m_permissions as $perm_resource => $perm_types)
		{
			$output .= '<tr><td>Resource: ' . $perm_resource . '</td>';
			foreach($this->_m_roles as $role)
			{
				$output .= '<td></td>';
			}
			
			$output .= '</tr>';
			foreach($perm_types as $perm_typename => $perm_acts)
			{
				$output .= '<tr><td>&nbsp;&nbsp;Resource Type: ' . $perm_typename . '</td>';
				foreach($this->_m_roles as $role)
				{
					$output .= '<td></td>';
				}
				$output .= '</tr>';
				foreach($perm_acts as $perm_act)
				{
					$output .= '<tr><td>&nbsp;&nbsp;&nbsp;&nbsp;Action: '.$perm_act['action'].'</td>';
					$output .= '<td>' . $perm_act['description'] . '</td>';
					foreach($this->_m_roles as $role)
					{
						$checked = xAccessPermission::checkPermission($perm_resource,$perm_typename,NULL,
							$perm_act['action'],$role->m_name);
						$output .= '<td>';
						
						$input_name = 'permission['.$perm_resource.']['.$perm_act['action'].']['.$role->m_name.']';
						if($perm_typename != NULL)
						{
							$input_name .= '['.$perm_typename.']';
						}
						
						$check = new xFormElementCheckbox($input_name,'','',1,$checked,FALSE,new xInputValidatorInteger());
						
						$output .= $check->render();
						$output .= '</td>';
						
					}
					$output .= '</tr>';
				}
			}
		}
		$output .= '</table>';
		$sub = new xFormSubmit('submit','Save');
		$output .= $sub->render();
		$output .= '</form>';
		
		return $output;
	}
}




/**
 *
 *
 * @internal
 */
class xPageContentAdminAccesspermissions extends xPageContent
{

	function xPageContentAdminAccesspermissions($path)
	{
		xPageContent::xPageContent($path);
	}
	
	// DOCS INHERITHED  ========================================================
	function onCheckPreconditions()
	{
		if(xAccessPermission::checkCurrentUserPermission('admin/accesspermissions',NULL,NULL,'view'))
		{
			return TRUE;
		}
		
		return new xPageContentNotAuthorized($this->m_path);
	}
	
	// DOCS INHERITHED  ========================================================
	function onCreate()
	{
		//create form
		$form = new xFormAccessPermission('?p=' . $this->m_path->m_full_path);
		
		$ret = $form->validate();
		if(! $ret->isEmpty())
		{
			if(empty($ret->m_errors))
			{
				$walker = new xFormArrayInputWalker($ret->m_valid_data);
				while($curr = $walker->next('permission'))
				{
					if(count($curr) == 4)
					{
						list($value,$resource,$action,$role) = $curr;
						$type = NULL;
					}
					else
					{
						list($value,$resource,$action,$role,$type) = $curr;
					}
					
					$perm = new xAccessPermission($resource,$type,NULL,$action,$role);
					$perm_present = $perm->check();
					if($value)
					{
						if(! $perm_present)
						{
							if($perm->insert())
							{
								xNotifications::add(NOTIFICATION_NOTICE,'Permission updated successfully');
							}
							else
							{
								xNotifications::add(NOTIFICATION_ERROR,'Error while performing action');
							}
						}
					}
					else
					{
						if($perm_present)
						{
							if($perm->delete())
							{
								xNotifications::add(NOTIFICATION_NOTICE,'Permission updated successfully');
							}
							else
							{
								xNotifications::add(NOTIFICATION_ERROR,'Error while performing action');
							}
						}
					}
				}
			}
			else
			{
				foreach($ret->m_errors as $error)
				{
					xNotifications::add(NOTIFICATION_WARNING,$error);
				}
			}
		}
		
		xPageContent::_set("Access Permissions",$form->render(),'','');
		return true;
	}
	
}


?>
