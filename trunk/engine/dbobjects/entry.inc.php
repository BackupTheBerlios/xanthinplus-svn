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



class xanthEntry
{
	var $id;
	var $title;
	var $type;
	var $author;
	var $creation_time;
	var $content;
	var $content_format;
	var $categories;
	
	function xanthEntry($id = NULL,$title = NULL,$type = NULL,$author = NULL,$content = NULL,
		$content_format = NULL,$categories = NULL,$creation_time = NULL)
	{
		$this->set_id($id);
		$this->set_title($title);
		$this->set_type($type);
		$this->set_author($author);
		$this->set_creation_time($creation_time);
		$this->set_content($content);
		$this->set_content_format($content_format);
		$this->set_categories($categories);
	}
	
	function set_id($id)
	{$this->id = $id;}
	
	function set_title($title)
	{$this->title = strip_tags($title);}
	
	function set_type($type)
	{$this->type = strip_tags($type);}
	
	function set_author($author)
	{$this->author = strip_tags($author);}
	
	function set_creation_time($creation_time)
	{$this->creation_time = $creation_time;}
	
	function set_content($content)
	{$this->content = $content;}
	
	function set_content_format($content_format)
	{$this->content_format = $content_format;}
	
	function set_categories($categories)
	{$this->categories = $categories;}
	
	function &get_id()
	{return $this->id;}
	
	function &get_title()
	{return $this->title;}
	
	function &get_type()
	{return $this->type;}
	
	function &get_author()
	{return $this->author;}
	
	function &get_creation_time()
	{return $this->creation_time;}
	
	function &get_content()
	{return $this->content;}
	
	function &get_content_format()
	{return $this->content_format;}
	
	function &get_categories()
	{return $this->categories;}
}



/**
* Return an entry complete of last id and creation time.
*/
function xanth_entry_create($entry)
{
	xanth_db_start_transaction();
	$time = time();
	xanth_db_query("INSERT INTO entry (title,type,author,content,content_format,creation_time) VALUES('%s','%s','%s','%s','%s',UNIX_TIMESTAMP(%d))",
		$entry->get_title(),$entry->get_type(),$entry->get_author(),$entry->get_content(),
		$entry->get_content_format(),$time);
	
	$entry->set_creation_time($time);
	$entry->set_id(xanth_db_get_last_id());
	
	$categories = $entry->get_categories();
	if(!empty($categories))
	{
		foreach($categories as $category)
		{
			xanth_db_query("INSERT INTO categorytoentry (entryId,catId) VALUES('%d','%d')",
				$entry->get_id(),$category->get_id());
		}
	}
	
	xanth_db_commit();
	
	return $entry;
}


/**
*
*/
function xanth_entry_delete($entry_id)
{
	xanth_db_query("DELETE FROM entry WHERE id = %d",$entry_id);
}


/**
*
*/
function xanth_entry_update($entry)
{
	xanth_db_start_transaction();
	
	xanth_db_query("UPDATE entry SET title = '%s',type = '%s',author = '%s',content = '%s',content_format = '%s' WHERE id = %d",
		$entry->get_id(),$entry->get_title(),$entry->get_type(),$entry->get_author(),
		$entry->get_content(),$entry->get_content_format());
	
	xanth_db_query("DELETE FROM categorytoentry WHERE entryId = '%d'",$entry->get_id());
	$categories = $entry->get_categories();
	if(!empty($categories))
	{
		foreach($categories as $category)
		{
			xanth_db_query("INSERT INTO categorytoentry (entryId,catId) VALUES('%d','%d')",
				$entry->get_id(),$category->get_id());
		}
	}

	xanth_db_commit();
}


/**
*
*/
function xanth_entry_get($entry_id)
{
	xanth_db_start_transaction();
	
	$entry = NULL;
	$result = xanth_db_query("SELECT * FROM entry WHERE id = %d",$entry_id);
	if($row = xanth_db_fetch_object($result))
	{
		$entry = new xanthEntry($row->id,$row->title,$row->type,$row->author,$row->content,
			$row->content_format,array(),xanth_db_decode_timestamp($row->creation_time));
		
		$result = xanth_db_query("SELECT * FROM categorytoentry WHERE entryId = %d",$entry_id);
		$categories = array();
		while($row = xanth_db_fetch_object($result))
		{
			$categories[] = new xanthCategory($row->id,$row->title,$row->parentId);
		}
		$entry->set_categories($categories);
	}

	xanth_db_commit();
	
	return $entry;
}

/**
*
*/
function xanth_entry_list()
{
	xanth_db_start_transaction();
	
	$entries = array();
	$result = xanth_db_query("SELECT * FROM entry");
	for($i = 0;$row = xanth_db_fetch_object($result);$i++)
	{
		$entries[$i] = new xanthEntry($row->id,$row->title,$row->type,$row->author,$row->content,
			$row->content_format,array(),xanth_db_decode_timestamp($row->creation_time));
		
		$result = xanth_db_query("SELECT * FROM categorytoentry WHERE entryId = %d",$row->id);
		$categories = array();
		while($row = xanth_db_fetch_object($result))
		{
			$categories[] = new xanthCategory($row->id,$row->title,$row->parentId);
		}
		$entry[$i]->set_categories($categories);
	}

	xanth_db_commit();
	
	return $entries;
}



?>