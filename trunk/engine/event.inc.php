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
 */
function xanth_broadcast_event($eventName,$source_component,$arguments = array())
{
	global $xanth_events;
	if(!isSet($xanth_events[$eventName]))
		return;
	
	$arrayOfAnEvent = $xanth_events[$eventName];
	foreach($arrayOfAnEvent as $callback)
	{
		$callback($eventName,$source_component,$arguments);
	}
}


?>
