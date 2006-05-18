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
	 * @var int
	 * @access public
	 */
	var $m_resource_type_id;
	
	/**
	 * @var string
	 * @access public
	 */
	var $m_operation;
	
	/**
	 * @var string
	 * @access public
	 */
	var $m_role;
	
	/**
	 * Contructor
	 */
	function xAccessPermission($resource,$resource_type_id,$operation,$role)
	{
		$this->m_resource = $resource;
		$this->m_resource_type_id = $resource_type_id;
		$this->m_operation = $operation;
		$this->m_role = $role;
	}
	
	
	/**
	 *
	 */
	function dbInsert()
	{
		xAccessPermissionDAO::insert($this);
	}
	
	/**
	 *
	 */
	function dbDelete()
	{
		xAccessPermissionDAO::delete($this->m_resource,$this->m_resource_type_id,$this->m_operation,$this->m_role);
	}
	
		
	/**
	 *
	 * @return bool
	 */
	function checkPermissionForRole($role)
	{
		return checkPermission($this->m_resource,$this->m_resource_type_id,$this->m_operation,$role);
	}
	
	/**
	 *
	 * @return bool
	 * @static
	 */
	function checkPermission($resource,$resource_type_id,$operation,$role)
	{
		$perm = xAccessPermissionDAO::load($resource,$resource_type_id,$operation,$role);
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
	function checkCurrentUserPermission($resource,$resource_type_id,$operation)
	{
		$uid = xUser::getLoggedinUserid();
		if($uid === 0)
		{
			return xAccessPermission::checkPermission($resource,$resource_type_id,$operation,'anonymous');
		}
		else
		{
			//bypass if administrator
			if(xUser::currentHaveRole('administrator'))
			{
				return TRUE;
			}
			
			//check for authenticated role
			$perm = xAccessPermission::checkPermission($resource,$resource_type_id,$operation,'authenticated');
			if($perm)
			{
				return TRUE;
			}
			
			//check for other roles
			return xAccessPermission::checkUserPermission($resource,$resource_type_id,$operation,$uid);
		}
		
		return FALSE;
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
class xPermissionDescriptor()
{
	/**
	 * @var string
	 * @access public
	 */
	var $m_resource;
	
	/**
	 * @var int
	 * @access public
	 */
	var $m_resource_type_id;
	
	/**
	 * @var string
	 * @access public
	 */
	var $m_operation;
	
	/**
	 * @var string
	 * @access public
	 */
	var $m_description;
	
	
	function xPermissionDescriptor($resource,$resource_type_id,$operation,$description)
	{
		$this->m_resource = $resource;
		$this->m_resource_type_id = $resource_type_id;
		$this->m_operation = $operation;
		$this->m_description = $description;
	}
}


?>