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
class xViewMode
{
	var $id;
	var $name;
	var $relative_visual_element;
	var $display_procedure;
	var $default_for_element;
	
	
	function xViewMode($id,$name,$relative_visual_element,$default_for_element,$display_procedure)
	{
		$this->id = $id;
		$this->name = $name;
		$this->relative_visual_element = $relative_visual_element;
		$this->display_procedure = $display_procedure;
		$this->default_for_element = $default_for_element;
	}
	
	/**
	*
	*/
	function insert()
	{
		xanth_db_query("INSERT INTO view_mode (name,relative_visual_element,default_for_element,display_procedure) 
			VALUES ('%s','%s',%d,'%s')",$this->name,$this->relative_visual_element,$this->default_for_element,
			$this->display_procedure);
		$this->id = xanth_db_get_last_id();
	}
	
	/**
	*
	*/
	function delete()
	{
		xanth_db_query("DELETE FROM view_mode WHERE id = %d",$this->id);
	}
	
	/**
	*
	*/
	function update()
	{
		xanth_db_query("UPDATE view_mode SET name = '%s',display_procedure = '%s'",$this->name,$this->display_procedure);
	}
	
	/**
	*
	*/
	function find_by_element($visual_element)
	{
		$modes = array();
		$result = xanth_db_query("SELECT * FROM view_mode WHERE relative_visual_element = '%s'",$visual_element);
		while($row = xanth_db_fetch_object($result))
		{
			$modes[] = new xViewMode($row->id,$row->name,$row->relative_visual_element,$row->default_for_element,$row->display_procedure);
		}
		return $modes;
	}
	
	/**
	*
	*/
	function find_all()
	{
		$modes = array();
		$result = xanth_db_query("SELECT * FROM view_mode");
		while($row = xanth_db_fetch_object($result))
		{
			$modes[] = new xViewMode($row->id,$row->name,$row->relative_visual_element,$row->default_for_element,$row->display_procedure);
		}
		return $modes;
	}
	
		
	/**
	*
	*/
	function get_default_for_element($visual_element)
	{
		$modes = array();
		$result = xanth_db_query("SELECT * FROM view_mode WHERE relative_visual_element = '%s' AND default_for_element = %d",
			$visual_element,TRUE);
		if($row = xanth_db_fetch_object($result))
		{
			return new xViewMode($row->id,$row->name,$row->relative_visual_element,$row->default_for_element,$row->display_procedure);
		}
		return NULL;
	}
}




?>