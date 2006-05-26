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
* Module responsible of settings management
*/
class xModuleSettings extends xModule
{
	function xModuleUser()
	{
		$this->xModule();
	}

	// DOCS INHERITHED  ========================================================
	function xm_contentFactory($path)
	{
		if($path->m_base_path == 'admin/settings')
		{
			return new xContentAdminSettings($path);
		}
		
		return NULL;
	}
	
	
	// DOCS INHERITHED  ========================================================
	function xm_onPageCreation()
	{
		//load settings
		xSettings::load();
	}
};

xModule::registerDefaultModule(new xModuleSettings());







/**
 * @internal
 */
class xContentAdminSettings extends xContent
{	
	function xContentAdminSettings($path)
	{
		$this->xContent($path);
	}

	// DOCS INHERITHED  ========================================================
	function onCheckPermission()
	{
		return xAccessPermission::checkCurrentUserPermission('settings','admin');
	}
	
	
	// DOCS INHERITHED  ========================================================
	function onCreate()
	{
		//create form
		$form = new xForm('?p=admin/settings');
		
		$form->m_elements[] = new xFormElementTextField('site_name','Site name','',xSettings::get('site_name'),
			FALSE,new xInputValidatorText(256));
		$form->m_elements[] = new xFormElementTextField('site_description','Site description','',xSettings::get('site_description'),
			FALSE,new xInputValidatorText(512));
		$form->m_elements[] = new xFormElementTextField('site_keywords','Site keywords','',xSettings::get('site_keywords'),
			FALSE,new xInputValidatorText(128));
		$form->m_elements[] = new xFormElementTextField('site_theme','Site theme','',xSettings::get('site_theme'),
			FALSE,new xInputValidatorText(128));
		
		//submit buttom
		$form->m_elements[] = new xFormSubmit('submit','Save');
		
		$ret = $form->validate();
		if(! $ret->isEmpty())
		{
			if(empty($ret->m_errors))
			{
				foreach($ret->m_valid_data as $var_name => $var_value)
				{
					xSettings::set($var_name,$var_value);
				}
				xSettings::save();

				xNotifications::add(NOTIFICATION_NOTICE,'Settings saved');

				xContent::_set("Admin settings",'','','');
				return true;
			}
			else
			{
				foreach($ret->m_errors as $error)
				{
					xNotifications::add(NOTIFICATION_WARNING,$error);
				}
			}
		}
		
		xContent::_set("Create new item page",$form->render(),'','');
		return TRUE;
	}
};


	
?>
