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


	// DOCS INHERITHED  ========================================================
	function xm_fetchContent($resource,$action,$path)
	{
		if($resource === "admin" && empty($action))
		{
			return new xPageContentAdmin($path);
		}
		
		return NULL;
	}
	
	/**
	 * @see xDummyModule::fetchPermissionDescriptors()
	 */ 
	function xm_fetchPermissionDescriptors()
	{
		return new xAccessPermissionDescriptor('admin',NULL,NULL,'view','View administration area');
	}
	
};

xModule::registerDefaultModule(new xModuleAdmin());


/**
 *
 *
 * @internal
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
		$content = '<a href="?p=admin/accesspermissions/view">Access Permissions</a>';
		xPageContent::_set("Xanthin+ Administration Area",$content,'','');
		return true;
	}
	
}
	
?>
