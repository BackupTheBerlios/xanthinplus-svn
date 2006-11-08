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
 * 
 */
class xModuleDTO
{
	var $m_path;
	var $m_enabled;
	var $m_installed;
	
	function xModuleDTO($path,$enabled,$installed)
	{
		$this->m_path = $path;
		$this->m_enabled = (bool) $enabled;
		$this->m_installed = (bool) $installed;	
	}
}

/**
 * The base class for modules.
 * 
 * See xDummyModule for a list of methods you can implement to respond to various events/request.
 * @package modules
 */
class xModule
{
	/**
	 * @var int
	 */
	var $m_weight;
	
	/**
	 * @ver string
	 */
	var $m_version;
	
	/**
	 * @var string
	 */
	var $m_description;
	
	/**
	 * @var string
	 */
	var $m_authors;
	
	/**
	 * @param int $weight Defines the position of a module during a method invokation.
	 * Modules with higher weights are processed after. Weights between 1000 and -1000 are 
	 * reserved for xanthin default modules.
	 */
	function xModule($weight,$description,$authors,$version)
	{
		$this->m_weight = $weight;
		$this->m_description = $description;
		$this->m_authors = $authors;
		$this->m_version = $version;
	}
}

//###########################################################################
//###########################################################################
//###########################################################################

/**
 * @package modules
 */
class xModuleManager
{
	function xModuleManager()
	{
		assert(false);	
	}
	
	/**
	 * @param string $search_dir The relative path to search
	 * @return array An array of strings representing modules path
	 */
	function findAllModules($search_dir)
	{
		global $xanth_working_dir;
		$search_dir =  $xanth_working_dir . '/' . $search_dir;
		if($handle = opendir($search_dir)) 
		{
			$ret = array();
			while(false !== ($file = readdir($handle))) 
			{
				$mod_file = $search_dir .'/'. $file . '/'.$file.'module.php';
				if($file != "." && $file != ".." && is_file($search_dir .'/'. $file . '/'))
					$ret[] = $mod_file;
			}
			closedir($handle);
		}
		else
			xLog::log(LOG_LEVEL_FATAL_ERROR,'Invalid search directory for modules. Dump: '.
				var_export($search_dir,true),__FILE__,__LINE__);
	}
	
	
	/**
	 * @static
	 */
	function includeModule($search_dir,$name)
	{
		include_once($search_dir . '/' . $name . '/' . $name . '.module.php');
	}
	
	
	/**
	 * @static
	 */
	function initModules($enabled,$installed)
	{
		global $xanth_working_dir;
		if($enabled || $installed)
		{
			$modules = xModuleDAO::find(NULL,$enabled,$installed);
			usort($modules,'objWeightCompare');
			foreach($modules as $module)
			{
				$mod_file = $xanth_working_dir . '/' . $module->m_path . '/' . basename($module->m_path) .
					'.module.php';
				if(is_file($mod_file))
				{
					include_once($mod_file);
					$mod = call_user_func('xm_load_' . basename($module->m_path));
					
					if(xanth_instanceof($mod,'xModule'))
						xModuleManager::registerModule($mod);
				}
			}
		}
	}
	
	
	/**
	 * 
	 */
	function dbUpdateModule($path,$enabled,$installed)
	{
		return xModuleDAO::update($path,$enabled,$installed);
	}
	
	
	/**
	* Register a module.
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
	 * @static
	 * @return mixed
	 */
	function invoke($function,$args = array())
	{
		//first user modules then default modules
		$modules = xModuleManager::getModules();
		
		foreach($modules as $module)
		{
			if(method_exists($module,$function))
			{
				$result = call_user_func_array(array(&$module,$function),$args);
				if($result !== NULL)
					return $result;
			}
		}
		
		return NULL;
	}
	
	
	/**
	 * Make a method call to all modules.
	 *
	 * @param string $function The method to call
	 * @param args An array containing the arguments to pass to the function
	 * @static
	 * @return xResultSet
	 */
	function invokeAll($function,$args = array())
	{
		//first user modules then default modules
		$modules = xModuleManager::getModules();
		$rs = new xResultSet();
		
		foreach($modules as $module)
		{
			if(method_exists($module,$function))
			{
				$result = call_user_func_array(array(&$module,$function),$args);
				if($result !== NULL)
					$rs->m_results[] = $result;
			}
		}
		
		return $rs;
	}
};


//###########################################################################
//###########################################################################
//###########################################################################


/**
 * A set of results
 */
class xResultSet
{
	var $m_results;
	
	function xResultSet($results = array())
	{
		$this->m_results = $results;
	}
	
	
	/**
	 * Returns true if the result set contains no results
	 */
	function isEmpty()
	{
		return empty($this->m_results);
	}
	
	/**
	 * Returns true if the current result set contains errors
	 * 
	 * @return bool
	 */
	function containsErrors()
	{
		foreach($this->m_results as $result)
			if(xError::isError($result))
				true;
		
		return false;
	}
	
	
	/**
	 * Returns true if the current result set contains errors
	 * 
	 * @return bool
	 */
	function containsValue($value)
	{
		foreach($this->m_results as $result)
			if($result === $value)
				true;
		
		return false;
	}
	
	
	/**
	 * Returns an array containing all errors contained in results.
	 */
	function getErrors()
	{
		$ret = array();
		foreach($this->m_results as $result)
			if(xError::isError($result))
				$ret[] = $result;
		
		return $ret;
	}
	
	/**
	 * Returns an array containing all not NULL values contained in a result without errors 
	 * in this result set.
	 * 
	 * @param bool $merge_arrays
	 */
	function getValidValues($merge_arrays = false)
	{
		$ret = array();
		
		if($merge_arrays)
		{
			foreach($this->m_results as $result)
				if(!xError::isError($result))
					if(is_array($result))
						$ret = array_merge($ret,$result);
					else
						$ret[] = $result;
		}
		else
		{
			foreach($this->m_results as $result)
				if(xError::isError($result))
					$ret[] = $result;
		}
		
		return $ret;
	}
}

?>