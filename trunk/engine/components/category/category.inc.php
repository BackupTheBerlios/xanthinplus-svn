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

require_once('engine/components/category/category.class.inc.php');



function xanth_category_admin_category_create($hook_primary_id,$hook_secondary_id,$arguments)
{
	if(!xUser::check_current_user_access('create category'))
	{
		return xSpecialPage::access_denied();
	}
	
	//create form
	$form = new xForm('?p=admin/category/create');
	$form->elements[] = new xFormElementTextField('cat_title','Title','','',TRUE,new xInputValidatorTextNoTags(256));
	$form->elements[] = new xFormElementTextArea('cat_description','Description','','',TRUE,new xInputValidatorText(-1));
	
	//types
	$types = xEntryType::find_all();
	$options = array();
	foreach($types as $type)
	{
		$options[$type->name] = $type->name;
	}
	$form->elements[] = new xFormElementOptions('entry_types','Select type','','',$options,TRUE,TRUE,new xInputValidatorTextNameId(32));
	
	//parent category
	$categories = xCategory::find_all();
	$options = array();
	$options['[no parent]'] = '0';
	foreach($categories as $category)
	{
		$options[$category->title] = $category->id;
	}
	$form->elements[] = new xFormElementOptions('parent_category','Parent category','','',$options,FALSE,FALSE,new xInputValidatorInteger());
	
	//view modes
	$modes = xViewMode::find_by_element('category');
	$options = array();
	$options['[theme default]'] = '0';
	foreach($modes as $mode)
	{
		$options[$mode->name] = $mode->id;
	}
	$form->elements[] = new xFormElementOptions('category_view_mode','View mode','','',$options,FALSE,FALSE,new xInputValidatorInteger());
	
	//submit buttom
	$form->elements[] = new xFormSubmit('submit','Create');
	
	$ret = $form->validate_input();
	if(isset($ret->valid_data['submit']))
	{
		if(empty($ret->errors))
		{
			$cat = new xCategory(NULL,$ret->valid_data['cat_title'],$ret->valid_data['entry_types'],
			$ret->valid_data['cat_description'],$ret->valid_data['category_view_mode'],$ret->valid_data['parent_category']);
			$cat->insert();
			
			return new xPageContent('Category created','Category created');
		}
		else
		{
			foreach($ret->errors as $error)
			{
				xanth_log(LOG_LEVEL_USER_MESSAGE,$error);
			}
		}
	}

	return new xPageContent('Create category',$form->render());
}

//@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@

function xanth_category_admin_entry_type_add($hook_primary_id,$hook_secondary_id,$arguments)
{
	if(!xUser::check_current_user_access('add entry type'))
	{
		return xSpecialPage::access_denied();
	}
	
	//create form
	$form = new xForm('?p=admin/entry_type/add');
	$form->elements[] = new xFormElementTextField('entry_type_name','Name','','',TRUE,new xInputValidatorTextNameId(32));
	
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

function xanth_category_admin_menu_add_link($hook_primary_id,$hook_secondary_id,$arguments)
{
	return 'admin/category/create';
}
function xanth_category_admin_menu_add_link2($hook_primary_id,$hook_secondary_id,$arguments)
{
	return 'admin/entry_type/add';
}

/*
*
*/
function xanth_init_component_category()
{
	xanth_register_mono_hook(MONO_HOOK_PAGE_CONTENT_CREATE, 'admin/category/create','xanth_category_admin_category_create');
	xanth_register_mono_hook(MONO_HOOK_PAGE_CONTENT_CREATE, 'admin/entry_type/add','xanth_category_admin_entry_type_add');
	
	xanth_register_multi_hook(MULTI_HOOK_ADMIN_MENU_ADD_PATH,NULL,'xanth_category_admin_menu_add_link');
	xanth_register_multi_hook(MULTI_HOOK_ADMIN_MENU_ADD_PATH,NULL,'xanth_category_admin_menu_add_link2');
}



?>