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


class xanthComponent
{
	var $path;
	var $name;
	
	function xanthComponent($path,$name)
	{
		$this->path = $path;
		$this->name = $name;
	}
};

/**
 * Returns an array of xanthComponent objects  representing all existing compoenents \n
 */
function xanth_component_list_existing()
{
	$components = array();
	
	//read additional module directory
	$dir = './engine/components';
	$dir_list = xanth_list_dirs($dir);
	if(is_array($dir_list))
	{
        foreach($dir_list as $raw_component)
		{
			$components[] = new xanthComponent($raw_component['path'],$raw_component['name']);
		}
    }
	else
	{
		xanth_log(LOG_LEVEL_FATAL_ERROR,"Component directory $dir not found","Core",__FUNCTION__);
	}
	
	return $components;
}


/**
 * Include enabled components and call xanth_init_component_[componentname] for every ones.
 */
function xanth_components_init()
{
	foreach(xanth_component_list_existing() as $component)
	{
		include_once($component->path . $component->name . '.inc.php');
		$init_func = "xanth_init_component_" . $component->name;
		$init_func();
	}
}


?>