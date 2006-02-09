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

class xCategory
{
	var $id;
	var $title;
	var $description;
	var $view_mode_id;
	var $parent_id;
	
	/**
	*
	*/
	function xCategory($id,$title = NULL,$description = NULL,$view_mode_id = 0,$parent_id = NULL)
	{
		$this->id = $id;
		$this->title = $title;
		$this->description = $description;
		$this->view_mode_id = $view_mode_id;
		$this->parent_id = $parent_id;
	}
	
	/**
	*
	*/
	function insert()
	{
		$field_names = "title,description";
		$field_values = "'%s','%s'";
		$values = array($this->title,$this->description);
		
		if(!empty($this->view_mode_id))
		{
			$field_names .= ",view_mode_id";
			$field_values .= ",'%d'";
			$values[] = $this->view_mode_id;
		}
		if(!empty($this->parent_id))
		{
			$field_names .= ",parent_id";
			$field_values .= ",'%d'";
			$values[] = $this->parent_id;
		}
		
		xanth_db_query("INSERT INTO category ($field_names) VALUES ($field_values)",$values);
		$this->id = xanth_db_get_last_id();
	}

	/**
	*
	*/
	function delete()
	{
		xanth_db_query("DELETE FROM category WHERE id = '%d'",$this->id);
	}

	/**
	*
	*/
	function find_root()
	{
		$result = xanth_db_query("SELECT * FROM category WHERE parent_id IS NUL");
		$categories = array();
		while($row = xanth_db_fetch_object($result))
		{
			$categories[] = new xCategory($row->id,$row->title,$row->description,$row->view_mode_id);
		}
		
		return $categories;
	}

	/**
	*
	*/
	function find_childs()
	{
		$result = xanth_db_query("SELECT * FROM category WHERE parent_id = %d",$this->parent_id);
		$categories = array();
		while($row = xanth_db_fetch_object($result))
		{
			$categories[] = new xCategory($row->id,$row->title,$row->description,$row->view_mode_id,$row->parentId);
		}
		
		return $categories;
	}


	/**
	*
	*/
	function find_all()
	{
		$result = xanth_db_query("SELECT * FROM category");
		$categories = array();
		while($row = xanth_db_fetch_object($result))
		{
			$categories[] = new xCategory($row->id,$row->title,$row->description,$row->view_mode_id,$row->parent_id);
		}
		
		return $categories;
	}
}



?>