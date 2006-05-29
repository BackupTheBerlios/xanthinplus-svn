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
* Module responsible of box management
*/
class xModuleBox extends xModule
{
	function xModuleBox()
	{
		$this->xModule();
	}
	
	
	// DOCS INHERITHED  ========================================================
	function xm_contentFactory($path)
	{
		if($path->m_base_path == 'admin/box')
		{
			return new xContentAdminBox($path);
		}
		
		return NULL;
	}
};

xModule::registerDefaultModule(new xModuleBox());







/**
 *
 *
 * @internal
 */
class xContentAdminBox extends xContent
{

	function xContentAdminBox($path)
	{
		xContent::xContent($path);
	}
	
	// DOCS INHERITHED  ========================================================
	function onCheckPreconditions()
	{
		return TRUE;
	}
	
	
	// DOCS INHERITHED  ========================================================
	function onCheckPermission()
	{
		return xAccessPermission::checkCurrentUserPermission('box','admin');
	}
	
	
	// DOCS INHERITHED  ========================================================
	function onCreate()
	{
		$boxes = xBox::findAll();
		
		$output = 
		'<table class="admin-table">
		<tr><th>Name</th><th>Title</th><th>Type</th><th>Filter name</th><th>Area</th><th>Operations</th></tr>
		';
		foreach($boxes as $box)
		{
			if(!empty($box->m_filterset))
			{
				$filter = xAccessFilterSet::dbLoad($box->m_filterset);
				$filtername = $filter->m_name;
			}
			else
			{
				$filtername = '[No Filter]';
			}
			
			$output .= '<tr><td>' . $box->m_name . '</td><td>' . $box->m_title . '</td><td>'.
			$box->m_type . '</td><td>' . $filtername . '</td><td>' . $box->m_area . '</td><td>Edit</td></tr>';
		}
		$output .= "</table>\n";
		
		xContent::_set("Manage box",$output,'','');
		return TRUE;
	}
}

	
?>
