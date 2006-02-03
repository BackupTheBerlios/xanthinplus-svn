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

/*
* Handle page creation.
*/
function xanth_page_page_creation($hook_primary_id,$hook_secondary_id)
{
	//retrieve content page
	$path = xanth_get_xanthpath();
	if($path == NULL)
	{
		xanth_log(LOG_LEVEL_ERROR,'Invalid xanth path','page',__FUNCTION__);
	}
	
	$content = xanth_invoke_mono_hook(MONO_HOOK_MAIN_ENTRY_CREATE,$path->base_path,array($path->resource_id));

	if(empty($content))
	{
		$content = "<b>Page not found</b>";
	}
	
	$logs = '';
	$log_entries = xanth_get_screen_log();
	//write log messages
	if(!empty($log_entries))
	{
		$logs .= '<table border="1" width="90%"><tr><td><ul>';
		foreach($log_entries as $entry)
		{
			$logs .= '<li>' . $entry->level . ' ' . $entry->component . ' ' . htmlspecialchars($entry->message) . ' ' . $entry->filename . '@' . $entry->line . '</li>';
		}
		$logs .= '</ul></td></tr></table>' . "\n";
	}
	
	$content = $logs . $content;
	
	//retrieve areas
	$areas = xanth_invoke_mono_hook(MONO_HOOK_TEMPLATE_AREAS_LIST,NULL);
	$areas_ready_to_print = array();

	//retrieve innermost elements
	foreach($areas as $area)
	{
		$boxes = xBox::find($area);
		$boxes_ready_to_print = array();
		foreach($boxes as $box)
		{
			//retrieve boxes
			$boxes_ready_to_print[] = xanth_invoke_mono_hook(MONO_HOOK_BOX_TEMPLATE,NULL,array($box->name,$box->content));
		}
		//retrieve an area
		$areas_ready_to_print[$area] = xanth_invoke_mono_hook(MONO_HOOK_AREA_TEMPLATE,$area,array($boxes_ready_to_print,$content));
	}

	//retrieve the full page
	$page_ready_to_print = xanth_invoke_mono_hook(MONO_HOOK_PAGE_TEMPLATE,NULL,array($areas_ready_to_print));

	echo $page_ready_to_print;
}


function xanth_init_component_page()
{
	xanth_register_mono_hook(MONO_HOOK_PAGE_CREATE,NULL,'xanth_page_page_creation');
}



?>