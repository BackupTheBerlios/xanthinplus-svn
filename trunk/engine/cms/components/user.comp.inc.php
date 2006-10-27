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


/**
* Module responsible of user management
*/
class xModuleUser extends xModule
{
	function xModuleUser()
	{
		$this->xModule();
	}


	// DOCS INHERITHED  ========================================================
	function xm_fetchContent($path)
	{
		if($path->m_resource === 'user' && $path->m_action === 'login')
		{
			return new xPageContentUserLogin($path);
		}
		elseif($path->m_resource === 'user' && $path->m_action === 'logout')
		{
			xUser::logout();
			return new xPageContentSimple("User logout",'Logged out','','',$path);
		}
		
		return NULL;
	}
	
	
	
	// DOCS INHERITHED  ========================================================
	function xm_onPageCreation()
	{
		//check the login
		xUser::checkUserLogin();
	}
};

xModule::registerDefaultModule(new xModuleUser());




/**
 * @internal
 */
class xPageContentUserLogin extends xPageContent
{	
	function xPageContentUserLogin($path)
	{
		$this->xPageContent($path);
	}
	
	// DOCS INHERITHED  ========================================================
	function onCheckPreconditions()
	{
		return TRUE;
	}
	
	
	// DOCS INHERITHED  ========================================================
	function onCreate()
	{
		$form = new xForm($this->m_path->getLink());
		$form->m_elements[] = new xFormElementTextField('username','Username','','',TRUE,new xInputValidatorText(255));
		$form->m_elements[] = new xFormElementPassword('password','Password','',TRUE,new xInputValidatorText(255));
		$form->m_elements[] = new xFormSubmit('submit','login');
		
		$ret = $form->validate();
		if(isset($ret->m_valid_data['submit']))
		{
			if(empty($ret->m_errors))
			{
				if($user = xUser::login($ret->m_valid_data['username'],$ret->m_valid_data['password'],TRUE) != NULL)
				{
					xPageContent::_set("User login",'Logged in','','');
					return TRUE;
				}
			}
			else
			{
				foreach($ret->m_errors as $error)
				{
					xNotifications::add(NOTIFICATION_WARNING,$error);
				}
			}
		}

		xPageContent::_set("User login",$form->render(),'','');
		return TRUE;
	}
};



	
?>
