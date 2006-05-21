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


class xRoleDAO
{
	function xRoleDAO()
	{
		//non instaltiable
		assert(FALSE);
	}
	
	/**
	 * Insert a new role 
	 *
	 * @param xRole $role The role to insert
	 * @return bool FALSE on error
	 * @static
	 */
	function insert($role)
	{
		return xDB::getDB()->query("INSERT INTO role(name,description) VALUES ('%s','%s')",$role->m_name,$role->m_description);
	}
	
	/**
	 * Deletes a role. Based on key.
	 *
	 * @param string $rolename
	 * @return bool FALSE on error
	 * @static
	 */
	function delete($rolename)
	{
		return xDB::getDB()->query("DELETE FROM role WHERE name = '%s'",$rolename);
	}
	
	/**
	 * Updates a role.
	 *
	 * @param xRole $role The role to update
	 * @return bool FALSE on error
	 * @static
	 */
	function update($role)
	{
		return xDB::getDB()->query("UPDATE role SET description = '%s' WHERE name = '%s')",$role->m_description,$role->m_name);
	}
	
	
	/**
	 *
	 * @return xItem
	 * @static
	 * @access private
	 */
	function _roleFromRow($row_object)
	{
		return new xRole($row_object->name,$row_object->description);
	}
	
	
	/**
	 * Retrieves all roles.
	 *
	 * @return array(xRole)
	 * @static
	 */
	function findAll()
	{
		$roles = array();
		$result = xDB::getDB()->query("SELECT * FROM roles");
		while($row = xDB::getDB()->fetchObject($result))
		{
			$roles[] = xRoleDAO::_roleFromRow($row);
		}
		return $roles;
	}
}

?>