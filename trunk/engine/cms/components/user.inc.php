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
		$this->xModule('User','engine/cms/components/');
	}

	/**
	 * @see xDummyModule::renderBoxContent()
	 */
	function renderBoxContent($id)
	{
		if($id == 'Login')
		{
			$username = xUser::getLoggedinUsername();
			if(!empty($username))
			{
				return "Logged as user $username<br /><a href=\"?p=user/logout\">Logout</a>";
			}
			
			return "User not logged in<br /><a href=\"?p=user/login\">Login</a>";
		}
		
		return NULL;
	}

	/**
	 * @see xDummyModule::getContent()
	 */
	function getContent($path)
	{
		//============================================================
		if($path->m_base_path == 'user/login')
		{
			$form = new xForm('?p=user/login');
			$form->m_elements[] = new xFormElementTextField('username','Username','','',TRUE,new xInputValidatorText(256));
			$form->m_elements[] = new xFormElementPassword('password','Password','',TRUE,new xInputValidatorText(256));
			$form->m_elements[] = new xFormSubmit('submit','login');
			
			$ret = $form->validate();
			if(isset($ret->m_valid_data['submit']))
			{
				if(empty($ret->m_errors))
				{
					if($user = xUser::login($ret->m_valid_data['username'],$ret->m_valid_data['password'],TRUE) != NULL)
					{
						return new xContentSimple('',"User login",'Logged in','','');
					}
				}
				else
				{
					foreach($ret->m_errors as $error)
					{
						xLog::log(LOG_LEVEL_USER_MESSAGE,$error);
					}
				}
			}
			return new xContentSimple('',"User login",$form->render(),'','');
		}
		
		//============================================================
		elseif($path->m_base_path == 'user/logout')
		{
			xUser::logout();
			return new xContentSimple('',"User logout",'Logged out','','');
		}
		
		//============================================================
		return NULL;
	}
	
	/**
	 * @see xDummyModule::onPageCreation()
	 */
	function onPageCreation()
	{
		//check the login
		xUser::checkUser();
	}
};

xModule::registerModule(new xModuleUser());
	
?>
