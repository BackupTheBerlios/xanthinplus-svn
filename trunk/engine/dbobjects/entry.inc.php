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
	
	function xanthEntry($id,$title,$type,$author,$content,$content_format,,$categories,$creation_time)
	{
		$this->id = $id;
		$this->title = strip_tags($title);
		$this->type = $type;
		$this->author = strip_tags($author);
		$this->creation_time = $creation_time;
		$this->content = $content;
		$this->content_format = $content_format;
		$this->categories = $categories;
	}
}


/**
* Return an entry complete of last id and creation time.
*/
function xanth_entry_create($entry)
{
	xanth_db_start_transaction();
	$time = time();
	xanth_db_query("INSERT INTO entry (title,type,author,content,content_format,creation_time) VALUES('%s','%s','%s','%s','%s',UNIX_TIMESTAMP(%d))",
		$entry->title,$entry->type,$entry->author,$entry->content,$entry->content_format,$time);
	
	$entry->time = $time;
	$entry->id = xanth_db_get_last_id();
	
	if(!empty($entry->categories))
	{
		foreach($entry->categories as $category)
		{
			xanth_db_query("INSERT INTO categorytoentry (entryId,catId) VALUES('%d','%d')",$entry->id,$category->id);
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
	
	xanth_db_query("UPDATE entry SET title = '%s',type = '%s',author = '%s',content = '%s',content_format = '%s' WHERE id = %d",$entry->id
		$entry->title,$entry->type,$entry->author,$entry->content,$entry->content_format);
	
	xanth_db_query("DELETE FROM categorytoentry WHERE entryId = '%d'",$entry->id);
	if(!empty($entry->categories))
	{
		foreach($entry->categories as $category)
		{
			xanth_db_query("INSERT INTO categorytoentry (entryId,catId) VALUES('%d','%d')",$entry->id,$category->id);
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
	$result = xanth_db_query("SELECT * FROM entry WHERE id = %d",$entry->id);
	if($row = xanth_db_fetch_object($result))
	{
		$entry = new xanthEntry($row->id,$row->title,$row->type,$row->author,$row->content,
			$row->content_format,array(),xanth_db_decode_timestamp($row->creation_time));
		
		$result = xanth_db_query("SELECT * FROM categorytoentry WHERE entryId = %d",$entry_id);
		while($row = xanth_db_fetch_object($result))
		{
			$entry->categories[] = new xanthCategory($row->id,$row->title,$row->parentId);
		}
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
		while($row = xanth_db_fetch_object($result))
		{
			$entry[$i]->categories[] = new xanthCategory($row->id,$row->title,$row->parentId);
		}
	}

	xanth_db_commit();
	
	return $entries;
}



?>