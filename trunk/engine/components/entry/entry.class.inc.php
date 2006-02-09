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
class xEntry
{
	var $id;
	var $title;
	var $type;
	var $author;
	var $creation_time;
	var $content;
	var $content_format;
	var $categories;
	var $published;
	var $description;
	var $keywords;
	
	function xEntry($id = NULL,$title = NULL,$type = NULL,$author = NULL,$content = NULL,
		$content_format = NULL,$published = NULL,$description = NULL,$keywords = NULL,
		$categories = array(),$creation_time = NULL)
	{
		$this->id = $id;
		$this->title = $title;
		$this->type = $type;
		$this->author = $author;
		$this->creation_time = $creation_time;
		$this->content = $content;
		$this->content_format = $content_format;
		$this->categories = $categories;
		$this->published = $published;
		$this->description = $description;
		$this->keywords = $keywords;
	}
	
	
	/**
	*
	*/
	function insert()
	{
		xanth_db_start_transaction();
		$this->creation_time = time();
		xanth_db_query("INSERT INTO entry (title,type,author,content,content_format,published,description,keywords,creation_time)
			VALUES('%s','%s','%s','%s','%s',%d,'%s','%s','%s')",
			$this->title,$this->type,$this->author,$this->content,$this->content_format,$this->published,
			$this->description,$this->keywords,xanth_db_encode_timestamp($this->creation_time));
		
		$this->id = xanth_db_get_last_id();
		
		if(!empty($this->categories))
		{
			foreach($this->categories as $category)
			{
				xanth_db_query("INSERT INTO categorytoentry (entryId,catId) VALUES('%d','%d')",
					$this->id,$category->id);
			}
		}
		
		xanth_db_commit();
	}
	
	/**
	*
	*/
	function delete()
	{
		xanth_db_query("DELETE FROM entry WHERE id = %d",$this->id);
	}
	
	/**
	*
	*/
	function update()
	{
		xanth_db_start_transaction();
		
		xanth_db_query("UPDATE entry SET title = '%s',type = '%s',author = '%s',content = '%s',content_format = '%s', 
			published = %d ,description = '%s' ,keywords = '%s' WHERE id = %d",
			$this->id,$this->title,$this->type,$this->author,$this->content,$this->content_format,$this->published,
			$this->description,$this->keywords);
		
		xanth_db_query("DELETE FROM categorytoentry WHERE entryId = '%d'",$this->id);

		if(!empty($this->categories))
		{
			foreach($this->categories as $category)
			{
				xanth_db_query("INSERT INTO categorytoentry (entryId,catId) VALUES('%d','%d')",
					$this->id,$category->id);
			}
		}
		xanth_db_commit();
	}
	
	/**
	 *
	 */
	function get($entry_id)
	{
		xanth_db_start_transaction();
		
		$entry = NULL;
		$result = xanth_db_query("SELECT * FROM entry WHERE id = %d",$entry_id);
		if($row = xanth_db_fetch_object($result))
		{
			$entry = new xEntry($row->id,$row->title,$row->type,$row->author,$row->content,
				$row->content_format,$row->published,$row->description,$row->keywords,array(),
				xanth_db_decode_timestamp($row->creation_time));
			
			$result = xanth_db_query("SELECT * FROM categorytoentry,category WHERE entryId = %d AND category.id = catId",$entry_id);
			$categories = array();
			while($row = xanth_db_fetch_object($result))
			{
				$categories[] = new xCategory($row->id,$row->title,$row->parent_id);
			}
			$entry->categories = $categories;
		}

		xanth_db_commit();
		
		return $entry;
	}
	
	
	/**
	 *
	 */
	function find_all()
	{
		xanth_db_start_transaction();
		
		$entries = array();
		$result = xanth_db_query("SELECT * FROM entry");
		for($i = 0;$row = xanth_db_fetch_object($result);$i++)
		{
			$entries[$i] = new xEntry($row->id,$row->title,$row->type,$row->author,$row->content,
				$row->content_format,$row->published,$row->description,$row->keywords,
				array(),xanth_db_decode_timestamp($row->creation_time));
			
			$result = xanth_db_query("SELECT * FROM categorytoentry,category WHERE entryId = %d AND category.id = catId",$row->id);
			$categories = array();
			while($row = xanth_db_fetch_object($result))
			{
				$categories[] = new xCategory($row->id,$row->title,$row->parent_id);
			}
			$entry[$i]->categories = $categories;
		}

		xanth_db_commit();
		
		return $entries;
	}
}


















?>