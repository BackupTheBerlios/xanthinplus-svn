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
*
*/
function xanth_admin_create_admin_box($hook_primary_id,$hook_secondary_id,$arguments)
{
	$paths = xanth_invoke_multi_hook(MULTI_HOOK_ADMIN_MENU_ADD_PATH,NULL);
	$output = '';
	
	$output .= '<ul>';
	foreach($paths as $path)
	{
		$output .= '<li><a href="?p='.$path.'">'.$path.'</a></li>';
	}
	$output .= '</ul>';
	
	return $output;
}


/*
*
*/
function xanth_init_component_admin()
{
	xanth_register_mono_hook(MONO_HOOK_CREATE_BOX_CONTENT,'admin_menu','xanth_admin_create_admin_box');
}



?>