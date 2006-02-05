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


function xanth_get_mono_hooks()
{
	global $xanth_mono_hooks;
	
	return $xanth_mono_hooks;
}


/**
*
*/
function xanth_register_mono_hook($hook_primary_id,$hook_secondary_id,$hook_function)
{
	global $xanth_mono_hooks;
	$hook_id = $hook_primary_id . $hook_secondary_id;

	if(empty($xanth_mono_hooks[$hook_id]))
	{
		$xanth_mono_hooks[$hook_id] = $hook_function;
	}
	else
	{
		xanth_log(LOG_LEVEL_WARNING,"Mono hook collision detected.Hook function $hook_function is trying to overwrite "
			.$xanth_mono_hooks[$hook_id]." for hook id '$hook_primary_id:$hook_secondary_id'",
			'hook');
	}
}

/**
*
*/
function xanth_register_multi_hook($hook_primary_id,$hook_secondary_id,$hook_function)
{
	global $xanth_multi_hooks;
	$hook_id = $hook_primary_id . $hook_secondary_id;

	$xanth_multi_hooks[$hook_id][] = $hook_function;
}

/**
*
*/
function xanth_unregister_mono_hook($hook_primary_id,$hook_secondary_id,$hook_function)
{
	global $xanth_mono_hooks;
	$hook_id = $hook_primary_id . $hook_secondary_id;

	if(!empty($xanth_mono_hooks[$hook_id]) && $xanth_mono_hooks[$hook_id] == $hook_function)
	{
		unset($xanth_mono_hooks[$hook_id]);
	}
}

/**
*
*/
function xanth_unregister_multi_hook($hook_primary_id,$hook_secondary_id,$hook_function)
{
	global $xanth_multi_hooks;
	$hook_id = $hook_primary_id . $hook_secondary_id;

	if($i = array_search($hook_function, $xanth_multi_hooks[$hook_id]) != FALSE)
	{
		array_splice($xanth_multi_hooks[$hook_id],$i,1);
	}
}

/**
*
*/
function xanth_invoke_mono_hook($hook_primary_id,$hook_secondary_id,$arguments = NULL)
{
	global $xanth_mono_hooks;
	$hook_id = $hook_primary_id . $hook_secondary_id;
	if(!empty($xanth_mono_hooks[$hook_id]))
	{
		return $xanth_mono_hooks[$hook_id]($hook_primary_id,$hook_secondary_id,$arguments);
	}
	return NULL;
}

/**
*
*/
function xanth_invoke_multi_hook($hook_primary_id,$hook_secondary_id,$arguments = NULL)
{
	global $xanth_multi_hooks;
	$hook_id = $hook_primary_id . $hook_secondary_id;
	if(empty($xanth_multi_hooks[$hook_id]))
		return NULL;
	
	$returns = array();
	foreach($xanth_multi_hooks[$hook_id] as $hook_func)
	{
		$returns[] = $hook_func($hook_primary_id,$hook_secondary_id,$arguments);
	}
	return $returns;
}

?>
