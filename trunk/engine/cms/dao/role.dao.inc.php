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
	function xRole()
	{
		//non instaltiable
		assert(FALSE);
	}
	
	/**
	 * Insert a new role 
	 *
	 * @param xRole $role The role to insert
	 * @static
	 */
	function insert($role)
	{
		xDB::getDB()->query("INSERT INTO role(name,description) VALUES ('%s','%s')",$role->m_name,$role->m_description);
	}
	
	/**
	 * Deletes a role. Based on key.
	 *
	 * 
	 * @param xRole $role The role to delete
	 * @static
	 */
	function delete($role)
	{
		xDB::getDB()->query("DELETE FROM role WHERE name = '%s'",$role->m_name);
	}
	
	/**
	 * Updates a role.
	 *
	 * 
	 * @param xRole $role The role to update
	 * @static
	 */
	function update($role)
	{
		xDB::getDB()->query("UPDATE role SET description = '%s' WHERE name = '%s')",$role->m_description,$role->m_name);
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
			$roles[] = new xRole($row->name,$row->description);
		}
		return $roles;
	}
	
	/**
	 * Gives a new access rule to a role.
	 *
	 * @param xRole $role
	 * @param string $access_rule
	 * @static
	 */
	function giveAccessRule($role,$access_rule)
	{
		xDB::getDB()->query("INSERT INTO role_access_rule(roleName,access_rule) VALUES ('%s','%s')",
			$role->m_name,$access_rule);
	}
	
	/**
	* Takes off from a role an access rule
	*
	* @param xRole $role
	* @param string $access_rule
	* @static
	*/
	function takeoffAccessRule($role,$access_rule)
	{
		xDB::getDB()->query("DELETE FROM role_access_rule WHERE roleId = '%s' AND access_rule = '%s'",
			$role->m_name,$access_rule);
	}
	
	/**
	 * Check if a particular role have an access rule
	 *
	 * @param xRole $role
	 * @param string $access_rule
	 * @return bool
	 * @static
	 */
	function roleHaveAccess($role,$access_rule)
	{
		$result = xDB::getDB()->query("SELECT * FROM role_access_rule WHERE roleName = '%s' AND access_rule = '%s'",
			$role->m_name,$access_rule);
		if(xDB::getDB()->fetchObject($result))
		{
			return TRUE;
		}
		return FALSE;
	}
}

?>