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
function xanth_content_content_create($hook_primary_id,$hook_secondary_id,$arguments)
{
	$selected_entry = xEntry::get($arguments[0]);
	if($selected_entry == NULL)
	{
		xanth_log(LOG_LEVEL_ERROR,'Content not found','content');
	}
	else
	{
		return xanth_invoke_mono_hook(MONO_HOOK_ENTRY_TEMPLATE,NULL,array($selected_entry));
	}
}

/*
*
*/
function xanth_content_admin_content_create($hook_primary_id,$hook_secondary_id,$arguments)
{
	$form_elements = array();
	$form_groups = array();
	
	$form_elements[] = new xFormTextField('content_title','Title:','','');
	$form_elements[] = new xFormSubmit('submit','Create');
	$form_groups[] = new xFormGroup($form_elements,'');
	$form = new xForm('?p=admin/content/create',$form_groups);
	
	return $form->render();
}


/*
*
*/
function xanth_init_component_content()
{
	xanth_register_mono_hook(MONO_HOOK_MAIN_ENTRY_CREATE, 'content','xanth_content_content_create');
	xanth_register_mono_hook(MONO_HOOK_MAIN_ENTRY_CREATE, 'admin/content/create','xanth_content_admin_content_create');
}



?>