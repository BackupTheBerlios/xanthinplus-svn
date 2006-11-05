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

	/**
	 * @see xDummyModule::xm_fetchContent()
	 */ 
	function xm_fetchContent($path)
	{
		if($path->m_resource == 'settings' && $path->m_action == 'admin' && $path->m_type == NULL)
		{
			return new xResult(new xPageContentAdminSettings($path));
		}
		
		return NULL;
	}
	
	
	// DOCS INHERITHED  ========================================================
	function xm_onInit()
	{
		//load settings
		xSettings::load();
	}
};

xModule::registerDefaultModule(new xModuleSettings());







/**
 *
 */
class xPageContentAdminSettings extends xPageContent
{	
	function xPageContentAdminSettings($path)
	{
		$this->xPageContent($path);
	}
	
	// DOCS INHERITHED  ========================================================
	function onCheckPreconditions()
	{
		//check type permission
		if(!xAccessPermission::checkCurrentUserPermission('settings',NULL,NULL,'edit'))
			return new xPageContentNotAuthorized($this->m_path);
			
		return true;
	}
	
	
	// DOCS INHERITHED  ========================================================
	function onCreate()
	{
		//create form
		$form = new xForm('settings',xanth_relative_path($this->m_path->m_full_path));
		
		$form->m_elements[] = new xFormElementTextField('site_name','Site name','',xSettings::get('site_name'),
			FALSE,new xInputValidatorText(256));
		$form->m_elements[] = new xFormElementTextField('site_description','Site description','',xSettings::get('site_description'),
			FALSE,new xInputValidatorText(512));
		$form->m_elements[] = new xFormElementTextField('site_keywords','Site keywords','',xSettings::get('site_keywords'),
			FALSE,new xInputValidatorText(128));
		$form->m_elements[] = new xFormElementTextField('theme','Site theme','',xSettings::get('theme'),
			FALSE,new xInputValidatorText(128));
		$form->m_elements[] = new xFormElementTextField('default_lang','Default Language','',xSettings::get('default_lang'),
			FALSE,new xInputValidatorText(2));
		
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
				xSettings::dbSave();

				xNotifications::add(NOTIFICATION_NOTICE,'Settings saved');

				xPageContent::_set("Admin settings",'','','');
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
		
		xPageContent::_set("Create new item page",$form->render(),'','');
		return TRUE;
	}
};


	
?>
