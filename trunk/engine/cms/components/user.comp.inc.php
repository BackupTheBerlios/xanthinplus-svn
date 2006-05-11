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

	// DOCS INHERITHED  ========================================================
	function getDynamicBox($box)
	{
		if($box->m_name == 'Login')
		{
			return new xBoxLogin($box->m_name,$box->m_title,$box->m_type,$box->m_area);
		}
		
		return NULL;
	}

	/**
	 * @access private
	 */
	function _getContentLogin()
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
					return new xContentSimple("User login",'Logged in','','');
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
		return new xContentSimple("User login",$form->render(),'','');
	}

	// DOCS INHERITHED  ========================================================
	function getContent($path)
	{
		if($path->m_base_path == 'user/login')
		{
			return $this->_getContentLogin();
		}
		elseif($path->m_base_path == 'user/logout')
		{
			xUser::logout();
			return new xContentSimple("User logout",'Logged out','','');
		}
		
		return NULL;
	}
	
	
	
	// DOCS INHERITHED  ========================================================
	function onPageCreation()
	{
		//check the login
		xUser::checkUser();
	}
	
	// DOCS INHERITHED  ========================================================
	function getMenuItem($box_name)
	{
		if($box_name == 'Admin')
		{
			return new xMenuItem('Test','?p=test');
		}
		
		return NULL;
	}
};

xModule::registerModule(new xModuleUser());


/**
 * A box for dysplaing login info.
 */
class xBoxLogin extends xBoxDynamic
{
	function xBoxLogin($name,$title,$type,$area = NULL)
	{
		xBoxDynamic::xBoxDynamic($name,$title,$type,$area);
	}
	
	// DOCS INHERITHED  ========================================================
	function render()
	{
		$username = xUser::getLoggedinUsername();
		if(!empty($username))
		{
			$content = "Logged as user $username<br /><a href=\"?p=user/logout\">Logout</a>";
		}
		else
		{
			$content = "User not logged in<br /><a href=\"?p=user/login\">Login</a>";
		}
		
		return xTheme::getActive()->renderBox($this->m_name,$this->m_title,$content);
	}
}
	
?>
