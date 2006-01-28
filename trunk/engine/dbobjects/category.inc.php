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

class xanthCategory
{
	var $id;
	var $title;
	var $parent_id;
	
	function xanthCategory($id,$title,$parent_id = NULL)
	{
		$this->set_id($id);
		$this->set_title($title);
		$this->set_parent_id($parent_id);
	}
	
	function set_id($id)
	{$this->id = $id;}
	
	function set_title($title)
	{$this->title = strip_tags($title);}
	
	function set_parent_id($parent_id)
	{$this->parent_id = $parent_id;}
	
	function get_id()
	{return $this->id;}
	
	function get_title()
	{return $this->title;}
	
	function get_parent_id()
	{return $this->parent_id;}
}

/**
*
*/
function xanth_category_create($category)
{
	if($category->get_parent_id() == NULL)
	{
		xanth_db_query("INSERT INTO category (title) VALUES ('%s')",$category->get_title());
	}
	else
	{
		xanth_db_query("INSERT INTO category (title,parentId) VALUES ('%s','%d')",
			$category->get_title(),$category->get_parent_id());
	}
}

/**
*
*/
function xanth_category_delete($category_id)
{
	xanth_db_query("DELETE FROM category WHERE id = '%d'",$category_id);
}

/**
*
*/
function xanth_category_list_root()
{
	$result = xanth_db_query("SELECT * FROM category WHERE parentId = NULL");
	$categories = array();
	while($row = xanth_db_fetch_object($result))
	{
		$categories[] = new xanthCategory($row->id,$row->title);
	}
	
	return $categories;
}

/**
*
*/
function xanth_category_list_childs($parentId)
{
	$result = xanth_db_query("SELECT * FROM category WHERE parentId = '%d'",$parentId);
	$categories = array();
	while($row = xanth_db_fetch_object($result))
	{
		$categories[] = new xanthCategory($row->id,$row->title,$row->parentId);
	}
	
	return $categories;
}


/**
*
*/
function xanth_category_list()
{
	$result = xanth_db_query("SELECT * FROM category");
	$categories = array();
	while($row = xanth_db_fetch_object($result))
	{
		$categories[] = new xanthCategory($row->id,$row->title,$row->parentId);
	}
	
	return $categories;
}


?>