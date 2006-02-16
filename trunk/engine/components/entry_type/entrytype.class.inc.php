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
class xEntryType
{
	var $name;
	var $view_mode_id;
	
	function xEntryType($name,$view_mode_id)
	{
		$this->name = $name;
		$this->view_mode_id = $view_mode_id;
	}
	
	/**
	*
	*/
	function insert()
	{
		if(empty($this->view_mode_id))
		{
			xanth_db_query("INSERT INTO entry_type (name) VALUES ('%s')",$this->name);
		}
		else
		{
			xanth_db_query("INSERT INTO entry_type (name,view_mode_id) VALUES ('%s',%d)",$this->name,$this->view_mode_id);
		}
	}
	
	/**
	*
	*/
	function delete()
	{
		xanth_db_query("DELETE FROM entry_type WHERE name = '%s'",$this->name);
	}
	
	/**
	*
	*/
	function update()
	{
		xanth_db_query("UPDATE entry_type SET view_mode_id = %d WHERE name = '%s'",$this->view_mode_id,$this-name);
	}
	
	/**
	*
	*/
	function find_all()
	{
		$types = array();
		$result = xanth_db_query("SELECT * FROM entry_type");
		while($row = xanth_db_fetch_object($result))
		{
			$types[] = new xEntryType($row->name,$row->view_mode_id);
		}
		
		return $types;
	}
	
	/**
	*
	*/
	function get($name)
	{
		$result = xanth_db_query("SELECT * FROM entry_type WHERE name = '%s'",$name);
		if($row = xanth_db_fetch_object($result))
		{
			return new xEntryType($row->name,$row->view_mode_id);
		}
		
		return NULL;
	}
}

?>