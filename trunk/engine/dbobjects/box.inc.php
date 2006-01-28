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
* \ingroup Events
* This is a special event, you should append to this event identifier the name of the box you would like to handle content creation.\n
* Additional arguments:
* 1) $&content: a reference to a string to fill with the  box content.
*/
define('EVT_CORE_CREATE_BOX_CONTENT_','evt_core_create_box_content_');


class xanthBox
{
	var $id;
	var $title;
	var $user_defined;
	var $content;
	var $content_format;
	
	function xanthBox($id,$title,$content,$content_format,$user_defined)
	{
		$this->set_id($id);
		$this->set_title($title);
		$this->set_content_format($content_format);
		$this->set_user_defined($user_defined);
		$this->set_content($content);
	}
	
	function set_id($id)
	{$this->id = $id;}
	
	function set_title($title)
	{$this->title = strip_tags($title);}
	
	function set_content_format($content_format)
	{$this->content_format = $content_format;}
	
	function set_content($content)
	{$this->content = $content;}
	
	function set_user_defined($user_defined)
	{$this->user_defined = $user_defined;}
	
	function get_id()
	{return $this->id;}
	
	function get_title()
	{return $this->title;}
	
	function get_content_format()
	{return $this->content_format;}
	
	function get_content()
	{return $this->content;}
	
	function get_user_defined()
	{return $this->user_defined;}
};

/**
* Create a new box and add it in database. Return false if a box with that name already exists.
*/
function xanth_box_create($xanth_box)
{
	xanth_db_query("INSERT INTO box(id,title,content,content_format_name,is_user_defined) VALUES(%d,'%s','%s','%s',%d)",
		$xanth_box->get_id(),$xanth_box->get_title(),$xanth_box->get_content(),
		$xanth_box->get_content_format(),$xanth_box->get_user_defined());
}

/**
* Update an existing box.
*/
function xanth_box_update($xanth_box)
{
	xanth_db_query("UPDATE box SET content = '%s',content_format_name = '%s',title = '%s' WHERE id = '%s'",
	$xanth_box->get_content(),$xanth_box->get_content_format(),$xanth_box->get_title(),$xanth_box->get_id());
}


/**
* Delete an existing box.
*/
function xanth_box_delete($box_id)
{
	xanth_db_query("DELETE FROM box WHERE id = '%s'",$xanth_box->get_id());
}


/**
* List all box in an area.
*/
function xanth_box_list($area = '')
{
	$boxes = array();
	if(empty($area))
	{
		$result = xanth_db_query("SELECT * FROM box");
	}
	else
	{
		$result = xanth_db_query("SELECT * FROM box,boxtoarea WHERE box.boxName = boxtoarea.boxName AND boxtoarea.area = '%s'",$area);
	}
	
	while($row = xanth_db_fetch_array($result))
	{
		$current_box = new xanthBox($row['id'],$row['title'],$row['content'],$row['content_format_name'],$row['is_user_defined']);
		if($current_box->get_user_defined())
		{
			//retrieve built-in box content
			$content = '';
			xanth_broadcast_event(EVT_CORE_CREATE_BOX_CONTENT_ . $current_box->get_id(),'core',array(&$content));
			$current_box->set_content($content);
		}
		else
		{
			$current_box->set_content(xanth_apply_content_format($current_box->get_content(),$row['content_format']));
		}
		$boxes[] = $current_box;
	}
	return $boxes;
}


?>