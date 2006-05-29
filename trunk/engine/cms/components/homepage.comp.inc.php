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
* The module responsible of homepage.
*/
class xModuleHomepage extends xModule
{
	function xModuleHomepage()
	{
		$this->xModule();
	}

	// DOCS INHERITHED  ========================================================
	function xm_contentFactory($path)
	{
		if($path->m_base_path == '')
		{
			return new xContentHomepage($path);
		}
		
		return NULL;
	}
};

xModule::registerDefaultModule(new xModuleHomepage());





/**
 * @internal
 */
class xContentHomepage extends xContent
{	
	function xContentHomepage($path)
	{
		$this->xContent($path);
	}

	// DOCS INHERITHED  ========================================================
	function onCheckPreconditions()
	{
		return TRUE;
	}
	
	// DOCS INHERITHED  ========================================================
	function onCheckPermission()
	{
		return TRUE;
	}
	
	
	// DOCS INHERITHED  ========================================================
	function onCreate()
	{
		xContent::_set("Homepage",'Welcome to Xanthin+ CMS!','','');
		return TRUE;
	}
};



	
?>
