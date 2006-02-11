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


/**
*
*/
class xCategory
{
	var $id;
	var $title;
	var $description;
	var $entry_type_names;
	var $view_mode_id;
	var $parent_id;
	
	/**
	*
	*/
	function xCategory($id = NULL,$title = NULL,$entry_type_names = array(),$description = NULL,$view_mode_id = 0,$parent_id = NULL)
	{
		$this->id = $id;
		$this->title = $title;
		$this->description = $description;
		$this->view_mode_id = $view_mode_id;
		$this->parent_id = $parent_id;
		$this->entry_type_names = $entry_type_names;
	}
	
	/**
	*
	*/
	function insert()
	{
		xanth_db_start_transaction();
		
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
		
		if(!empty($this->entry_type_names))
		{
			foreach($this->entry_type_names as $type)
			{
				xanth_db_query("INSERT INTO category_to_entry_type (cat_id,entry_type) VALUES ('%s','%s')",$this->id,$type);
			}
		}
		
		xanth_db_commit();
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
	function _get_entry_types_by_cat_id($cat_id)
	{
		$result = xanth_db_query("SELECT * FROM category_to_entry_type WHERE cat_id = %d",$cat_id);
		$types = array();
		while($row = xanth_db_fetch_object($result))
		{
			$types[] = $row->entry_type;
		}
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
			$categories[] = new xCategory($row->id,$row->title,xCategory::_get_entry_types_by_cat_id($row->id),
				$row->entry_types,$row->description,$row->view_mode_id);
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
			$categories[] = new xCategory($row->id,$row->title,xCategory::_get_entry_types_by_cat_id($row->id),
				$row->description,$row->view_mode_id,$row->parentId);
		}
		
		return $categories;
	}

	/**
	*
	*/
	function get($cat_id)
	{
		$result = xanth_db_query("SELECT * FROM category WHERE id = %d",$cat_id);
		while($row = xanth_db_fetch_object($result))
		{
			return new xCategory($row->id,$row->title,xCategory::_get_entry_types_by_cat_id($row->id),
				$row->description,$row->view_mode_id,$row->parent_id);
		}
		
		return NULL;
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
			$categories[] = new xCategory($row->id,$row->title,xCategory::_get_entry_types_by_cat_id($row->id),
				$row->description,$row->view_mode_id,$row->parent_id);
		}
		
		return $categories;
	}
	
	/**
	*
	*/
	function find_by_entry_type($entry_type)
	{
		$categories = array();
		$result = xanth_db_query("SELECT * FROM category_to_entry_type WHERE entry_type = '%s'",$entry_type);
		while($row = xanth_db_fetch_object($result))
		{
			$categories[] = get($row->cat_id);
		}
		
		return $categories;
	}
}



?>