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
class xModuleTest extends xModule
{
	function xModuleTest()
	{
		$this->xModule();
	}

	// DOCS INHERITHED  ========================================================
	function xm_contentFactory($path)
	{
		if($path->m_base_path == 'test')
		{
			return new xContentTest($path);
		}
		
		return NULL;
	}
};

xModule::registerDefaultModule(new xModuleTest());







/**
 * @internal
 */
class xContentTest extends xContent
{	
	function xContentTest($path)
	{
		$this->xContent($path);
	}

	// DOCS INHERITHED  ========================================================
	function onCheckPermission()
	{
		return TRUE;
	}
	
	
	// DOCS INHERITHED  ========================================================
	function onCreate()
	{
		xContent::_set("TEST",'(TEST PAGE)','','');
		return TRUE;
	}
};


	
?>
