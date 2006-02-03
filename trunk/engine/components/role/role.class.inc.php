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
	var $id;
	var $name;
	var $description;
	
	function xRole($id,$name,$description)
	{
		$this->id = $id;
		$this->name = $name;
		$this->description = $description;
	}
	
	/**
	*
	*/
	function insert()
	{
		xanth_db_query("INSERT INTO role(name,description) VALUES ('%s','%s')",$this->name,$this->description);
		$this->id = xanth_db_get_last_id();
	}
	
	/**
	*
	*/
	function delete()
	{
		xanth_db_query("DELETE FROM role WHERE id = %d",$this->id);
	}
	
	/**
	*
	*/
	function update()
	{
		xanth_db_query("UPDATE role SET name = '%s',description = '%s' WHERE id = %d)",$this->name,$this->description,$this->id);
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
			$roles[] = new xRole($row->id,$row->name,$row->description);
		}
		return $roles;
	}
	
	/**
	*
	*/
	function associate_access_rule($access_rule)
	{
		xanth_db_query("INSERT INTO role_access_rule(roleId,access_rule) VALUES (%d,'%s')",$this->id,$access_rule);
	}
	
	/**
	*
	*/
	function dissociate_access_rule($access_rule)
	{
		xanth_db_query("DELETE FROM role_access_rule WHERE roleId = %d AND access_rule = '%s'",$this->id,$access_rule);
	}
	
	/**
	*
	*/
	function list_access_rules()
	{
		$rules = array();
		$result = xanth_db_query("SELECT * FROM role_access_rule WHERE roleId = %d",$this->id);
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
		$result = xanth_db_query("SELECT * FROM role_access_rule WHERE roleId = %d AND access_rule = '%s'",$this->id,$access_rule);
		if(xanth_db_fetch_object($result))
		{
			return TRUE;
		}
		return FALSE;
	}
}

?>