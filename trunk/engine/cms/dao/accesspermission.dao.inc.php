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
		$field_names = "resource,action,role";
		$field_values = "'%s','%s','%s'";
		$values = array($access_permission->m_resource,$access_permission->m_action,$access_permission->m_role);
		
		if(!empty($access_permission->m_resource_type))
		{
			$field_names .= ',resource_type';
			$field_values .= ",'%s'";
			$values[] = $access_permission->m_resource_type;
		}
		
		if(!empty($access_permission->m_resource_id))
		{
			$field_names .= ',resource_id';
			$field_values .= ",%d";
			$values[] = $access_permission->m_resource_id;
		}
		
		if(! xDB::getDB()->query("INSERT INTO access_permission($field_names) VALUES($field_values)",$values))
			return false;
		
		return true;
	}
	
	/**
	 *
	 * @return bool FALSE on error
	 * @static
	 */
	function delete($resource,$resource_type,$resource_id,$action,$role)
	{
		$fields = "resource = '%s' AND action = '%s' AND role = '%s' ";
		$values = array($resource,$action,$role);
		
		if(!empty($resource_type))
		{
			$fields .= 'AND resource_type IS NULL ';
		}
		else
		{
			$fields .= "AND resource_type = '%s' ";
			$values[] = $resource_type;
		}
		
		if(!empty($resource_id))
		{
			$fields .= 'AND resource_id IS NULL ';
		}
		else
		{
			$fields .= "AND resource_id = %d ";
			$values[] = $resource_id;
		}
		
		if(! xDB::getDB()->query("DELETE FROM access_permission WHERE $fields",$values))
			return false;
		
		return true;
	}
	
	
	/**
	 *
	 * @param string $name
	 * @static
	 */
	function checkUserPermission($resource,$resource_type,$resource_id,$action,$uid)
	{
		$fields = "access_permission.resource = '%s' AND access_permission.action = '%s' ";
		$values = array($uid,$resource,$action);
		
		if(!empty($resource_type))
		{
			$fields .= 'AND access_permission.resource_type IS NULL ';
		}
		else
		{
			$fields .= "AND access_permission.resource_type = '%s' ";
			$values[] = $resource_type;
		}
		
		if(!empty($resource_id))
		{
			$fields .= 'AND access_permission.resource_id IS NULL ';
		}
		else
		{
			$fields .= "AND access_permission.resource_id = %d ";
			$values[] = $resource_id;
		}
		
		
		$result = xDB::getDB()->query("SELECT access_permission.resource FROM access_permission,user_to_role 
			WHERE user_to_role.userid = %d AND access_permission.role = user_to_role.roleName $fields",$values);
			
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
		return new xAccessPermission($row_object->resource,$row_object->resource_type,$row_object->resource_id,
			$row_object->action,$row_object->role);
	}
	
	/**
	 * Retrieve a complete access permission
	 *
	 * @param string $name
	 * @return xAccessPermission The loaded object or NULL if not found
	 * @static
	 */
	function load($resource,$resource_type,$resource_id,$action,$role)
	{
		$fields = "resource = '%s' AND action = '%s' AND role = '%s'";
		$values = array($resource,$action,$role);
		
		if(!empty($resource_type))
		{
			$fields .= 'AND resource_type IS NULL ';
		}
		else
		{
			$fields .= "AND resource_type = '%s' ";
			$values[] = $resource_type;
		}
		
		if(!empty($resource_id))
		{
			$fields .= 'AND resource_id IS NULL ';
		}
		else
		{
			$fields .= "AND  resource_id = %d ";
			$values[] = $resource_id;
		}
		
		$result = xDB::getDB()->query("SELECT * FROM access_permission WHERE $fields",$values);
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