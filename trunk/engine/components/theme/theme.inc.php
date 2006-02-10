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
		$output .= "<tr><td>".$theme->name.'</td><td><a href="?p=admin/theme/edit//'.$theme->name.
			'">Edit</a></td><td>Delete</td></tr>';
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
	$form->elements[] = new xFormElementTextField('theme_name','Theme name','','',FALSE,new xInputValidatorTextNameId(32));
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

function xanth_theme_admin_theme_edit($hook_primary_id,$hook_secondary_id,$arguments)
{
	if(!xUser::check_current_user_access('edit theme'))
	{
		return xSpecialPage::access_denied();
	}
	
	if(empty($arguments[0]))
	{
		new xPageContent('Edit Theme','Wich theme should edit?');
	}
	
	$form = new xForm('?p=admin/theme/edit//'.$arguments[0]);
	$form->elements[] = new  xFormElementHidden('theme_name','Theme Name',$arguments[0],FALSE,new xInputValidatorTextNameId(32));
	
	//iterate every registered visual element
	$visual_elements = xVisualElement::find_all();
	foreach($visual_elements as $visual_element)
	{
		//now iterate every registered view mode
		$view_modes = xViewMode::find_by_element($visual_element->name);
		$options = array();
		foreach($view_modes as $view_mode)
		{
			$options[$view_mode->name] = $view_mode->id;
		}
		
		$form->elements[] = new  xFormElementHidden('visual_elements',
			'Visual elements',$visual_element->name,TRUE,new xInputValidatorTextNameId(32));
		$form->elements[] = new xFormElementOptions('view_mode_'.$visual_element->name,
			'View Mode for v.e. '.$visual_element->name,'','',$options,FALSE,FALSE,new xInputValidatorInteger());
	}
	
	$form->elements[] = new xFormSubmit('submit','submit');

	$ret = $form->validate_input();
	if(isset($ret->valid_data['submit']))
	{
		if(empty($ret->errors))
		{
			//process form
			$themed_elements = array();
			foreach($ret->valid_data['visual_elements'] as $vename)
			{
				$themed_elements[$vename] = $ret->valid_data['view_mode_'.$vename];
			}
			$theme = new xTheme($ret->valid_data['theme_name'],$themed_elements);
			$theme->update();
			
			return new xPageContent('Edit Theme','Theme Edited');
		}
		else
		{
			foreach($ret->errors as $error)
			{
				xanth_log(LOG_LEVEL_USER_MESSAGE,$error);
			}
		}
	}

	return new xPageContent('Edit Theme',$form->render());
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
	xanth_register_mono_hook(MONO_HOOK_PAGE_CONTENT_CREATE, 'admin/theme/edit','xanth_theme_admin_theme_edit');
	
	xanth_register_multi_hook(MULTI_HOOK_ADMIN_MENU_ADD_PATH,NULL,'xanth_theme_admin_menu_add_link');
	xanth_register_multi_hook(MULTI_HOOK_ADMIN_MENU_ADD_PATH,NULL,'xanth_theme_admin_menu_add_link2');
}



?>