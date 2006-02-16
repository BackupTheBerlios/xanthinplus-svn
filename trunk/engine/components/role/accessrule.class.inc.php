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


class xAccessRule
{
	var $name;
	var $rule_group;
	
	function xAccessRule($name,$rule_group)
	{
		$this->name = $name;
		$this->rule_group = $rule_group;
	}
	
		/**
	*
	*/
	function insert()
	{
		xanth_db_query("INSERT INTO access_rule(name,rule_group) VALUES ('%s','%s')",$this->name,$this->rule_group);
	}
	
	/**
	*
	*/
	function delete()
	{
		xanth_db_query("DELETE FROM access_rule WHERE name = '%s'",$this->name);
	}
	
	/**
	*
	*/
	function find_all()
	{
		$accesses = array();
		$result = xanth_db_query("SELECT * FROM access_rule");
		while($row = xanth_db_fetch_object($result))
		{
			$accesses[] = new xAccessRule($row->name,$row->rule_group);
		}
		return $accesses;
	}
	
	/**
	 *
	 */
	function find_by_group($group)
	{
		$accesses = array();
		$result = xanth_db_query("SELECT * FROM access_rule WHERE rule_group = '%s'",$rule_group);
		while($row = xanth_db_fetch_object($result))
		{
			$accesses[] = new xAccessRule($row->name,$row->rule_group);
		}
		return $accesses;
	}
	
	/**
	 *
	*/
	function exists($access_rule)
	{
		$result = xanth_db_query("SELECT * FROM access_rule WHERE name = '%s'",$access_rule);
		if($row = xanth_db_fetch_object($result))
		{
			return TRUE;
		}
		
		return FALSE;
	}
}
	
	
?>