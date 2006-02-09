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

require_once('engine/components/view_mode/viewmode.class.inc.php');
require_once('engine/components/view_mode/visualelement.class.inc.php');

//@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@

function xanth_view_mode_admin_view_mode($hook_primary_id,$hook_secondary_id,$arguments)
{
	if(!xUser::check_current_user_access('manage view_modes'))
	{
		return xSpecialPage::access_denied();
	}
	
	$modes = xViewMode::find_all();
	
	$output = "<table>\n";
	$output .= "<tr><th>Id</th><th>Name</th><th>Visual Element</th><th>Default</th><th>Edit</th><th>Delete</th></tr>\n";
	foreach($modes as $mode)
	{
		$output .= "<tr><td>".$mode->id."</td><td>".$mode->name."</td><td>".$mode->relative_visual_element."</td>
		<td>".$mode->default_for_element."</td><td>Edit</td><td>Delete</td></tr>";
	}
	$output .= "<table>\n";
	
	return new xPageContent('Admin View Modes',$output);

}

//@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@

function xanth_view_mode_admin_view_mode_add($hook_primary_id,$hook_secondary_id,$arguments)
{
	if(!xUser::check_current_user_access('add view_mode'))
	{
		return xSpecialPage::access_denied();
	}
	
	$form = new xForm('?p=admin/view_mode/add');
	$form->elements[] = new xFormElementTextField('view_mode_name','Name','','',FALSE,new xInputValidatorTextNoTags(32));
	
	//view elements option
	$velems = xVisualElement::find_all();
	$voptions = array();
	foreach($velems as $velem)
	{
		$voptions[$velem->name] = $velem->name;
	}
	$form->elements[] = new xFormElementOptions('visual_element','Visual Element','','',$voptions,FALSE,FALSE,new xInputValidatorTextNoTags(32));
	
	$form->elements[] = new xFormElementTextArea('view_mode_procedure','Procedure','','',FALSE,new xInputValidatorText(NULL));
	$form->elements[] = new xFormSubmit('submit','submit');
		
	$ret = $form->validate_input();
	if(isset($ret->valid_data['submit']))
	{
		if(empty($ret->errors))
		{
			$view = new xViewMode(NULL,$ret->valid_data['view_mode_name'],$ret->valid_data['visual_element'],
				FALSE,$ret->valid_data['view_mode_procedure']);
			$view->insert();
			
			return new xPageContent('Add View Mode','View mode Added');
		}
		else
		{
			foreach($ret->errors as $error)
			{
				xanth_log(LOG_LEVEL_USER_MESSAGE,$error);
			}
		}
	}

	return new xPageContent('Add View Mode',$form->render());
}

//@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@

function xanth_view_mode_admin_menu_add_link($hook_primary_id,$hook_secondary_id,$arguments)
{
	return 'admin/view_mode';
}
function xanth_view_mode_admin_menu_add_link2($hook_primary_id,$hook_secondary_id,$arguments)
{
	return 'admin/view_mode/add';
}

//@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@

function xanth_init_component_view_mode()
{
	xanth_register_mono_hook(MONO_HOOK_PAGE_CONTENT_CREATE, 'admin/view_mode','xanth_view_mode_admin_view_mode');
	xanth_register_mono_hook(MONO_HOOK_PAGE_CONTENT_CREATE, 'admin/view_mode/add','xanth_view_mode_admin_view_mode_add');
	
	xanth_register_multi_hook(MULTI_HOOK_ADMIN_MENU_ADD_PATH,NULL,'xanth_view_mode_admin_menu_add_link');
	xanth_register_multi_hook(MULTI_HOOK_ADMIN_MENU_ADD_PATH,NULL,'xanth_view_mode_admin_menu_add_link2');
}



?>