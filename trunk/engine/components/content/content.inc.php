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

require_once('engine/components/content/entry.class.inc.php');

/*
*
*/
function xanth_content_content_create($hook_primary_id,$hook_secondary_id,$arguments)
{
	$selected_entry = xEntry::get($arguments[0]);
	if($selected_entry === NULL)
	{
		return NULL;
	}
	
	$content_format = new xContentFormat($selected_entry->content_format,'');
	$selected_entry->content = $content_format->apply_to($selected_entry->content);
	
	if(!empty($content_format->last_error))
	{
		xanth_log(LOG_LEVEL_USER_MESSAGE,$content_format->last_error);
	}
	
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
	if(!xUser::check_current_user_access('create content'))
	{
		xanth_log(LOG_LEVEL_ERROR,"Access denied");
		return FALSE;
	}
	
	$form = new xForm('?p=admin/content/create');
	$form->elements[] = new xFormElementTextField('content_title','Title','','',new xInputValidatorTextNoTags(256,TRUE));
	$form->elements[] = new xFormElementTextArea('content_body','Body','','',new xInputValidatorText(256,TRUE));
	
	
	$content_formats = xContentFormat::find_all();
	$content_formats_radio_group = new xFormRadioGroup(array(),'Content format');
	
	foreach($content_formats as $content_format)
	{
		$content_formats_radio_group->elements[] = new xFormElementRadio('content_format',$content_format->name,
			$content_format->description,$content_format->name,FALSE,
			new xInputValidatorText(64,TRUE));
	}
	$form->elements[] = $content_formats_radio_group;
	$form->elements[] = new xFormSubmit('submit','create');
	
	$ret = $form->validate_input();
	if(isset($ret->valid_data['submit']))
	{
		if(empty($ret->errors))
		{
			$author = xUser::get_current_username() !== NULL ? xUser::get_current_username() : 'anonymous';
			$entry = new xEntry(NULL,$ret->valid_data['content_title'],NULL,$author,$ret->valid_data['content_body'],
				$ret->valid_data['content_format']);
			$entry->insert();
			return 'Entry created, <a href="?p=content/'.$entry->id.'">view it</a>';
		}
		else
		{
			foreach($ret->errors as $error)
			{
				xanth_log(LOG_LEVEL_USER_MESSAGE,$error);
			}
			return $form->render();
		}
	}
	else
	{
		return $form->render();
	}
}


function xanth_content_admin_menu_add_link($hook_primary_id,$hook_secondary_id,$arguments)
{
	return 'admin/content/create';
}


/*
*
*/
function xanth_init_component_content()
{
	xanth_register_mono_hook(MONO_HOOK_MAIN_ENTRY_CREATE, 'content','xanth_content_content_create');
	xanth_register_mono_hook(MONO_HOOK_MAIN_ENTRY_CREATE, 'admin/content/create','xanth_content_admin_content_create');
	xanth_register_multi_hook(MULTI_HOOK_ADMIN_MENU_ADD_PATH,NULL,'xanth_content_admin_menu_add_link');
}



?>