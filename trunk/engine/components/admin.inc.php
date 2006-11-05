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
* A module for tests
*/
class xModuleAdmin extends xModule
{
	function xModuleAdmin()
	{
		$this->xModule();
	}


	/**
	 * @see xDummyModule::xm_fetchContent()
	 */ 
	function xm_fetchContent($path)
	{
		if($path->m_resource === "admin" && $path->m_type == NULL && $path->m_action == NULL)
		{
			return new xResult(new xPageContentAdmin($path));
		}
		
		if($path->m_resource === "admin" && $path->m_type == NULL && $path->m_action == NULL)
		{
			return new xResult(new xPageContentAdmin($path));
		}
		
		return NULL;
	}
	
	/**
	 * @see xDummyModule::xm_fetchPermissionDescriptors()
	 */ 
	function xm_fetchPermissionDescriptors()
	{
		$descrs = array();
		$descrs[] = new xAccessPermissionDescriptor('admin',NULL,NULL,'view','View administration area');
		
		$filters = xContentFilterController::getAllFilters();
		foreach($filters as $filter)
		{
			$descrs[] = new xAccessPermissionDescriptor('filter',$filter["name"],NULL,'use','Use "'.$filter["name"].'" content filter');
		}
		
		return new xResult($descrs);
	}
	
};

xModule::registerDefaultModule(new xModuleAdmin());





/**
 *
 */
class xPageContentAdmin extends xPageContent
{

	function xPageContentAdmin($path)
	{
		xPageContent::xPageContent($path);
	}
	
	// DOCS INHERITHED  ========================================================
	function onCheckPreconditions()
	{
		//only if administrator!
		if(xUser::currentHaveRole('administrator'))
		{
			return TRUE;
		}
		
		$redirect = new xJavaScriptRedirect('user/login',true,3);
		$extra_out = $redirect->render() . '<br>Redirecting in 3 seconds';
		return new xPageContentNotAuthorized($this->m_path,$extra_out);
	}
	
	// DOCS INHERITHED  ========================================================
	function onCreate()
	{
		$content = 
			'<a href="'.xanth_relative_path('admin/accesspermissions/view').'">Access Permissions</a>
			<br/><a href="'.xanth_relative_path('node/create').'">Create node</a>
			<br/><a href="'.xanth_relative_path('admin/box/create/custom').'">Create custom box</a>';
			
		xPageContent::_set("Xanthin+ Administration Area",$content,'','');
		return true;
	}
	
}
	
?>
