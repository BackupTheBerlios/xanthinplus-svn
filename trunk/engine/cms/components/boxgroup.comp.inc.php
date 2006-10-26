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
 * A module for box
 */
class xModuleBoxGroup extends xModule
{
	function xModuleBoxGroup()
	{
		xModule::xModule();
	}


	/**
	 * @see xDummyModule::xm_fetchContent()
	 */ 
	function xm_fetchContent($path)
	{
		if($path->m_resource === 'boxgroup' && $path->m_action === 'admin' && $path->m_type === NULL)
		{
			return new xPageContentBoxGroupAdmin($path);
		}
		
		return NULL;
	}
	
	/**
	 * @see xDummyModule::xm_fetchPermissionDescriptors()
	 */ 
	function xm_fetchPermissionDescriptors()
	{
		/*
		$descrs = array();
		$descr[] = new xAccessPermissionDescriptor('admin/box',NULL,NULL,'create','Create a custom box of any type');
		return $descrs;
		*/
	}
};

xModule::registerDefaultModule(new xModuleBoxGroup());




/**
 *
 */
class xPageContentBoxGroupAdmin extends xPageContent
{

	function xPageContentBoxAdmin($path)
	{
		xPageContent::xPageContent($path);
	}
	
	// DOCS INHERITHED  ========================================================
	function onCheckPreconditions()
	{
		if(!xAccessPermission::checkCurrentUserPermission('boxgroup',NULL,NULL,'admin'))
			return new xPageContentNotAuthorized($this->m_path);
			
		return true;
	}
	
	
	// DOCS INHERITHED  ========================================================
	function onCreate()
	{
		$groups = xBoxGroup::find(NULL,FALSE);
		
		$out = '<div class = "admin"><table>';
		$out .= '<tr><th>Name</th><th>Render?</th><th>Description</th></tr>';
		foreach($groups as $group)
		{
			$out  .= '<tr><td>'.$group->m_name.'</td><td>';
			if($group->m_render)
				$out  .= 'TRUE';
			else
				$out  .= 'FALSE';
			
			$out  .= '</td><td>'.$group->m_description.'</td></tr>';
		}
		
		$out  .= "</table></div>\n";
		
		xPageContent::_set("Admin box groups",$out,'','');
		return true;
	}
}

	
?>
