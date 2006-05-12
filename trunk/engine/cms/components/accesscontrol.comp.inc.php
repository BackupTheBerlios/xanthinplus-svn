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
* Module responsible of access filters and permission
*/
class xModuleAccessControl extends xModule
{
	function xModuleAccessControl()
	{
		$this->xModule('Access Control','engine/cms/components/');
	}
	
	// DOCS INHERITHED  ========================================================
	function getContent($path)
	{
		if($path->m_base_path == 'admin/accessfilters')
		{
			return $this->_getContentManageAccessFilters();
		}
		elseif($path->m_base_path == 'admin/accesspermissions')
		{
			return $this->_getContentManageAccessPermissions();
		}
		
		return NULL;
	}
	
	/**
	 * @access private
	 */
	function _getContentManageAccessFilters()
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
	function _getContentManageAccessPermissions()
	{
		//only if administrator!
		if(!xUser::currentHaveRole('administrator'))
		{
			return new xContentNotAuthorized();
		}
		
		$permissions = xAccessPermission::findAll();
		
		$output = 
		'<table class="admin-table">
		<tr><th>Permission name</th><th>Filter set</th><th>Operations</th></tr>
		';
		foreach($permissions as $permission)
		{
			$filterset = xAccessFilterSet::dbLoad($permission->m_filterset);
			
			$output .= '<tr><td>' . $permission->m_name . '</td><td>' . $filterset->m_name . '</td>';
			$output .= '<td>Edit</td></tr>';
		}
		$output .= "</table>\n";
		
		return new xContentSimple("Manage Access Permissions",$output,'','');
	}
	
	
	// DOCS INHERITHED  ========================================================
	function getMenuItem($box_name)
	{
		if($box_name == 'Admin')
		{
			return array(
				new xMenuItem('Manage Access Filters','?p=admin/accessfilters'),
				new xMenuItem('Manage Access Permissions','?p=admin/accesspermissions')
			);
		}
		
		return NULL;
	}
};

xModule::registerModule(new xModuleAccessControl());

	
?>

