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
* Module responsible of access filters and permission+
*
* @todo optimize access permission
*/
class xModuleAccessControl extends xModule
{
	function xModuleAccessControl()
	{
		$this->xModule();
	}
	
	// DOCS INHERITHED  ========================================================
	function xm_fetchContent($path)
	{
		if($path->m_resource === 'accesspermissions' && $path->m_action === 'admin')
		{
			return new xResult(new xPageContentAdminAccesspermissions($path));
		}
		
		return NULL;
	}
};

xModule::registerDefaultModule(new xModuleAccessControl());



/**
 *
 *
 * @internal
 */
class xPageContentAdminAccesspermissions extends xPageContent
{

	function xPageContentAdminAccesspermissions($path)
	{
		xPageContent::xPageContent($path);
	}
	
	// DOCS INHERITHED  ========================================================
	function onCheckPreconditions()
	{
		if(xAccessPermission::checkCurrentUserPermission('admin/accesspermissions',NULL,NULL,'view'))
		{
			return TRUE;
		}
		
		return new xPageContentNotAuthorized($this->m_path);
	}
	
	// DOCS INHERITHED  ========================================================
	function onCreate()
	{
		//create form
		$form = new xFormAccessPermission('?p=' . $this->m_path->m_full_path);
		
		$ret = $form->validate();
		if(! $ret->isEmpty())
		{
			if(empty($ret->m_errors))
			{
				$walker = new xFormArrayInputWalker($ret->m_valid_data);
				while($curr = $walker->next('permission'))
				{
					if(count($curr) == 4)
					{
						list($value,$resource,$action,$role) = $curr;
						$type = NULL;
					}
					else
					{
						list($value,$resource,$action,$role,$type) = $curr;
					}
					
					$perm = new xAccessPermission($resource,$type,NULL,$action,$role);
					$perm_present = $perm->check();
					if($value)
					{
						if(! $perm_present)
						{
							if($perm->insert())
							{
								xNotifications::add(NOTIFICATION_NOTICE,'Permission updated successfully');
							}
							else
							{
								xNotifications::add(NOTIFICATION_ERROR,'Error while performing action');
							}
						}
					}
					else
					{
						if($perm_present)
						{
							if($perm->delete())
							{
								xNotifications::add(NOTIFICATION_NOTICE,'Permission updated successfully');
							}
							else
							{
								xNotifications::add(NOTIFICATION_ERROR,'Error while performing action');
							}
						}
					}
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
		
		xPageContent::_set("Access Permissions",$form->render(),'','');
		return true;
	}
	
}


?>
