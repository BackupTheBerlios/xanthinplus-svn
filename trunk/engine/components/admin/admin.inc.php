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
*
*/
function xanth_admin_index($eventName,$source_component,$arguments)
{
	echo '<ul>';
	echo '<li><a href="?p=admin/input_format">Input format</a></li>';
	echo '</ul>';
}

/*
*
*/
function xanth_admin_content_format($eventName,$source_component,$arguments)
{
	
}


/*
*
*/
function xanth_init_component_admin()
{
	xanth_register_callback(EVT_CORE_MAIN_ENTRY_CREATE_ . 'admin','xanth_admin_index');
	xanth_register_callback(EVT_CORE_MAIN_ENTRY_CREATE_ . 'admin/content_format','xanth_admin_content_format');
}



?>