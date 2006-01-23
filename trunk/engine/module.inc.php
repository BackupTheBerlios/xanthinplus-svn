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
// Event framework
//------------------------------------------------------------------------------------------------------------------//

/**
 * Register a callback in the event system. The callback function should be of type: \n
 * callback($eventName,$source_component,$refOutBuffer,$arg0,$arg1,$arg2,$arg3,$arg4,$arg5,$arg6,$arg7,$arg8,$arg9)
 */
function xanth_register_callback($eventName,$callback)
{
	global $xanth_events;
	if(!isSet($xanth_events[$eventName]))
		$xanth_events[$eventName] = array();
		
	array_push($xanth_events[$eventName],$callback);
}

/**
 * Unregister a callback in the event system
 */
function xanth_unregister_callback($eventName,$callback)
{
	global $xanth_events;
	$arrayOfAnEvent = $xanth_events[$eventName];
	$toRemove = array_search($callback,$arrayOfAnEvent);
	array_splice($arrayOfAnEvent,$toRemove,1);
}

/**
 * Triggers an event, calling all registered callbacks.
 * 
 * @param ... Any argument to be passed to callback (max 10).
 */
function xanth_broadcast_event($eventName,$source_component)
{
	global $xanth_events;
	if(!isSet($xanth_events[$eventName]))
		return;
		
	$args = func_get_args();
	array_shift($args);array_shift($args); //remove first and second element
	$arg0 = '';
	$arg1 = '';
	$arg2 = '';
	$arg3 = '';
	$arg4 = '';
	$arg5 = '';
	$arg6 = '';
	$arg7 = '';
	$arg8 = '';
	$arg9 = '';
	for($i = 0;$i <= 10 && $i < count($args);$i++)
	{
		$current_arg = "arg$i";
		$$current_arg = $args[$i];
	}
	
	$arrayOfAnEvent = $xanth_events[$eventName];
	foreach($arrayOfAnEvent as $callback)
	{
		$callback($eventName,$source_component,$arg0,$arg1,$arg2,$arg3,$arg4,$arg5,$arg6,$arg7,$arg8,$arg9);
	}
}

//------------------------------------------------------------------------------------------------------------------//
// Modules functions
//------------------------------------------------------------------------------------------------------------------//


/**
 * Returns an array of mapped array representing all existing modules \n
 * $ret[0] = array(name,path)
 */
function xanth_list_existing_modules()
{
	$modules = array();
	
	//read builtin directory
	$dir = xanth_get_working_dir() . '/engine/modules/';
	$dir_list = xanth_list_dirs($dir);
	if(is_array($dir_list))
	{
        $modules = array_merge($modules,$dir_list);
    }
	else
	{
		xanth_log(LOG_LEVEL_FATAL_ERROR,"Builtin module directory $dir not found","Core",__FILE__,__LINE__);
	}
	
	
	//read additional module directory
	$dir = xanth_get_working_dir() . '/modules/';
	$dir_list = xanth_list_dirs($dir);
	if(is_array($dir_list))
	{
        $modules = array_merge($modules,$dir_list);
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
		$result = xanth_db_query("SELECT enabled FROM modules WHERE name = '%s'",$module['name']);
		if($row = xanth_db_fetch_array($result))
		{
			if($row['enabled'] !== 0)
			{
				array_push($enabled_mod,$module);
			}
		}
	}
	return $enabled_mod;
}

/**
*
*/
function xanth_module_exists($name,$path)
{
	return is_dir($path . $name);
}

/**
*
*/
function xanth_enable_module($path,$name)
{
	if(xanth_module_exists($name,$path))
	{
		$result = xanth_db_query("SELECT enabled FROM modules WHERE name = '%s'",$name);
		if($row = xanth_db_fetch_array($result))
		{
			if(!$row['enabled'])
				xanth_db_query("UPDATE modules SET enabled = 1 WHERE name = '%s'",$name);
		}
		else
		{
			xanth_db_query("INSERT INTO modules(name,path,enabled) VALUES('%s','%s',%d)",$name,$path,1);
		}
		
		return true;
	}
	return false;
}

/**
*
*/
function xanth_disable_module($path,$name)
{
	if(xanth_module_exists($name,$path))
	{
		$result = xanth_db_query("SELECT enabled FROM modules WHERE name = '%s'",$name);
		if($row = xanth_db_fetch_array($result))
		{
			if($row['enabled'])
				xanth_db_query("UPDATE modules SET enabled = 0 WHERE name = '%s'",$name);
		}
		return true;
	}
	return false;
}

/**
 * Include enabled modules and call xanth_init_[modulename] for every loaded module
 */
function xanth_init_modules()
{
	foreach(xanth_list_enabled_modules() as $module)
	{
		include_once($module['path'] . $module['name'] . '.inc.php');
		$init_func = "xanth_init_".$module['name'];
		$init_func();
	}
}


?>