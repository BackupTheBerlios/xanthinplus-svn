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

function default_page_template($hook_primary_id,$hook_secondary_id,$arguments)
{
	list($areas) = $arguments;
	
	$output = "<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Strict//EN\" \"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd\">\n";
	$output .= "<html>\n";
	$output .= "<head>\n";
	$output .= "</head>";
	$output .= "<body>\n";
	$output .= $areas['custom area'];
	$output .= " </body>\n";
	$output .= "</html>\n";
	
	return $output;
}


function default_content_entry_template($hook_primary_id,$hook_secondary_id,$arguments)
{
	$entry = $arguments[0];
	
	return "<strong>" . $entry->title ."</strong> <br>" . $entry->content;
}



function default_box_template($hook_primary_id,$hook_secondary_id,$arguments)
{
	list($title,$body) = $arguments;
	
	return "<strong>$title</strong> <br> $body";
}


function default_custom_area_template($hook_primary_id,$hook_secondary_id,$arguments)
{
	list($boxes,$content) = $arguments;
	
	$output = '';
	foreach($boxes as $box)
	{
		$output .= "$box <br>";
	}

	$output .= "$content <br>";
	
	return $output;
}


function xanth_custom_theme_areas($hook_primary_id,$hook_secondary_id,$arguments)
{
	return array('custom area');
}


function xanth_theme_init_default()
{
	xanth_register_mono_hook(MONO_HOOK_PAGE_TEMPLATE,NULL,'default_page_template');
	xanth_register_mono_hook(MONO_HOOK_ENTRY_TEMPLATE,NULL,'default_content_entry_template');
	xanth_register_mono_hook(MONO_HOOK_BOX_TEMPLATE,NULL,'default_box_template');
	xanth_register_mono_hook(MONO_HOOK_AREA_TEMPLATE,'custom area','default_custom_area_template');
	xanth_register_mono_hook(MONO_HOOK_TEMPLATE_AREAS_LIST,NULL,'xanth_custom_theme_areas');
}


?>