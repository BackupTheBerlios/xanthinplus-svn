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

$g_xanth_builtin_modules = array();
$g_xanth_modules = array();

/**
* The base class for modules.
* 
* See xDummyModule for a list of methods you can implement to respond to various events/request.
*/
class xModule
{
	/**
	 *
	 */
	function xModule()
	{
	}
	
	//----------------STATIC FUNCTIONS----------------------------------------------
	//----------------STATIC FUNCTIONS----------------------------------------------
	//----------------STATIC FUNCTIONS----------------------------------------------
	
	/**
	* Register a module.
	*
	* @param xModule $module The module to register.
	* @internal
	* @static
	*/
	function registerDefaultModule($module)
	{
		global $g_xanth_builtin_modules;
		$g_xanth_builtin_modules[] = $module;
	}
	
	
	/**
	* Retrieve all registered modules as an array.
	*
	* @return array(xModule) all registered modules.
	* @internal
	* @static
	*/
	function getDefaultModules()
	{
		global $g_xanth_builtin_modules;
		return $g_xanth_builtin_modules;
	}
	
	/**
	* Register a module.
	*
	* @param xModule $module The module to register.
	* @static
	*/
	function registerModule($module)
	{
		global $g_xanth_modules;
		$g_xanth_modules[] = $module;
	}
	
	
	/**
	* Retrieve all registered modules as an array.
	*
	* @return array(xModule) all registered modules.
	* @static
	*/
	function getModules()
	{
		global $g_xanth_modules;
		return $g_xanth_modules;
	}
	
	
	/**
	 * Make a method call to all modules until a not-NULL result is returned.
	 *
	 * @param string $function The method to call
	 * @param args An array containing the arguments to pass to the function
	 * @return xResult
	 */
	function invoke($function,$args = array())
	{
		//first user modules then default modules
		$modules = array_merge(xModule::getModules(),xModule::getDefaultModules());
		
		foreach($modules as $module)
		{
			if(method_exists($module,$function))
			{
				$result = call_user_func_array(array(&$module,$function),$args);
				if($result !== NULL)
				{
					if(xanth_instanceof($result,'xResult'))
						return $result;
					else
						xLog::log(LOG_LEVEL_WARNING,'Module function returned an invalid result. Function: '.
							$function . '. Module: '. get_class($module) . '. Result dump :' 
							. var_export($result,true),__FILE__,__LINE__);
				}
			}
		}
		
		return NULL;
	}
	
	
	/**
	 * Make a method call to all modules.
	 *
	 * @param string $function The method to call
	 * @param args An array containing the arguments to pass to the function
	 * @return xResultSet
	 */
	function invokeAll($function,$args = array())
	{
		//first user modules then default modules
		$modules = array_merge(xModule::getModules(),xModule::getDefaultModules());
		$rs = new xResultSet();
		
		foreach($modules as $module)
		{
			if(method_exists($module,$function))
			{
				$result = call_user_func_array(array(&$module,$function),$args);
				if($result !== NULL)
				{
					if(xanth_instanceof($result,'xResult'))
						$rs->m_results[] = $result;
					else
						xLog::log(LOG_LEVEL_WARNING,'Module function returned an invalid result. Function: '.
							$function . '. Module: '. get_class($module) . '. Result dump :' 
							. var_export($result,true),__FILE__,__LINE__);
				}
			}
		}
		
		return $rs;
	}
};

/**
 * A dummy module class for documentation purpose only.
 */
class xDummyModule extends xModule
{
	/**
	* This method should executes all sql queries needed to install a module in a mysql db.
	*/
	function xm_installDBMySql()
	{
	}
	
	/**
	* Returns a valid content for the given path. Note that you SHOULD NOT call onCheckPrecontitions() or
	* call onCreate() on the content object before you return it.
	*
	* @param xPath $path
	* @return xPageContent A valid xPageContent object for the given path, NULL otherwise.
	*/
	function xm_fetchContent($path)
	{
	}
	
	
	/**
	* Returns a valid box corresponding to the given name/type
	*
	* @param xPath $path
	* @return xPageContent A valid xPageContent object for the given path, NULL otherwise.
	*/
	function xm_fetchBuiltinBox($box_name,$lang)
	{
	}
	
	/**
	* Called when the page creation occur. Use this method to do all the stuff befor a the page is created.
	*
	* @param xPath $path
	*/
	function xm_onPageCreation($path)
	{
	}
	
	/**
	* Returns a single permission descriptor or array of permission descriptor that corresponds to
	* all access permissions that a module uses.
	*
	* @return xAccessPermissionDescriptor
	*/
	function xm_fetchPermissionDescriptors()
	{
	}
	
	/**
	 * Called after framework initialization but before page fetching.
	 * 
	 */
	function xm_onInit()
	{
	}
}


?>
