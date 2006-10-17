﻿<?php
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
	 * Make a method call to all modules and return the first result !== NULL (0 argument version).
	 *
	 * @param string $function The method to call
	 * @return mixed
	 */
	function callWithSingleResult0($function)
	{
		//first to user modules then default
		$all_modules = array(xModule::getModules(),xModule::getDefaultModules());
		
		foreach($all_modules as $modules)
		{
			foreach($modules as $module)
			{
				if(method_exists($module,$function))
				{
					$result = $module->$function();
					if($result !== NULL)
					{
						return $result;
					}
				}
			}
		}
		
		return NULL;
	}
	
	/**
	 * Make a method call to all modules and return the first result !== NULL (1 argument version).
	 *
	 * @param string $function The method to call
	 * @return mixed
	 */
	function callWithSingleResult1($function,&$arg1)
	{
		//first to user modules then default
		$all_modules = array(xModule::getModules(),xModule::getDefaultModules());
		
		foreach($all_modules as $modules)
		{
			foreach($modules as $module)
			{
				if(method_exists($module,$function))
				{
					$result = $module->$function($arg1);
					if($result !== NULL)
					{
						return $result;
					}
				}
			}
		}
		
		return NULL;
	}
	
	/**
	 * Make a method call to all modules and return the first result !== NULL (2 argument version).
	 *
	 * @param string $function The method to call
	 * @return mixed
	 */
	function callWithSingleResult2($function,&$arg1,&$arg2)
	{
		//first to user modules then default
		$all_modules = array(xModule::getModules(),xModule::getDefaultModules());
		
		foreach($all_modules as $modules)
		{
			foreach($modules as $module)
			{
				if(method_exists($module,$function))
				{
					$result = $module->$function($arg1,$arg2);
					if($result !== NULL)
					{
						return $result;
					}
				}
			}
		}
		
		return NULL;
	}
	
	/**
	 * Make a method call to all modules and return the first result !== NULL (3 argument version).
	 *
	 * @param string $function The method to call
	 * @return mixed
	 */
	function callWithSingleResult3($function,&$arg1,&$arg2,&$arg3)
	{
		//first to user modules then default
		$all_modules = array(xModule::getModules(),xModule::getDefaultModules());
		foreach($all_modules as $modules)
		{
			foreach($modules as $module)
			{
				if(method_exists($module,$function))
				{
					
					$result = $module->$function($arg1,$arg2,$arg3);
					if($result !== NULL)
					{
						return $result;
					}
				}
			}
		}
		
		return NULL;
	}
	
	/**
	 * Make a method call to all modules and return an array that is the union
	 * of all results != NULL returned. (0 argument version).
	 *
	 * @param string $function The method to call
	 * @return array(mixed)
	 */
	function callWithArrayResult0($function)
	{
		$array_result = array();
		$modules = array_merge(xModule::getModules(),xModule::getDefaultModules());
		foreach($modules as $module)
		{
			if(method_exists($module,$function))
			{
				$result = $module->$function();
				if($result !== NULL)
				{
					if(is_array($result))
					{
						foreach($result as $one_result)
						{
							$array_result[] = $one_result;
						}
					}
					else
					{
						$array_result[] = $result;
					}
				}
			}
		}
		
		return $array_result;
	}
	
	/**
	 * Make a method call to all modules and return an array that is the union
	 * of all results != NULL returned. (1 argument version).
	 *
	 * @param string $function The method to call
	 * @return array(mixed)
	 */
	function callWithArrayResult1($function,&$arg1)
	{
		$array_result = array();
		$modules = array_merge(xModule::getModules(),xModule::getDefaultModules());
		foreach($modules as $module)
		{
			if(method_exists($module,$function))
			{
				$result = $module->$function($arg1);
				if($result !== NULL)
				{
					if(is_array($result))
					{
						foreach($result as $one_result)
						{
							$array_result[] = $one_result;
						}
					}
					else
					{
						$array_result[] = $result;
					}
				}
			}
		}
		
		return $array_result;
	}
	
	/**
	 * Make a method call to all modules and return an array that is the union
	 * of all results != NULL returned. (2 argument version).
	 *
	 * @param string $function The method to call
	 * @return array(mixed)
	 */
	function callWithArrayResult2($function,&$arg1,&$arg2)
	{
		$array_result = array();
		$modules = array_merge(xModule::getModules(),xModule::getDefaultModules());
		foreach($modules as $module)
		{
			if(method_exists($module,$function))
			{
				$result = $module->$function($arg1,$arg2);
				if($result !== NULL)
				{
					if(is_array($result))
					{
						foreach($result as $one_result)
						{
							$array_result[] = $one_result;
						}
					}
					else
					{
						$array_result[] = $result;
					}
				}
			}
		}
		
		return $array_result;
	}
	
	/**
	 * Make a method call to all modules and return an array that is the union
	 * of all results != NULL returned. (3 argument version).
	 *
	 * @param string $function The method to call
	 * @return array(mixed)
	 */
	function callWithArrayResult3($function,&$arg1,&$arg2,&$arg3)
	{
		$array_result = array();
		$modules = array_merge(xModule::getModules(),xModule::getDefaultModules());
		foreach($modules as $module)
		{
			if(method_exists($module,$function))
			{
				$result = $module->$function($arg1,$arg2,$arg3);
				if($result !== NULL)
				{
					if(is_array($result))
					{
						foreach($result as $one_result)
						{
							$array_result[] = $one_result;
						}
					}
					else
					{
						$array_result[] = $result;
					}
				}
			}
		}
		
		return $array_result;
	}
	
	/**
	 * Make a method call to all modules. (0 argument version).
	 *
	 * @param string $function The method to call
	 */
	function callWithNoResult0($function)
	{
		$modules = array_merge(xModule::getModules(),xModule::getDefaultModules());
		foreach($modules as $module)
		{
			if(method_exists($module,$function))
			{
				$module->$function();
			}
		}
	}
	
	/**
	 * Make a method call to all modules. (1 argument version).
	 *
	 * @param string $function The method to call
	 */
	function callWithNoResult1($function,&$arg1)
	{
		$modules = array_merge(xModule::getModules(),xModule::getDefaultModules());
		foreach($modules as $module)
		{	
			if(method_exists($module,$function))
			{
				$module->$function($arg1);
			}
		}
	}
	
	/**
	 * Make a method call to all modules. (2 argument version).
	 *
	 * @param string $function The method to call
	 */
	function callWithNoResult2($function,&$arg1,&$arg2)
	{
		$modules = array_merge(xModule::getModules(),xModule::getDefaultModules());
		foreach($modules as $module)
		{
			if(method_exists($module,$function))
			{
				$module->$function($arg1,$arg2);
			}
		}
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
	function xm_fetchBox($box_name,$box_type)
	{
	}
	
	/**
	* Returns a valid cathegory corresponding to the given name/type
	*
	* @param xPath $path
	* @return xCathegory A valid xCathegory object for the given path, NULL otherwise.
	*/
	function xm_fetchCathegory($cat_id,$cat_type)
	{
	}
	
	
	/**
	* Returns a valid content for the given alias path. Note that you SHOULD NOT call onCheckPrecontitions() or
	* call onCreate() on the content object before you return it.
	*
	* @param xPath $path
	* @return xPageContent A valid xPageContent object for the given resource/action/id, NULL otherwise.
	*/
	function xm_fetchAliasContent($path)
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
	* Fetch a specific node object corresponding to given type and id.
	*
	* @return xNode
	*/
	function xm_fetchSingleNode($type,$id)
	{
	}
}


?>
