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

require_once('engine/components/settings/settings.class.inc.php');


/**
*
*/
function xanth_settings_manage_settings($hook_primary_id,$hook_secondary_id,$arguments)
{
	if(!xUser::check_current_user_access('manage settings'))
	{
		xanth_log(LOG_LEVEL_ERROR,"Access denied");
		return new xPageContent("Access denied",'');
	}
	
	$form = new xForm('?p=admin/settings');
	$form->elements[] = new xFormElementTextField('site_name','Site name','',xSettings::get('site_name'),new xInputValidatorTextNoTags(256,FALSE));
	$form->elements[] = new xFormSubmit('submit','submit');
	
	$ret = $form->validate_input();
	if(isset($ret->valid_data['submit']))
	{
		if(empty($ret->errors))
		{
			xSettings::set('site_name',$ret->valid_data['site_name']);
			xSettings::save();
			return new xPageContent('Manage settings','Settings updated');
		}
		else
		{
			foreach($ret->errors as $error)
			{
				xanth_log(LOG_LEVEL_USER_MESSAGE,$error);
			}
		}
	}

	return new xPageContent('Manage settings',$form->render());
}


function xanth_settings_on_page_creation($hook_primary_id,$hook_secondary_id,$arguments)
{
	xSettings::load();
}


function xanth_settings_admin_menu_add_link($hook_primary_id,$hook_secondary_id,$arguments)
{
	return 'admin/settings';
}

/*
*
*/
function xanth_init_component_settings()
{
	xanth_register_mono_hook(MONO_HOOK_PAGE_CONTENT_CREATE, 'admin/settings','xanth_settings_manage_settings');
	
	xanth_register_multi_hook(MULTI_HOOK_ADMIN_MENU_ADD_PATH,NULL,'xanth_settings_admin_menu_add_link');
	
	xanth_register_multi_hook(MULTI_HOOK_PAGE_CREATE_EVT,'','xanth_settings_on_page_creation');
}



?>