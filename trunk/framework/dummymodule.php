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
	 * Must return an array describing a widget (or an array of such arrays)
	 * <br> Called with xModuleManager::invoke();
	 * 
	 * @return array() An array so composed: 
	 * <code>
	 * array(
	 * 		'class name' => [class name],
	 * 		'widget name' => [widget name],
	 * 		'description' => [description]
	 * 		)
	 * </code>
	 * Called with xModuleManager::invokeAll();
	 */
	function xm_declareWidget()
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
	 * This method is called after module load and init.
	 * <br> Called with xModuleManager::invokeAll();
	 * 
	 * @return NULL
	 */
	function xm_initUtilities()
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
	 * This method is called to finalize all utilities.
	 * <br> Called with xModuleManager::invokeAll();
	 * 
	 * @return NULL
	 */
	function xm_finalUtilities()
	{
	}
	
	/**
	 * Returns true if object precontitions are valid, in this case 
	 * all other preconditions are ignored.
	 * 
	 * <br> Called with xModuleManager::invoke();
	 * @return mixed Returns true on success (this method should not return an xError).
	 */
	function xm_checkPreconditionsExclusive(&$renderable_obj)
	{	
	}
	
	
	/**
	 * Returns true if object precontitions are valid, in this case 
	 * other modules precontitions are checked.
	 * 
	 * <br> Called with xModuleManager::invokeAll();
	 * @return mixed Returns true on valid preconditions, false if the creation workflow
	 * should fail silently, an xError object on fatal error.
	 */
	function xm_checkPreconditionsInclusive(&$renderable_obj)
	{
	}
	
	
	/**
	 * Renders a renderable object.
	 * 
	 * @return string Returns the rendered element or a xError object on error.
	 */
	function xm_render(&$renderable_obj)
	{
	}
	
	/**
	 * Pre-process the contents of a renderable object.
	 * 
	 * @return NULL 
	 */
	function xm_preprocess(&$renderable_obj)
	{
	}
	
	
	/**
	 * Post-process the contents of a renderable object.
	 * 
	 * @return NULL
	 */
	function xm_postprocess(&$renderable_obj)
	{
	}
	
	
	/**
	 * Pre-filter the contents of a renderable object.
	 * 
	 * @return NULL 
	 */
	function xm_prefilter(&$renderable_obj)
	{
	}
	
	
	/**
	 * Post-filter the contents of a renderable object.
	 * 
	 * @return NULL
	 */
	function xm_postfilter(&$renderable_obj)
	{
	}
}



?>
