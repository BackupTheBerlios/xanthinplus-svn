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

require_once('engine/components/theme/theme.class.inc.php');
require_once('engine/components/theme/themearea.class.inc.php');

//@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@

function xanth_theme_admin_theme($hook_primary_id,$hook_secondary_id,$arguments)
{
	if(!xUser::check_current_user_access('manage themes'))
	{
		return xSpecialPage::access_denied();
	}
	
	$themes = xTheme::find_all();
	
	$output = "<table>\n";
	$output .= "<tr><th>Name</th><th>Edit</th><th>Delete</th></tr>\n";
	foreach($themes as $theme)
	{
		$output .= "<tr><td>".$theme->name."</td><td>Edit</td><td>Delete</td></tr>";
	}
	$output .= "<table>\n";
	
	return new xPageContent('Admin themes',$output);
}

//@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@

function xanth_theme_admin_theme_add($hook_primary_id,$hook_secondary_id,$arguments)
{
	if(!xUser::check_current_user_access('add theme'))
	{
		return xSpecialPage::access_denied();
	}
	
	$form = new xForm('?p=admin/theme/add');
	$form->elements[] = new xFormElementTextField('theme_name','Theme name','','',FALSE,new xInputValidatorTextNoTags(32));
	$form->elements[] = new xFormSubmit('submit','submit');
		
	$ret = $form->validate_input();
	if(isset($ret->valid_data['submit']))
	{
		if(empty($ret->errors))
		{
			$theme = new xTheme($ret->valid_data['theme_name'],array());
			$theme->insert();
			
			return new xPageContent('Add Theme','Theme added');
		}
		else
		{
			foreach($ret->errors as $error)
			{
				xanth_log(LOG_LEVEL_USER_MESSAGE,$error);
			}
		}
	}

	return new xPageContent('Add Theme',$form->render());
}

//@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@

function xanth_theme_admin_menu_add_link($hook_primary_id,$hook_secondary_id,$arguments)
{
	return 'admin/theme';
}
function xanth_theme_admin_menu_add_link2($hook_primary_id,$hook_secondary_id,$arguments)
{
	return 'admin/theme/add';
}

//@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@

function xanth_init_component_theme()
{
	xanth_register_mono_hook(MONO_HOOK_PAGE_CONTENT_CREATE, 'admin/theme','xanth_theme_admin_theme');
	xanth_register_mono_hook(MONO_HOOK_PAGE_CONTENT_CREATE, 'admin/theme/add','xanth_theme_admin_theme_add');
	
	xanth_register_multi_hook(MULTI_HOOK_ADMIN_MENU_ADD_PATH,NULL,'xanth_theme_admin_menu_add_link');
	xanth_register_multi_hook(MULTI_HOOK_ADMIN_MENU_ADD_PATH,NULL,'xanth_theme_admin_menu_add_link2');
}



?>