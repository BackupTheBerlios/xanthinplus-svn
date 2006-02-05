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


require_once('engine/components/user/user.class.inc.php');


function xanth_user_user_login($hook_primary_id,$hook_secondary_id,$arguments)
{
	$form = new xForm('?p=user/login');
	$form->elements[] = new xFormElementTextField('username','Username','','',new xInputValidatorText(256,TRUE));
	$form->elements[] = new xFormElementPassword('password','Password','','',new xInputValidatorText(256,TRUE));
	$form->elements[] = new xFormSubmit('submit','login');
	
	$ret = $form->validate_input();
	if(isset($ret->valid_data['submit']))
	{
		if(empty($ret->errors))
		{
			$user = new xUser('',$ret->valid_data['username']);
			if($user->login($ret->valid_data['password'],TRUE))
			{
				return 'Logged in';
			}
		}
		else
		{
			foreach($ret->errors as $error)
			{
				xanth_log(LOG_LEVEL_USER_MESSAGE,$error);
			}
		}
	}

	return $form->render();
}


function xanth_user_login_box($hook_primary_id,$hook_secondary_id,$arguments)
{
	$username = xUser::get_current_username();
	if(!empty($username))
	{
		return "Logged as user $username<br /><a href=\"?p=user/logout\">Logout</a>";
	}
	
	return "User not logged in<br /><a href=\"?p=user/login\">Login</a>";
}


function xanth_user_user_logout($hook_primary_id,$hook_secondary_id,$arguments)
{
	xUser::logout();
	return "Logged out";
}

function xanth_user_on_page_creation($hook_primary_id,$hook_secondary_id,$arguments)
{
	xUser::check_user();
}


/*
*
*/
function xanth_init_component_user()
{
	xanth_register_mono_hook(MONO_HOOK_MAIN_ENTRY_CREATE, 'user/login','xanth_user_user_login');
	xanth_register_mono_hook(MONO_HOOK_MAIN_ENTRY_CREATE, 'user/logout','xanth_user_user_logout');
	
	xanth_register_mono_hook(MONO_HOOK_CREATE_BOX_CONTENT,'login_box','xanth_user_login_box');
	
	xanth_register_multi_hook(MULTI_HOOK_PAGE_CREATE_EVT,'','xanth_user_on_page_creation');
}



?>