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


function xanth_content_content_create($eventName,$source_component,$arguments)
{
	$selected_entry = xanth_entry_get($arguments[0]);
	if($selected_entry == NULL)
	{
		xanth_log(LOG_LEVEL_ERROR,'content','Content not found');
	}
	else
	{
		xanth_broadcast_event(EVT_THEME_ENTRY_TEMPLATE,'content',array($selected_entry));
	}
}


function xanth_init_component_content()
{
	xanth_register_callback(EVT_CORE_MAIN_ENTRY_CREATE_ . 'content','xanth_content_content_create');
}



?>