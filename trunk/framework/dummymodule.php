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
 * A dummy module class for documentation purpose only.
 */
class xDummyModule extends xModule
{
	/**
	 * This method should executes all sql queries needed to install a module.
	 * 
	 * @return NULL
	 */
	function xm_install($db_name)
	{
	}
	
	
	/**
	 * This method is called to create the full web page.
	 * <br> Called with xModuleManager::invoke();
	 * 
	 * @return NULL
	 */
	function xm_createPage(&$path)
	{
	}
	
	/**
	 * This method is called after module load.
	 * <br> Called with xModuleManager::invokeAll();
	 * 
	 * @return NULL
	 */
	function xm_initModules()
	{
	}

	
	/**
	 * This method is called to finalize all modules.
	 * <br> Called with xModuleManager::invokeAll();
	 * 
	 * @return NULL
	 */
	function xm_finalModules()
	{
	}
	
	
	/**
	 * Return the specified dao for the specified db type.
	 */
	function xm_fetchDAO($db_type,$name)
	{
	}
	
	
	/**
	 * Called on component init, returns a component extension or null
	 * <br> Called with xModuleManager::invokeAll();
	 * 
	 * @return xComponentExtension
	 */
	function xm_componentExtensions(&$component)
	{
	}
	
	
	/**
	 * Called on component init, returns a list of extension to block in the provided
	 * component
	 * <br> Called with xModuleManager::invokeAll();
	 * 
	 * @return string
	 */
	function xm_componentBlockedExtensions(&$component)
	{
	}
}



?>