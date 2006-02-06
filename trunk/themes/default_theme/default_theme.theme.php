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
	list($areas,$title,$metadata) = $arguments;
	
	$output = "<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Strict//EN\" \"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd\">\n";
	$output .= "<html>\n";
	$output .= "<head>\n";
	$output .= "<title>$title</title>". "\n";
	$output .= '<meta name="keywords" content="'.$metadata['keywords'].'" />' . "\n";
	$output .= '<meta name="description" content="'.$metadata['description'].'" />'. "\n";
	$output .= "<style type=\"text/css\" media=\"all\">@import \"themes/default_theme/style.css\";</style>" . "\n";
	$output .= "</head>";
	$output .= "<body>\n";
	$output .= '<table id="page-table"><tr>' . "\n";
	$output .= '<td id="left-sidebar">'. $areas['left sidebar'] . '</td>';
	$output .= '<td id="content">'. $areas['content'] .'</td>';
	$output .= "</tr></table>\n";
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
	
	return "<div class=\"title\">$title</div><div class=\"body\">$body</div>";
}


function default_left_sidebar_area_template($hook_primary_id,$hook_secondary_id,$arguments)
{
	list($boxes,$content) = $arguments;
	
	$output = '';
	foreach($boxes as $box)
	{
		$output .= "<div class=\"box\">$box</div>";
	}
	
	return $output;
}


function default_content_area_template($hook_primary_id,$hook_secondary_id,$arguments)
{
	list($boxes,$content) = $arguments;

	$output = "$content";
	
	return $output;
}



function xanth_custom_theme_areas($hook_primary_id,$hook_secondary_id,$arguments)
{
	return array('left sidebar','content');
}


function xanth_theme_init_default()
{
	xanth_register_mono_hook(MONO_HOOK_PAGE_TEMPLATE,NULL,'default_page_template');
	xanth_register_mono_hook(MONO_HOOK_ENTRY_TEMPLATE,NULL,'default_content_entry_template');
	xanth_register_mono_hook(MONO_HOOK_BOX_TEMPLATE,NULL,'default_box_template');
	xanth_register_mono_hook(MONO_HOOK_TEMPLATE_AREAS_LIST,NULL,'xanth_custom_theme_areas');
	
	xanth_register_mono_hook(MONO_HOOK_AREA_TEMPLATE,'left sidebar','default_left_sidebar_area_template');
	xanth_register_mono_hook(MONO_HOOK_AREA_TEMPLATE,'content','default_content_area_template');
}


?>
