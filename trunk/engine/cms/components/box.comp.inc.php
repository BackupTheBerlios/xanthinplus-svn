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
		$this->xModule('Box','engine/cms/components/');
	}
	
	// DOCS INHERITHED  ========================================================
	function getContent($path)
	{
		if($path->m_base_path == 'admin/box')
		{
			return $this->_getContentManageBox();
		}
		
		return NULL;
	}
	
	/**
	 * @access private
	 */
	function _getContentManageBox()
	{
		if(!xUser::checkUserAccess('manage box'))
		{
			return new xContentNotAuthorized();
		}
		
		$boxes = xBox::findAll();
		
		$output = 
		'<table class="admin-table">
		<tr><th>Name</th><th>Title</th><th>Type</th><th>Area</th><th>Operations</th></tr>
		';
		foreach($boxes as $box)
		{
			$output .= '<tr><td>' . $box->m_name . '</td><td>' . $box->m_title . '</td><td>'.
			$box->m_type . '</td><td>' . $box->m_area . '</td><td>Edit</td></tr>';
		}
		$output .= "</table>\n";
		
		return new xContentSimple("Manage box",$output,'','');
	}
	
	// DOCS INHERITHED  ========================================================
	function getMenuItem($box_name)
	{
		if($box_name == 'Admin')
		{
			return new xMenuItem('Manage Box','?p=admin/box');
		}
		
		return NULL;
	}
};

xModule::registerModule(new xModuleBox());

	
?>
