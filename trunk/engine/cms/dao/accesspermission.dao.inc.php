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
 *
 */
class xAccessPermissionDAO
{
	function xAccessPermissionDAO()
	{
		//non instaltiable
		assert(FALSE);
	}
	
	/**
	 *
	 * @param xAccessPermission $access_permission
	 * @return bool FALSE on error
	 * @static
	 */
	function insert($access_permission)
	{
		return xDB::getDB()->query("INSERT INTO access_permission(resource,resource_type,action,role) 
			VALUES ('%s','%s','%s','%s')",
			$access_permission->m_resource,(string) $access_permission->m_resource_type,$access_permission->m_action,
			$access_permission->m_role);
	}
	
	/**
	 *
	 * @return bool FALSE on error
	 * @static
	 */
	function delete($resource,$resource_type,$action,$role)
	{
		return xDB::getDB()->query("DELETE FROM access_permission WHERE resource = '%s' AND resource_type = '%s' 
			AND action = '%s' AND role = '%s'",$resource,(string) $resource_type,$action,$role);
	}
	
	
	/**
	 *
	 * @param string $name
	 * @static
	 */
	function checkUserPermission($resource,$resource_type,$action,$uid)
	{
		$result = xDB::getDB()->query("SELECT access_permission.resource FROM access_permission,user_to_role 
			WHERE user_to_role.userid = %d AND access_permission.role = user_to_role.roleName 
			AND access_permission.resource = '%s' AND access_permission.resource_type = '%s' 
			AND access_permission.action = '%s'",$uid,$resource,(string) $resource_type,$action);
			
		if($row = xDB::getDB()->fetchObject($result))
		{
			return TRUE;
		}
		
		return FALSE;
	}
	
	
	/**
	 *
	 * @return xItem
	 * @static
	 * @access private
	 */
	function _accesspermissionFromRow($row_object)
	{
		return new xAccessPermission($row_object->resource,$row_object->action,
			$row_object->role,$row_object->resource_type);
	}
	
	/**
	 * Retrieve a complete access permission
	 *
	 * @param string $name
	 * @return xAccessPermission The loaded object or NULL if not found
	 * @static
	 */
	function load($resource,$resource_type,$action,$role)
	{
		$result = xDB::getDB()->query("SELECT * FROM access_permission WHERE resource = '%s' AND resource_type = '%s' 
			AND action = '%s' AND role = '%s'",$resource,(string) $resource_type,$action,$role);
		
		if($row = xDB::getDB()->fetchObject($result))
		{
			return xAccessPermissionDAO::_accesspermissionFromRow($row);
		}
		
		return NULL;
	}
	
	/**
	 * Retrieve all access permissions
	 *
	 * @return array(xAccessPermission)
	 * @static
	 */
	function findAll()
	{
		$permissions = array();
		$result = xDB::getDB()->query("SELECT * FROM access_permission");
		while($row = xDB::getDB()->fetchObject($result))
		{
			$permissions[] = xAccessPermissionDAO::_accesspermissionFromRow($row);
		}
		
		return $permissions;
	}
}

?>