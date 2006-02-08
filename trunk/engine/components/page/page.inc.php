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


function xanth_page_generate_area($area_name,$boxes,$page_content)
{
	if($area_name == 'content')
	{
		$output = $page_content;
	}
	else
	{
		$output = '';
		foreach($boxes as $box)
		{
			$output .= "<div class=\"box\">$box</div>";
		}
	}
	
	return $output;
}

/*
* Handle page creation.
*/
function xanth_page_page_creation($hook_primary_id,$hook_secondary_id)
{
	$theme = xTheme::get_default();
	
	//retrieve path
	$path = xXanthPath::get_current();
	if($path == NULL)
	{
		xanth_log(LOG_LEVEL_ERROR,'Invalid xanth path','page',__FUNCTION__);
	}
	
	//retrieve content
	$page_content = xanth_invoke_mono_hook(MONO_HOOK_PAGE_CONTENT_CREATE,$path->base_path,array($path->resource_id));
	if($page_content === NULL)
	{
		$page_content = new xPageContent('Page not found',"<b>Page not found</b>");
	}
	
	$logs = '';
	$log_entries = xanth_get_screen_log();
	//display log messages
	if(!empty($log_entries))
	{
		$logs .= '<table border="1" width="90%"><tr><td><ul>';
		foreach($log_entries as $entry)
		{
			$logs .= '<li>' . $entry->level . ' ' . $entry->component . ' ' . htmlspecialchars($entry->message) . ' ' . $entry->filename . '@' . $entry->line . '</li>';
		}
		$logs .= '</ul></td></tr></table>' . "\n";
	}
	$page_content->body = $logs . $page_content->body;
	
	//retrieve areas
	$areas = xThemeArea::find_all();
	$page_areas = array();

	//retrieve innermost elements
	foreach($areas as $area)
	{
		$boxes = xBox::find($area->name);
		$boxes_ready_to_print = array();
		foreach($boxes as $box)
		{
			//retrieve box view
			$boxes_ready_to_print[] = eval($theme->get_view_mode_procedure('box'));
		}
		
		//Generate area view (not useing view mode)
		$page_areas[$area->name] = xanth_page_generate_area($area->name,$boxes_ready_to_print,$page_content->body);
	}

	//construct metadata array
	$page_metadata = array();
	if(empty($page_content->description))
	{
		$page_metadata['description'] = xSettings::get('site_description');
	}
	else
	{
		$page_metadata['description'] = $page_content->description;
	}
	
	if(empty($page_content->keywords))
	{
		$page_metadata['keywords'] = xSettings::get('site_keywords');
	}
	else
	{
		$page_metadata['keywords'] = $page_content->keywords;
	}
	
	//retrieve the full page
	$page_title = $page_content->title;
	$page_ready_to_print = eval($theme->get_view_mode_procedure('page'));
	
	echo $page_ready_to_print;
}



function xanth_init_component_page()
{
	xanth_register_mono_hook(MONO_HOOK_PAGE_CREATE,NULL,'xanth_page_page_creation');
}



?>