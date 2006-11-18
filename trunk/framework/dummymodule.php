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
	function xh_install($db_name)
	{
	}
	
	
	/**
	 * This method is called to create the full web page.
	 * <br> Called with xModuleManager::invoke();
	 * 
	 * @return NULL
	 */
	function xh_createDocument()
	{
	}
	
	/**
	 * This method is called after module load.
	 * <br> Called with xModuleManager::invokeAll();
	 * 
	 * @return NULL
	 */
	function xh_initModules()
	{
	}

	
	/**
	 * This method is called to finalize all modules.
	 * <br> Called with xModuleManager::invokeAll();
	 * 
	 * @return NULL
	 */
	function xh_finalModules()
	{
	}
	
	
	/**
	 * Return the specified dao for the specified db type.
	 * <br> Called with xModuleManager::invoke();
	 * 
	 * @return object
	 */
	function xh_fetchDAO($db_type,$name)
	{
	}
	
	
	/**
	 * Returns a xFilter object for the given filter name.
	 * <br> Called with xModuleManager::invoke();
	 * @return object 
	 */
	function xh_fetchFilter($filter_name)
	{
	}
	
	
	/**
	 * Returns a string or an array of strings representing available filters name
	 * <br> Called with xModuleManager::invokeAll();
	 * @return mixed
	 */
	function xh_listFilters()
	{
	}
	
	
	/**
	 * Returns the absolute path to template that maps the given name.
	 * <br> Called with invoke()
	 * 
	 * @return string
	 */
	function xh_templateMapping($name)
	{
	}
	
	/**
	 * @return void
	 */
	function xh_filterComponentView(&$component_view)
	{
	}
	
	/**
	 * @return void
	 */
	function xh_initComponentController(&$component)
	{}
	
	/**
	 * @return void
	 */
	function xh_authComponentController(&$component)
	{}
	
	/**
	 * @return void
	 */
	function xh_processComponentController(&$component)
	{}
	
	/**
	 * @return void
	 */
	function xh_preprocessComponentController(&$component)
	{}
	
	/**
	 * Return a content component.
	 * Called with invoke().
	 * 
	 * @return NULL
	 */
	function xh_fetchContent(&$path)
	{
	}
	
	
	
	/**
	 * Return a list of raw components to be included in the current document.
	 * Called with invokeAll().
	 * 
	 * @return mixed A named array of xComponent objects.
	 */
	function xh_documentComponents(&$path)
	{
	}
	
	/**
	 * Return a list of string representing the relative path to a css file.
	 * Called with invokeAll().
	 * 
	 * @return mixed A named array of xComponent objects.
	 */
	function xh_documentStylesheets(&$path)
	{
	}
}



?>