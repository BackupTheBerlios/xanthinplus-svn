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
* Module responsible of user management
*/
class xModuleNode extends xModule
{
	function xModuleNode()
	{
		$this->xModule();
	}


	// DOCS INHERITHED  ========================================================
	function xm_fetchContent($resource,$action,$path)
	{
		if($resource === 'node' && $action === 'create')
		{
			return new xPageContentNodeCreate($path);
		}
		elseif($resource === 'node' && $action === 'view')
		{
			return new xPageContentNodeView($path);
		}
		
		return NULL;
	}
};

xModule::registerDefaultModule(new xModuleNode());




/**
 * 
 */
class xPageContentNodeCreate extends xPageContent
{	
	function xPageContentNodeCreate($path)
	{
		$this->xPageContent($path);
	}
	
	// DOCS INHERITHED  ========================================================
	function onCheckPreconditions()
	{
		return TRUE;
	}
	
	
	// DOCS INHERITHED  ========================================================
	function onCreate()
	{
		return new xPageContentSimple('Create Node','Choose node type:','','',$this->m_path);
	}
};


/**
 * 
 */
class xPageContentNodeView extends xPageContent
{	
	function xPageContentNodeView($path)
	{
		$this->xPageContent($path);
	}
	
	/**
	 * Checks that node exists, checks cathegory and type view permission.
	 */
	function onCheckPreconditions()
	{
		//return xAccessPermission::checkCurrentUserPermission('node',NULL,NULL,'view');
	}
	
	
	/**
	 * Do nothing, must override.
	 */
	function onCreate()
	{
		return true;
	}
};
	
?>
