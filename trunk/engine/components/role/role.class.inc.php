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
	var $name;
	var $description;
	
	function xRole($name,$description)
	{
		$this->name = $name;
		$this->description = $description;
	}
	
	/**
	*
	*/
	function insert()
	{
		xanth_db_query("INSERT INTO role(name,description) VALUES ('%s','%s')",$this->name,$this->description);
	}
	
	/**
	*
	*/
	function delete()
	{
		xanth_db_query("DELETE FROM role WHERE name = '%s'",$this->name);
	}
	
	/**
	*
	*/
	function update()
	{
		xanth_db_query("UPDATE role SET description = '%s' WHERE name = '%s')",$this->description,$this->name);
	}
	
	/**
	*
	*/
	function find_all()
	{
		$roles = array();
		$result = xanth_db_query("SELECT * FROM roles");
		while($row = xanth_db_fetch_object($result))
		{
			$roles[] = new xRole($row->name,$row->description);
		}
		return $roles;
	}
	
	/**
	*
	*/
	function associate_access_rule($access_rule)
	{
		xanth_db_query("INSERT INTO role_access_rule(roleName,access_rule) VALUES ('%s','%s')",$this->name,$access_rule);
	}
	
	/**
	*
	*/
	function dissociate_access_rule($access_rule)
	{
		xanth_db_query("DELETE FROM role_access_rule WHERE roleId = '%s' AND access_rule = '%s'",$this->name,$access_rule);
	}
	
	/**
	*
	*/
	function list_access_rules()
	{
		$rules = array();
		$result = xanth_db_query("SELECT * FROM role_access_rule WHERE roleName = '%s' ",$this->name);
		while($row = xanth_db_fetch_object($result))
		{
			$rules[] = $row['access_rule'];
		}
		return $rules;
	}
	
	
	/**
	 *
	 */
	function has_access_rule($access_rule)
	{
		$result = xanth_db_query("SELECT * FROM role_access_rule WHERE roleName = '%s' AND access_rule = '%s'",$this->name,$access_rule);
		if(xanth_db_fetch_object($result))
		{
			return TRUE;
		}
		return FALSE;
	}
}

?>