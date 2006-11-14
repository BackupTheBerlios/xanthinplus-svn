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
class xModule extends xObject
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
	function __construct($weight,$description,$authors,$version)
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
class xModuleManager extends xObject
{
	var $m_modules = array();
	
	/**
	 * 
	 * @param string $search_dir The directory to search for modules
	 * @param string $suffix The suffix of the module file (eg. modulename.[suffix].php)
	 */
	function __construct()
	{
	}
	
	/**
	 * @param string $search_dir The relative path to search
	 * @return array An array of strings representing modules relative paths
	 * @static
	 */
	function findAllModules($search_dir,$suffix)
	{
		$full_search_dir = x_full_path($search_dir);
		$ret = array();
		if($handle = opendir($full_search_dir)) 
		{
			while(false !== ($file = readdir($handle))) 
			{
				if($file != "." && $file != "..")
				{
					//search for spare file module
					$mod_file = $search_dir .'/'.$file.'.'.$suffix.'.php';
					if(is_file($mod_file))  
					{
						$ret[] = $mod_file;
					}
					//search for file module inside directories
					else			
					{
						$mod_file = $search_dir .'/'. $file . '/'.$file.'.'.$suffix.'.php';
						if(is_file($mod_file))
							$ret[] = $mod_file;
					}
				}
			}
			closedir($handle);
		}
		else
			xLog::log('Framework',LOG_LEVEL_ERROR,'Invalid search directory for modules. Dump: '.
				var_export($full_search_dir,true),__FILE__,__LINE__);
				
		return $ret;
	}
	
	
	/**
	 * @todo Resolv dao
	 * @static
	 */
	function initModules($search_dir,$suffix,$enabled,$installed,$additionalModules = array())
	{
		if($enabled || $installed)
		{
			$modules = array();
			$dao =& x_getDAO('module');
			$dtos = $dao->find(NULL,$enabled,$installed);
			foreach($dtos as $dto)
				$modules[] = $dto->m_path;
		}
		else
			$modules = xModuleManager::findAllModules($search_dir,$suffix);
		
		foreach($modules as $module)
		{
			if(strpos($module->m_path,$this->m_search_dir) === 0)
			{
				if(is_file($module->m_path))
				{
					$name = basename($module->m_path,'.'.$suffix.'.php');
					include_once($module->m_path);
					$mod = call_user_func('xm_load_' . $name);
					
					if(xanth_instanceof($mod,'xModule'))
						$this->m_modules[] = $mod;
				}
			}
		}
		
		$this->m_modules = array_merge($this->m_modules,$additionalModules);
		$this->sort();
	}
	
	
	/**
	 * 
	 */
	function sort()
	{
		usort($this->m_modules,'x_objWeightCompare');
	}
	
	/**
	 * 
	 */
	function merge($other_module_manager)
	{
		$this->m_modules = array_merge($this->m_modules,$other_module_manager->m_modules);
		$this->sort();
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
		foreach($this->m_modules as $module)
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
		$rs = new xResultSet();
		foreach($this->m_modules as $module)
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
class xResultSet extends xObject
{
	var $m_results;
	
	/**
	 * 
	 */
	function __construct($results = array())
	{
		parent::__construct();
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