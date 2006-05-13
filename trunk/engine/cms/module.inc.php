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
* The base class for modules.
* 
* See xDummyModule for a list of methods you can implement to respond to various events/request.
*/
class xModule
{
	/**
	* @var string
	* @access public
	*/
	var $m_name;
	
	/**
	* Relative path to the xanthine directory
	*
	* @var string
	* @access public
	*/
	var $m_path;
	
	/**
	*
	* @param string $name
	* @param string $path Relative path to the xanthine directory
	*/
	function xModule($name,$path)
	{
		$this->m_name = $name;
		$this->m_path = $path;
	}
	
	/**
	* This method should executes all sql queries needed to install a module in a mysql db.
	*/
	function installDBMySql()
	{
		return NULL;
	}
	
	/**
	* Returns a valid xContent for the passed path
	*
	* @param xXanthPath $path
	* @return xContent A valid xContent object if your module is the responsable of the given path, NULL otherwise.
	*/
	function getContent($path)
	{
		return NULL;
	}
	
	/**
	* Called when the page creation occur. Use this method to do all the stuff befor a the page is created.
	*
	* @param xXanthPath $path
	*/
	function onPageCreation($path)
	{
		return NULL;
	}
	
	/**
	* Returns a dinamic box.
	*
	* @param xBox $box
	* @return xBoxDynamic
	*/
	function getDynamicBox($box)
	{
		return NULL;
	}
	
	/**
	 * Returns a specific xBox child object corresponding to the specified type.
	 *
	 * @param xBox $box
	 * @return xBox
	 */
	function convertFromSimpleBox($box)
	{
		return NULL;
	}
	
	//----------------STATIC FUNCTIONS----------------------------------------------
	//----------------STATIC FUNCTIONS----------------------------------------------
	//----------------STATIC FUNCTIONS----------------------------------------------
	
	/**
	* Register a module.
	*
	* @param xModule $module The module to register.
	* @static
	*/
	function registerModule($module)
	{
		global $g_modules;
		if(!isset($g_modules))
		{
			$g_modules = array();
		}
		
		$g_modules[] = $module;
	}
	
	
	/**
	* Retrieve all registered modules as an array.
	*
	* @return array(xModule) all registered modules.
	* @static
	*/
	function getModules()
	{
		global $g_modules;
		if(!isset($g_modules))
		{
			$g_modules = array();
		}
		
		return $g_modules;
	}
	
	
	/**
	 * Make a method call to all modules and return the first result !== NULL (0 argument version).
	 *
	 * @param string $function The method to call
	 * @return mixed
	 */
	function callWithSingleResult0($function)
	{
		$result = NULL;
		$modules = xModule::getModules();
		foreach($modules as $module)
		{
			$result = $module->$function();
			if($result !== NULL)
			{
				return $result;
			}
		}
		
		return $result;
	}
	
	/**
	 * Make a method call to all modules and return the first result !== NULL (1 argument version).
	 *
	 * @param string $function The method to call
	 * @return mixed
	 */
	function callWithSingleResult1($function,&$arg1)
	{
		$result = NULL;
		$modules = xModule::getModules();
		foreach($modules as $module)
		{
			$result = $module->$function($arg1);
			if($result !== NULL)
			{
				return $result;
			}
		}
		
		return $result;
	}
	
	/**
	 * Make a method call to all modules and return the first result !== NULL (2 argument version).
	 *
	 * @param string $function The method to call
	 * @return mixed
	 */
	function callWithSingleResult2($function,&$arg1,&$arg2)
	{
		$result = NULL;
		$modules = xModule::getModules();
		foreach($modules as $module)
		{
			$result = $module->$function($arg1,$arg2);
			if($result !== NULL)
			{
				return $result;
			}
		}
		
		return $result;
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
		$modules = xModule::getModules();
		foreach($modules as $module)
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
		$modules = xModule::getModules();
		foreach($modules as $module)
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
		
		return $array_result;
	}
	
	/**
	 * Make a method call to all modules and return an array that is the union
	 * of all results != NULL returned. (0 argument version).
	 *
	 * @param string $function The method to call
	 * @return array(mixed)
	 */
	function callWithArrayResult2($function,&$arg1,&$arg2)
	{
		$array_result = array();
		$modules = xModule::getModules();
		foreach($modules as $module)
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
		
		return $array_result;
	}
	
	/**
	 * Make a method call to all modules. (0 argument version).
	 *
	 * @param string $function The method to call
	 */
	function callWithNoResult0($function)
	{
		$modules = xModule::getModules();
		foreach($modules as $module)
		{
			$module->$function();
		}
	}
	
	/**
	 * Make a method call to all modules. (1 argument version).
	 *
	 * @param string $function The method to call
	 */
	function callWithNoResult1($function,&$arg1)
	{
		$modules = xModule::getModules();
		foreach($modules as $module)
		{
			$module->$function($arg1);
		}
	}
	
	/**
	 * Make a method call to all modules. (2 argument version).
	 *
	 * @param string $function The method to call
	 */
	function callWithNoResult2($function,&$arg1,&$arg2)
	{
		$modules = xModule::getModules();
		foreach($modules as $module)
		{
			$module->$function($arg1,$arg2);
		}
	}
	
};



?>
