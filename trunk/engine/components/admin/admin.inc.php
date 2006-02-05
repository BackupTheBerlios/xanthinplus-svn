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
function xanth_admin_index($hook_primary_id,$hook_secondary_id,$arguments)
{
	$output = '<ul>';
	$output .= '<li><a href="?p=admin/content_format">Content format</a></li>';
	$output .= '</ul>';
	
	return $output;
}

/**
*
*/
function xanth_admin_create_admin_box()
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
function xanth_admin_content_format($hook_primary_id,$hook_secondary_id,$arguments)
{
	$form = new xForm('?p=admin/content_format/add');
	
	$form_elements = array();
	
	$form_elements[] = new xFormTextField('content_title','Title:','','');
	$form_elements[] = new xFormSubmit('submit','Create');
	$form_groups[] = new xFormGroup($form_elements,'');
	
	
	return $form->render();
}


/*
*
*/
function xanth_init_component_admin()
{
	xanth_register_mono_hook(MONO_HOOK_MAIN_ENTRY_CREATE, 'admin','xanth_admin_index');
	xanth_register_mono_hook(MONO_HOOK_MAIN_ENTRY_CREATE, 'admin/content_format','xanth_admin_content_format');
	xanth_register_mono_hook(MONO_HOOK_CREATE_BOX_CONTENT,'admin_menu','xanth_admin_create_admin_box');
}



?>