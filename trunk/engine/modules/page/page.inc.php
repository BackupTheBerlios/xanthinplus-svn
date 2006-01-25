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
function xanth_page_page_creation($eventName,$source_component)
{	
	//retrieve content page
	$path = xanth_get_xanthpath();
	ob_start();
	xanth_broadcast_event(EVT_CORE_CREATE_CONTENT_ENTRY_ . $path,'page');
	$content = ob_get_clean();
	
	//retrieve areas
	$areas = array();
	
	xanth_broadcast_event(EVT_THEME_AREA_LIST,'theme',$areas);
	$areas_ready_to_print = array();

	//retrieve innermost elements
	foreach($areas as $area)
	{
		$boxes = xanth_box_list($area);
		$boxes_ready_to_print = array();
		foreach($boxes as $box)
		{
			//retrieve boxes
			ob_start();
			xanth_broadcast_event(EVT_THEME_BOX_TEMPLATE,'page',$box->name,$box->content);
			$boxes_ready_to_print[] = ob_get_clean();
		}
		
		//retrieve an area
		ob_start();
		xanth_broadcast_event(EVT_THEME_AREA_TEMPLATE_ . $area,'page',$boxes_ready_to_print,$content);
		$areas_ready_to_print[$area] = ob_get_clean();
	}
	
	//retrieve the full page
	ob_start();
	xanth_broadcast_event(EVT_THEME_PAGE_TEMPLATE,'page',$areas_ready_to_print);
	$page_ready_to_print = ob_get_clean();
	
	//now print all
	echo $page_ready_to_print;
}


function xanth_init_module_page()
{
	xanth_register_callback(EVT_CORE_CREATE_PAGE,'xanth_page_page_creation');
}



?>