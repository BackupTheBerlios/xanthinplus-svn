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
		xDB::getDB()->query("INSERT INTO access_permission(name,filterset) VALUES ('%s',%d)",
			$access_permission->m_name,$access_permission->m_filterset);
	}
	
	/**
	 *
	 * @param string $name
	 * @static
	 */
	function delete($name)
	{
		xDB::getDB()->query("DELETE FROM access_permission WHERE name = '%s'",$access_permission->m_name);
	}
	
	
	/**
	 * Retrieve a complete access permission
	 *
	 * @param string $name
	 * @return xAccessPermission The loaded object or NULL if not found
	 * @static
	 */
	function load($name)
	{
		$result = xDB::getDB()->query("SELECT * FROM access_permission WHERE name = '%s'",$name);
		if($row = xDB::getDB()->fetchObject($result))
		{
			return new xAccessPermission($row->name,$row->filterset);
		}
		
		return NULL;
	}
}

?>