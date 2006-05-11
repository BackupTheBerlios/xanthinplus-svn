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


class xRole
{
	/**
	 * @var string
	 * @access public
	 */
	var $m_name;
	
	/**
	 * @var string
	 * @access public
	 */
	var $m_description;
	
	
	function xRole($name,$description)
	{
		$this->m_name = $name;
		$this->m_description = $description;
	}
	
	/**
	 * Insert this xRole into database
	 */ 
	function dbInsert()
	{
		xRoleDAO::insert($this);
	}
	
	/**
	 * Delete this xRole from db. Using name.
	 */ 
	function dbDelete()
	{
		xRoleDAO::delete($this->m_name);
	}
	
	/**
	 * Update this xRole in database.
	 */
	function dbUpdate()
	{
		xRoleDAO::update($this);
	}
	
	/**
	 * Retrieves all roles from db.
	 *
	 * @return array(xRole)
	 */
	function findAll()
	{
		return xRoleDAO::findAll();
	}
	
	/**
	 * Gives a new access rule to this role
	 *
	 * @param string $access_rule
	 */
	function giveAccessRule($access_rule)
	{
		xRoleDAO::giveAccessRule($this->m_name,$access_rule);
	}
	
	/**
	* Takes off from this roel an access rule
	*
	* @param string $access_rule
	*/
	function takeoffAccessRule($access_rule)
	{
		xRoleDAO::takeoffAccessRule($this->m_name,$access_rule);
	}
	
	/**
	 * Check if this role have an access rule
	 *
	 * @param string $access_rule
	 * @return bool
	 * @static
	 */
	function haveAccess($access_rule)
	{
		return xRoleDAO::haveAccess($this->m_name,$access_rule);
	}
}

?>