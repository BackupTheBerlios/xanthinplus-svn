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

require_once('engine/components/entry/entry.class.inc.php');

//@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@

function xanth_entry_view_entry($hook_primary_id,$hook_secondary_id,$arguments)
{
	$selected_entry = xEntry::get($arguments[0]);
	if($selected_entry === NULL)
	{
		return NULL;
	}
	elseif($selected_entry->published == FALSE && !xUser::check_current_user_access('dummy access'))
	{
		return xSpecialPage::access_denied();
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
		$theme = xTheme::get_default();
		$entry = $selected_entry;
		$entry_ready_to_print = eval($theme->get_view_mode_procedure('entry'));
		return new xPageContent($selected_entry->title,$entry_ready_to_print,$selected_entry->description,
			$selected_entry->keywords);
	}
}

//@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@

function xanth_entry_admin_entry_create($hook_primary_id,$hook_secondary_id,$arguments)
{
	if(!xUser::check_current_user_access('create entry'))
	{
		return xSpecialPage::access_denied();
	}
	
	//create form
	$form = new xForm('?p=admin/entry/create');
	
	//types
	$types = xEntryType::find_all();
	$options = array();
	foreach($types as $type)
	{
		$options[$type->name] = $type->name;
	}
	$form->elements[] = new xFormElementOptions('entry_type','Select type','','',$options,FALSE,TRUE,new xInputValidatorTextNoTags(32));
	
	//title
	$form->elements[] = new xFormElementTextField('content_title','Title','','',TRUE,new xInputValidatorTextNoTags(256));
	//body
	$form->elements[] = new xFormElementTextArea('content_body','Body','','',TRUE,new xInputValidatorText(256));
	
	//content formats
	$content_formats = xContentFormat::find_all();
	$content_formats_radio_group = new xFormRadioGroup(array(),'Content format');
	
	foreach($content_formats as $content_format)
	{
		$content_formats_radio_group->elements[] = new xFormElementRadio('content_format',$content_format->name,
			$content_format->description,$content_format->name,FALSE,TRUE,
			new xInputValidatorText(64));
	}
	$content_formats_radio_group->elements[0]->checked = TRUE;
	$form->elements[] = $content_formats_radio_group;
	
	//categories
	$categories = xCategory::find_all();
	$options = array();
	foreach($categories as $category)
	{
		$options[$category->title] = $category->id;
	}
	$form->elements[] = new xFormElementOptions('entry_categories','Categories','','',$options,TRUE,FALSE,new xInputValidatorInteger());
	
	//parameters
	$parameters = new xFormGroup(array(),'Parameters');
	$parameters->elements[] = new xFormElementCheckbox('param_published','Published','','1',TRUE,FALSE,
		new xInputValidatorInteger());
	$form->elements[] = $parameters;
	
	//metadata
	$metadata = new xFormGroup(array(),'Metadata');
	$metadata->elements[] = new xFormElementTextField('meta_description','Description','','',FALSE,new xInputValidatorTextNoTags(512));
	$metadata->elements[] = new xFormElementTextField('meta_keywords','Keywords','','',FALSE,new xInputValidatorTextNoTags(128));
	$form->elements[] = $metadata;
	
	//submit buttom
	$form->elements[] = new xFormSubmit('submit','Create');
	
	$ret = $form->validate_input();
	if(isset($ret->valid_data['submit']))
	{
		if(empty($ret->errors))
		{
			//no error,lets create the entry
			$author = xUser::get_current_username() !== NULL ? xUser::get_current_username() : 'anonymous';
			//translate categories
			$cat_ids = $ret->valid_data['entry_categories'];
			$categories = array();
			if(!empty($cat_ids))
			{
				foreach($cat_ids as $cat_id)
				{
					$categories[] = new xCategory($cat_id);
				}
			}
			
			$entry = new xEntry(NULL,$ret->valid_data['content_title'],$ret->valid_data['entry_type'],$author,$ret->valid_data['content_body'],
				$ret->valid_data['content_format'],$ret->valid_data['param_published'],$ret->valid_data['meta_description'],
				$ret->valid_data['meta_keywords'],$categories);
			$entry->insert();
			
			
			return new xPageContent('Entry created','Entry created, <a href="?p=entry//'.$entry->id.'">view it</a>');
		}
		else
		{
			foreach($ret->errors as $error)
			{
				xanth_log(LOG_LEVEL_USER_MESSAGE,$error);
			}
		}
	}

	return new xPageContent('Create entry',$form->render());
}

//@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@

function xanth_entry_admin_entry($hook_primary_id,$hook_secondary_id,$arguments)
{
	if(!xUser::check_current_user_access('manage entries'))
	{
		return xSpecialPage::access_denied();
	}
	
	$entries = xEntry::find_all();
	
	$output = "<table>\n";
	$output .= "<tr><th>Id</th><th>Title</th><th>Edit</th><th>Delete</th></tr>\n";
	foreach($entries as $entry)
	{
		$output .= "<tr><td>".$entry->id."</td><td>".$entry->title."</td><td>Edit</td><td>Delete</td></tr>";
	}
	$output .= "<table>\n";
	
	return new xPageContent('Admin entries',$output);
}

//@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@

function xanth_entry_admin_entry_type_add($hook_primary_id,$hook_secondary_id,$arguments)
{
	if(!xUser::check_current_user_access('add entry type'))
	{
		return xSpecialPage::access_denied();
	}
	
	//create form
	$form = new xForm('?p=admin/entry_type/add');
	$form->elements[] = new xFormElementTextField('entry_type_name','Name','','',TRUE,new xInputValidatorTextNoTags(32));
	
	//view modes
	$modes = xViewMode::find_by_element('entry');
	$options = array();
	$options['[theme default]'] = '0';
	foreach($modes as $mode)
	{
		$options[$mode->name] = $mode->id;
	}
	$form->elements[] = new xFormElementOptions('entry_type_view_mode','View mode','','',$options,FALSE,FALSE,new xInputValidatorInteger());
	
	//submit buttom
	$form->elements[] = new xFormSubmit('submit','Add');
	
	$ret = $form->validate_input();
	if(isset($ret->valid_data['submit']))
	{
		if(empty($ret->errors))
		{
			$entry_type = new xEntryType($ret->valid_data['entry_type_name'],$ret->valid_data['entry_type_view_mode']);
			$entry_type->insert();
			
			return new xPageContent('Entry type created','Entry type created');
		}
		else
		{
			foreach($ret->errors as $error)
			{
				xanth_log(LOG_LEVEL_USER_MESSAGE,$error);
			}
		}
	}

	return new xPageContent('Create entry',$form->render());
}

//@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@

function xanth_entry_admin_menu_add_link($hook_primary_id,$hook_secondary_id,$arguments)
{
	return 'admin/entry/create';
}
function xanth_entry_admin_menu_add_link2($hook_primary_id,$hook_secondary_id,$arguments)
{
	return 'admin/entry';
}
function xanth_entry_admin_menu_add_link3($hook_primary_id,$hook_secondary_id,$arguments)
{
	return 'admin/entry_type/add';
}

//@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@

function xanth_init_component_entry()
{
	xanth_register_mono_hook(MONO_HOOK_PAGE_CONTENT_CREATE, 'entry','xanth_entry_view_entry');
	xanth_register_mono_hook(MONO_HOOK_PAGE_CONTENT_CREATE, 'admin/entry/create','xanth_entry_admin_entry_create');
	xanth_register_mono_hook(MONO_HOOK_PAGE_CONTENT_CREATE, 'admin/entry','xanth_entry_admin_entry');
	xanth_register_mono_hook(MONO_HOOK_PAGE_CONTENT_CREATE, 'admin/entry_type/add','xanth_entry_admin_entry_type_add');
	
	xanth_register_multi_hook(MULTI_HOOK_ADMIN_MENU_ADD_PATH,NULL,'xanth_entry_admin_menu_add_link');
	xanth_register_multi_hook(MULTI_HOOK_ADMIN_MENU_ADD_PATH,NULL,'xanth_entry_admin_menu_add_link2');
	xanth_register_multi_hook(MULTI_HOOK_ADMIN_MENU_ADD_PATH,NULL,'xanth_entry_admin_menu_add_link3');
}



?>