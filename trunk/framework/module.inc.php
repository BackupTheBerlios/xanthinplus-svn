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
	var $m_modules = array();
	
	var $m_search_dir = '';
	
	var $m_suffix = '';
	
	var $m_full_search_dir = '';
	
	/**
	 * 
	 * @param string $search_dir The directory to search for modules
	 * @param string $suffix The suffix of the module file (eg. modulename.[suffix].php)
	 */
	function xModuleManager($search_dir, $suffix)
	{
		$this->m_search_dir = $search_dir;
		$this->m_suffix = $suffix;
		$this->m_full_search_dir = x_full_path($search_dir); 
	}
	
	/**
	 * @param string $search_dir The relative path to search
	 * @return array An array of strings representing modules full paths
	 */
	function findAllModules()
	{
		if($handle = opendir($this->m_full_search_dir)) 
		{
			$ret = array();
			while(false !== ($file = readdir($handle))) 
			{
				if($file != "." && $file != "..")
				{
					$mod_file = $this->m_full_search_dir .'/'. $file . '/'.$file.'.'.$this->m_suffix.'.php';
					if(is_file($mod_file))
						$ret[] = $mod_file;
				}
			}
			closedir($handle);
		}
		else
			xLog::log('Framework',LOG_LEVEL_ERROR,'Invalid search directory for modules. Dump: '.
				var_export($this->m_full_search_dir,true),__FILE__,__LINE__);
	}
	
	
	/**
	 * @static
	 */
	function includeModule($name)
	{
		include_once($this->m_full_search_dir . '/' . $name . '/' . $name . '.'.$this->m_suffix.'.php');
	}
	
	
	/**
	 * @static
	 */
	function initModules($enabled,$installed)
	{
		if($enabled || $installed)
		{
			$modules = xModuleDAO::find(NULL,$enabled,$installed);
			usort($modules,'x_objWeightCompare');
			foreach($modules as $module)
			{
				if(basename($module->m_path) == $this->m_search_dir)
				{
					$name = basename($module->m_path);
					$mod_file = x_full_path($module->m_path) . '/' . $name .
						'.' . $this->m_suffix.'.php';
					if(is_file($mod_file))
					{
						include_once($mod_file);
						$mod = call_user_func('xm_load_' . $name);
						
						if(xanth_instanceof($mod,'xModule'))
							$this->m_modules = $mod;
					}
				}
			}
		}
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