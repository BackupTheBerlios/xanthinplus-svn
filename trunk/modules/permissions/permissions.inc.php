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
 * Defines an association between a permission name and a access filter set.
 */
class xAccessPermission
{
	/**
	 * @var string
	 * @access public
	 */
	var $m_resource;
	
	/**
	 * @var string
	 * @access public
	 */
	var $m_resource_type;
	
	/**
	 * @var int
	 * @access public
	 */
	var $m_resource_id;
	
	
	/**
	 * @var string
	 * @access public
	 */
	var $m_action;
	
	/**
	 * @var string
	 * @access public
	 */
	var $m_role;
	
	/**
	 * Contructor
	 *
	 * @param string $resource
	 * @param string $resource_type Can be NULL
	 * @param int $resource_id Can be NULL
	 * @param string $action
	 * @param string $role
	 */
	function xAccessPermission($resource,$resource_type,$resource_id,$action,$role)
	{
		$this->m_resource = $resource;
		$this->m_resource_type = $resource_type;
		$this->m_resource_id = $resource_id;
		$this->m_action = $action;
		$this->m_role = $role;
	}
	
	
	/**
	 * @return bool FALSE on error
	 */
	function insert()
	{
		return xAccessPermissionDAO::insert($this);
	}
	
	/**
	 * @return bool FALSE on error
	 */
	function delete()
	{
		return xAccessPermissionDAO::delete($this->m_resource,$this->m_resource_type,$this->m_resource_id,
			$this->m_action,$this->m_role);
	}

	
		
	/**
	 * @return bool
	 */
	function check()
	{
		return xAccessPermission::checkPermission($this->m_resource,$this->m_resource_type,$this->m_resource_id,
			$this->m_action,$this->m_role);
	}
	
	/**
	 *
	 * @return bool
	 * @static
	 */
	function checkPermission($resource,$resource_type,$resource_id,$action,$role)
	{
		$perm = xAccessPermissionDAO::load($resource,$resource_type,$resource_id,$action,$role);
		if($perm === NULL)
		{
			return FALSE;
		}
		
		return TRUE;
	}

	
	/**
	 *
	 * @return bool
	 * @static
	 */
	function checkUserPermission($resource,$resource_type,$resource_id,$action,$uid)
	{
		return xAccessPermissionDAO::checkUserPermission($resource,$resource_type,$resource_id,$action,$uid);
	}
	
	
	/**
	 *
	 * @return bool
	 * @static
	 */
	function checkCurrentUserPermission($resource,$resource_type,$resource_id,$action)
	{
		$uid = xUser::getLoggedinUserid();
		if($uid === 0)
		{
			return xAccessPermission::checkPermission($resource,$resource_type,$resource_id,$action,'anonymous');
		}
		else
		{
			//bypass if administrator
			if(xUser::currentHaveRole('administrator'))
			{
				return TRUE;
			}
			
			//check for authenticated role
			$perm = xAccessPermission::checkPermission($resource,$resource_type,$resource_id,$action,'authenticated');
			if($perm)
			{
				return TRUE;
			}
			
			//check for other roles
			return xAccessPermission::checkUserPermission($resource,$resource_type,$resource_id,$action,$uid);
		}
	}
	
	/**
	 *
	 * @return bool
	 * @static
	 */
	function checkCurrentUserPermissionPath($path)
	{
		return xAccessPermission::checkCurrentUserPermission($path->m_resource,$path->m_type,
			NULL,$path->m_action);
	}
	
	
	/**
	 * Retrieve all access permissions
	 *
	 * @return array(xAccessPermission)
	 * @static
	 */
	function findAll()
	{
		return xAccessPermissionDAO::findAll();
	}
}



/**
 * Represent a description of an access permission
 */
class xAccessPermissionDescriptor
{
	/**
	 * @var string
	 * @access public
	 */
	var $m_resource;
	
	/**
	 * @var string
	 * @access public
	 */
	var $m_resource_type;
	
	/**
	 * @var int
	 * @access public
	 */
	var $m_resource_id;
	
	/**
	 * @var string
	 * @access public
	 */
	var $m_action;
	
	/**
	 * @var string
	 * @access public
	 */
	var $m_description;
	
	
	function xAccessPermissionDescriptor($resource,$resource_type,$resource_id,$action,$description)
	{
		$this->m_resource = $resource;
		$this->m_resource_type = $resource_type;
		$this->m_resource_id = $resource_id;
		$this->m_action = $action;
		$this->m_description = $description;
	}
}




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
			return new xResult(new xContentAdminAccesspermissions($path));
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
class xContentAdminAccesspermissions extends xContent
{

	function xContentAdminAccesspermissions($path)
	{
		xContent::xContent($path);
	}
	
	// DOCS INHERITHED  ========================================================
	function onCheckPreconditions()
	{
		if(xAccessPermission::checkCurrentUserPermission('admin/accesspermissions',NULL,NULL,'view'))
		{
			return TRUE;
		}
		
		return new xContentNotAuthorized($this->m_path);
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
		
		xContent::_set("Access Permissions",$form->render(),'','');
		return true;
	}
	
}


?>
