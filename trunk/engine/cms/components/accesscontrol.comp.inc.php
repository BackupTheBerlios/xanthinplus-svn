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
	function getContent($path)
	{
		if($path->m_base_path == 'admin/accessfilters')
		{
			return $this->_getContentAdminAccessFilters();
		}
		elseif($path->m_base_path == 'admin/accesspermissions')
		{
			return $this->_getContentAdminAccesspermissions($path);
		}
		
		return NULL;
	}
	
	/**
	 * @access private
	 */
	function _getContentAdminAccessFilters()
	{
		//only if administrator!
		if(!xUser::currentHaveRole('administrator'))
		{
			return new xContentNotAuthorized();
		}
		
		$filtersets = xAccessFilterSet::findAll();
		
		$output = 
		'<table class="admin-table">
		<tr><th>Filter id</th><th>Name</th><th>Description</th><th>Filters</th><th>Operations</th></tr>
		';
		foreach($filtersets as $filterset)
		{
			$output .= '<tr><td>' . $filterset->m_id . '</td><td>' . $filterset->m_name . '</td><td>'.
				$filterset->m_description . '</td><td>';

			foreach($filterset->m_filters as $filter)
			{
				if(xanth_instanceof($filter,'xAccessFilterRole'))
				{
					$output .= 'Role Filter: ' . $filter->m_role_name;
				}
				elseif(xanth_instanceof($filter,'xAccessFilterPathExclude'))
				{
					$output .= 'Path Exclude Filter: ' . $filter->m_path;
				}
				elseif(xanth_instanceof($filter,'xAccessFilterPathInclude'))
				{
					$output .= 'Path Include Filter: ' . $filter->m_path;
				}
				else
				{
					$output .= 'Unknown filter type!';
				}
				
				$output .= '<br/>';
			}
		
			$output .= '</td><td>Edit</td></tr>';
		}
		$output .= "</table>\n";
		
		return new xContentSimple("Manage Access Filters",$output,'','');
	}
	
	/**
	 * @access private
	 */
	function _getContentAdminAccesspermissions($path)
	{
		//only if administrator!
		if(!xUser::currentHaveRole('administrator'))
		{
			return new xContentNotAuthorized();
		}
		
		//create form
		$form = new xFormAccessPermission('?p=' . $path->m_full_path);
		
		$ret = $form->validate();
		if(! $ret->isEmpty())
		{
			if(empty($ret->m_errors))
			{
				$walker = new xFormArrayInputWalker($ret->m_valid_data);
				while($curr = $walker->next('permission'))
				{
					list($value,$resource,$operation,$type,$role) = $curr;
					
					$perm = new xAccessPermission($resource,$operation,$role,$type);
					$perm_present = $perm->check();
					if($value)
					{
						if(! $perm_present)
						{
							if($perm->dbInsert())
							{
								xNotifications::add(NOTIFICATION_NOTICE,'New item successfully created');
							}
							else
							{
								xNotifications::add(NOTIFICATION_ERROR,'Error: Item was not created');
							}
						}
					}
					else
					{
						if($perm_present)
						{
							if($perm->dbDelete())
							{
								xNotifications::add(NOTIFICATION_NOTICE,'New item successfully created');
							}
							else
							{
								xNotifications::add(NOTIFICATION_ERROR,'Error: Item was not created');
							}
						}
					}
				}
				
				
				
				return new xContentSimple("Access Permissions",'','','');
			}
			else
			{
				foreach($ret->m_errors as $error)
				{
					xNotifications::add(NOTIFICATION_WARNING,$error);
				}
			}
		}
		
		
		return new xContentSimple("Access Permissions",$form->render(),'','');
	
	}

};

xModule::registerDefaultModule(new xModuleAccessControl());


class xFormAccessPermission extends xForm
{
	var $_m_permissions;
	var $_m_roles;
	
	
	function xFormAccessPermission($target)
	{
		xForm::xForm($target);
		
		$this->_m_permissions = xModule::callWithArrayResult0('getPermissionDescriptors');
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
			$ordered_permissions[$perm1->m_resource][$perm1->m_resource_type][] = 
				array("operation" => $perm1->m_operation, "description" => $perm1->m_description);
		}
		
		return $ordered_permissions;
	}
	
	
	// DOCS INHERITHED  ========================================================
	function validate()
	{
		foreach($this->_m_permissions as $perm_resource => $perm_types)
		{
			foreach($perm_types as $perm_typename => $perm_ops)
			{
				foreach($perm_ops as $perm_op)
				{
					foreach($this->_m_roles as $role)
					{
						$this->m_elements[] = new xFormElementCheckbox('permission['.$perm_resource.
							']['.$perm_op['operation'].']['.$perm_typename.']['.$role->m_name.']'
							,'','',1,FALSE,FALSE,new xInputValidatorInteger());
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
		$output .= '<table><tr><th></th>';
		foreach($this->_m_roles as $role)
		{
			$output .= '<th>' . $role->m_name .'</th>';
		}
		
		foreach($this->_m_permissions as $perm_resource => $perm_types)
		{
			$output .= '<tr><td>Resource:' . $perm_resource . '</td>';
			foreach($this->_m_roles as $role)
			{
				$output .= '<td></td>';
			}
			
			$output .= '</tr>';
			foreach($perm_types as $perm_typename => $perm_ops)
			{
				$output .= '<tr><td>&nbsp;&nbsp;Resource Type: ' . $perm_typename . '</td>';
				foreach($this->_m_roles as $role)
				{
					$output .= '<td></td>';
				}
				$output .= '</tr>';
				foreach($perm_ops as $perm_op)
				{
					$output .= '<tr><td>&nbsp;&nbsp;&nbsp;&nbsp;Operation: '.$perm_op['operation'].'</td>';
					foreach($this->_m_roles as $role)
					{
						$checked = xAccessPermission::checkPermission($perm_resource,$perm_typename,
							$perm_op['operation'],$role->m_name);
						$output .= '<td>';
						$check = new xFormElementCheckbox('permission['.$perm_resource.
							']['.$perm_op['operation'].']['.$perm_typename.']['.$role->m_name.']',
							'','',1,$checked,FALSE,new xInputValidatorInteger());
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



?>

