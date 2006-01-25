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


//------------------------------------------------------------------------------------------------------------------//
// Modules functions
//------------------------------------------------------------------------------------------------------------------//

class xanthModule
{
	var $path;
	var $name;
	
	function xanthModule($path,$name)
	{
		$this->path = $path;
		$this->name = $name;
	}
};


/**
 * Returns an array of xanthModule objects  representing all existing modules \n
 */
function xanth_list_existing_modules()
{
	$modules = array();
	
	//read builtin directory
	$dir = './engine/modules/';
	$dir_list = xanth_list_dirs($dir);
	if(is_array($dir_list))
	{
		foreach($dir_list as $raw_module)
		{
			$modules[] = new xanthModule($raw_module['path'],$raw_module['name']);
		}
    }
	else
	{
		xanth_log(LOG_LEVEL_FATAL_ERROR,"Builtin module directory $dir not found","Core",__FILE__,__LINE__);
	}
	
	
	//read additional module directory
	$dir = './modules/';
	$dir_list = xanth_list_dirs($dir);
	if(is_array($dir_list))
	{
        foreach($dir_list as $raw_module)
		{
			$modules[] = new xanthModule($raw_module['path'],$raw_module['name']);
		}
    }
	else
	{
		xanth_log(LOG_LEVEL_FATAL_ERROR,"Module directory $dir not found","Core",__FILE__,__LINE__);
	}
	
	return $modules;
}

/**
*
*/
function xanth_list_enabled_modules()
{
	$enabled_mod = array();
	foreach(xanth_list_existing_modules() as $module)
	{
		$result = xanth_db_query("SELECT enabled FROM modules WHERE name = '%s'",$module->name);
		if($row = xanth_db_fetch_array($result))
		{
			if($row['enabled'] !== 0)
			{
				$enabled_mod[] = $module;
			}
		}
	}
	return $enabled_mod;
}

/**
*
*/
function xanth_module_exists($module)
{
	return is_dir($module->path . $module->name);
}

/**
*
*/
function xanth_enable_module($module)
{
	if(xanth_module_exists($module))
	{
		$result = xanth_db_query("SELECT enabled FROM modules WHERE name = '%s'",$module->name);
		if($row = xanth_db_fetch_array($result))
		{
			if(!$row['enabled'])
				xanth_db_query("UPDATE modules SET enabled = 1 WHERE name = '%s'",$module->name);
		}
		else
		{
			xanth_db_query("INSERT INTO modules(name,path,enabled) VALUES('%s','%s',%d)",$module->name,$module->path,1);
		}
		
		return true;
	}
	return false;
}

/**
*
*/
function xanth_disable_module($module)
{
	if(xanth_module_exists($name,$path))
	{
		$result = xanth_db_query("SELECT enabled FROM modules WHERE name = '%s'",$module->name);
		if($row = xanth_db_fetch_array($result))
		{
			if($row['enabled'])
				xanth_db_query("UPDATE modules SET enabled = 0 WHERE name = '%s'",$module->name);
		}
		return true;
	}
	return false;
}

/**
 * Include enabled modules and call xanth_init_module_[modulename] for every loaded module
 */
function xanth_init_modules()
{
	foreach(xanth_list_enabled_modules() as $module)
	{
		include_once($module->path . $module->name . '.inc.php');
		$init_func = "xanth_init_module_".$module->name;
		$init_func();
	}
}


?>