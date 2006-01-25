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

function default_page_template($eventName,$component,$areas)
{
	echo "<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Strict//EN\" \"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd\">\n";
	echo "<html>\n";
	echo "<head>\n";
	echo "</head>";
	echo "<body>\n";
	echo $areas['custom area'];
	echo " </body>\n";
	echo "</html>\n";
}


function default_content_entry_template($eventName,$component,$title,$body)
{
	echo "<strong>$title</strong> <br> $body";
}



function default_box_template($eventName,$component,$title,$body)
{
	echo "<strong>$title</strong> <br> $body";
}


function default_custom_area_template($eventName,$component,$boxes,$content,$elements)
{
	foreach($boxes as $box)
	{
		echo "$box <br>";
	}

	echo "$content <br>";
}


function xanth_custom_theme_areas($eventName,$component,&$areas)
{
	$areas[] = 'custom area';
}


function xanth_theme_init()
{
	xanth_register_callback(EVT_THEME_PAGE_TEMPLATE,'default_page_template');
	xanth_register_callback(EVT_THEME_CONTENT_ENTRY_TEMPLATE,'default_content_entry_template');
	xanth_register_callback(EVT_THEME_BOX_TEMPLATE,'default_box_template');
	xanth_register_callback(EVT_THEME_AREA_TEMPLATE_ . 'custom area','default_custom_area_template');
	xanth_register_callback(EVT_THEME_AREA_LIST,'xanth_custom_theme_areas');
}


?>