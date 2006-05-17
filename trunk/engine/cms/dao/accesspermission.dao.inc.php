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
	 * @static
	 */
	function insert($access_permission)
	{
		xDB::getDB()->query("INSERT INTO access_permission(resource,resource_type_id,operation,role,description) 
			VALUES ('%s',%d,'%s','%s','%s')",
			$access_permission->m_resource,$access_permission->m_resource_type_id,$access_permission->m_operation,
			$access_permission->m_role,$access_permission->m_description);
	}
	
	/**
	 *
	 * @param string $name
	 * @static
	 */
	function delete($resource,$resource_type_id,$operation,$role)
	{
		xDB::getDB()->query("DELETE FROM access_permission WHERE resource = '%s' AND resource_type_id = %d 
			AND operation = '%s' AND role = '%s'",$resource,$resource_type_id,$operation,$role);
	}
	
	
	/**
	 *
	 * @param string $name
	 * @static
	 */
	function checkUserPermission($resource,$resource_type_id,$operation,$id)
	{
		xDB::getDB()->query("DELETE FROM access_permission WHERE resource = '%s' AND resource_type_id = %d 
			AND operation = '%s' AND role = '%s'",$resource,$resource_type_id,$operation,$role);
	}
	
	
	/**
	 *
	 * @return xItem
	 * @static
	 * @access private
	 */
	function _accesspermissionFromRow($row_object)
	{
		return new xAccessPermission($row_object->resource,$row_object->resource_type_id,$row_object->operation,
			$row_object->role,$row_object->description);
	}
	
	/**
	 * Retrieve a complete access permission
	 *
	 * @param string $name
	 * @return xAccessPermission The loaded object or NULL if not found
	 * @static
	 */
	function load($resource,$resource_type_id,$operation,$role)
	{
		$result = xDB::getDB()->query("SELECT * FROM access_permission WHERE resource = '%s' AND resource_type_id = %d 
			AND operation = '%s' AND role = '%s'",$resource,$resource_type_id,$operation,$role);
		
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