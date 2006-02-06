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
	var $display_mode;
	var $parent_id;
	
	/**
	*
	*/
	function xCategory($id,$title,$description,$display_mode,$parent_id = NULL)
	{
		$this->id = $id;
		$this->title = $title;
		$this->description = $description;
		$this->display_mode = $display_mode;
		$this->parent_id = $parent_id;
	}
	
	/**
	*
	*/
	function insert()
	{
		if($this->parent_id === NULL || $this->parent_id == 0)
		{
			xanth_db_query("INSERT INTO category (title,description,display_mode) VALUES ('%s','%s','%s')",
				$this->title,$this->description,$this->display_mode);
		}
		else
		{
			xanth_db_query("INSERT INTO category (title,parent_id,description,display_mode) VALUES ('%s',%d,'%s','%s')",
				$this->title,$this->parent_id,$this->description,$this->display_mode);
		}
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
		$result = xanth_db_query("SELECT * FROM category WHERE parentId IS NUL");
		$categories = array();
		while($row = xanth_db_fetch_object($result))
		{
			$categories[] = new xCategory($row->id,$row->title,$row->description,$row->display_mode);
		}
		
		return $categories;
	}

	/**
	*
	*/
	function find_childs()
	{
		$result = xanth_db_query("SELECT * FROM category WHERE parentId = '%d'",$this->parent_id);
		$categories = array();
		while($row = xanth_db_fetch_object($result))
		{
			$categories[] = new xCategory($row->id,$row->title,$row->description,$row->display_mode,$row->parentId);
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
			$categories[] = new xCategory($row->id,$row->title,$row->description,$row->display_mode,$row->parent_id);
		}
		
		return $categories;
	}

}



?>