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
 * An object to tell module manager to bypass successive hook invocation in invokeAll function.
 */
class xBypassObject extends xObject
{
	var $m_data = NULL;
	
	/**
	 * 
	 */
	function __construct($data)
	{
		parent::__construct();
		$this->m_data = $data;
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
	function __construct($description,$authors,$version)
	{
		$this->m_description = $description;
		$this->m_authors = $authors;
		$this->m_version = $version;
	}
	
	
	/**
	 * Return an associative array containing
	 * 
	 * @return array
	 */
	function registerHooks(&$module_manager)
	{
	}
}


//###########################################################################
//###########################################################################
//###########################################################################

/**
 * 
 */
function x_hook_compare($a, $b)
{
    if ($a[2] == $b[2]) 
        return 0;
	
    return ($a[2] < $b[2]) ? -1 : 1;
}


/**
 * @package modules
 */
class xModuleManager extends xObject
{
	var $m_modules = array();
	
	var $m_hooks = array();
	
	/**
	 * 
	 * @param string $search_dir The directory to search for modules
	 * @param string $suffix The suffix of the module file (eg. modulename.[suffix].php)
	 */
	function __construct()
	{
		parent::__construct();
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
					if(preg_match("#.\.".$suffix."\.php#i",$file))
					{
						$mod_file = $search_dir.'/'.$file;
						$ret[] = $mod_file;
					}
					//search for file module inside directory
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
	 * @todo Fix dao problem
	 * 
	 * @param array $search_param An array of arrays with such a structure
	 * <code>
	 * array(
	 * 		'search dir' => [search dir]
	 * 		'suffix' => [suffix]
	 * 		'enabled' => [enabled]
	 * 		'installed' => [installed]
	 * )
	 * </code> 
	 * @static
	 */
	function initModules($search_params,$additionalModules = array())
	{
		foreach($additionalModules as $k => $v)
		{
			$additionalModules[$k]->registerHooks($this);
			$this->m_modules[] =& $additionalModules[$k];
		}
		
		foreach($search_params as $param)
		{
			if($param['enabled'] || $param['installed'])
			{
				$modules = array();
				$dao =& new xModuleDAO();
				$dtos = $dao->find(NULL,$param['enabled'],$param['installed']);
				foreach($dtos as $dto)
					$modules[] = $dto->m_path;
			}
			else
				$modules = xModuleManager::findAllModules($param['search dir'],$param['suffix']);
			
			foreach($modules as $module)
			{
				if(strpos($module,$param['search dir']) !== 0)
					continue;
				
				if(! is_file($module))
					continue;
						
				$name = basename($module,'.'.$param['suffix'].'.php');
				include_once($module);
				
				if(function_exists('xm_load_module_' . $name))
				{
					$mod = call_user_func('xm_load_module_' . $name);
					if(!is_array($mod))
						$mod = array($mod);
					
					foreach($mod as $m)
					{
						if(xanth_instanceof($m,'xModule'))
						{
							$m->registerHooks($this);
							$this->m_modules[] =& $m;
						}
					}
				}
			}//foreach modules
		}//foreach search params
		
		$this->sortHooks();
	}
	
	
	/**
	 * 
	 */
	function sortHooks()
	{
		foreach($this->m_hooks as $k => $v)
			usort($this->m_hooks[$k],'x_hook_compare');
	}
	
	
	/**
	 * 
	 */
	function registerHook(&$module,$hook_name,$function,$priority = 1)
	{
		$this->m_hooks[$hook_name][] = array(&$module,$function,$priority); 
	}
	
	
	/**
	 * Make a method call to all modules until a not-NULL result is returned.
	 *
	 * @param string $function The method to call
	 * @param args An array containing the arguments to pass to the function
	 * @static
	 * @return mixed
	 */
	function invoke($hook,$args = array())
	{
		if(isset($this->m_hooks[$hook]))
		{
			foreach($this->m_hooks[$hook] as $k => $v)
			{
				$result = call_user_func_array(array(&$this->m_hooks[$hook][$k][0],$this->m_hooks[$hook][$k][1]),
					$args);
				if($result !== NULL)
					return $result;
			}
		}
		
		return NULL;
	}
	
	

	
	/**
	 * Make a method call to all modules. 
	 * <br><strong>Note</strong>: if a xBypassObject is received hook invocation
	 * is immediately interrupted and results returned.
	 *
	 * @param string $function The method to call
	 * @param args An array containing the arguments to pass to the function
	 * @static
	 * @return xResultSet
	 */
	function invokeAll($hook,$args = array())
	{
		$rs = new xResultSet();
		
		if(isset($this->m_hooks[$hook]))
		{
			foreach($this->m_hooks[$hook] as $k => $v)
			{
				$result = call_user_func_array(array(&$this->m_hooks[$hook][$k][0],$this->m_hooks[$hook][$k][1]),
					$args);
				
				if(xanth_instanceof($result,'xBypassObject'))
				{
					if($result->m_data !== NULL)
						$rs->m_results[] = $result->m_data;
					break;
				}
				elseif($result !== NULL)
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