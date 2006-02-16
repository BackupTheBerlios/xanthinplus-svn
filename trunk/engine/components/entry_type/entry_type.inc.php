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

require_once('engine/components/entry_type/entrytype.class.inc.php');


/**
*
*/
function xanth_entry_type_admin_entry_type_add($hook_primary_id,$hook_secondary_id,$arguments)
{
	if(!xUser::check_current_user_access('manage entry type'))
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


function xanth_entry_type_admin_menu_add_link($hook_primary_id,$hook_secondary_id,$arguments)
{
	return 'admin/entry_type/add';
}

/*
*
*/
function xanth_init_component_entry_type()
{
	xanth_register_mono_hook(MONO_HOOK_PAGE_CONTENT_CREATE, 'admin/entry_type/add','xanth_entry_type_admin_entry_type_add');
	
	xanth_register_multi_hook(MULTI_HOOK_ADMIN_MENU_ADD_PATH,NULL,'xanth_entry_type_admin_menu_add_link');
}



?>