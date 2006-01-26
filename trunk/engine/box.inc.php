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
	var $name;
	var $title;
	var $user_defined;
	var $content;
	var $content_format;
	
	function xanthBox($name,$title,$content,$content_format,$user_defined)
	{
		$this->name = $name;
		$this->title = $title;
		$this->content_format = $content_format;
		$this->user_defined = $user_defined;
		$this->content = $content;
	}
};

/**
*
*/
function xanth_box_exists($box_name)
{
	$result = xanth_db_query("SELECT FROM box WHERE boxName = '%s'",$box_name);
	if(xanth_db_fetch_array($result))
	{
		return true;
	}
	else
	{
		return false;
	}
}

/**
* Create a new box and add it in database. Return false if a box with that name already exists.
*/
function xanth_box_create($xanth_box)
{
	if(!xanth_exists_box($xanth_box->name))
	{
		xanth_db_query("INSERT INTO box(boxName,title,content,content_format_name,is_user_defined) VALUES('%s','%s','%s','%s',%d)",$xanth_box->name,$xanth_box->title,$xanth_box->content,$xanth_box->content_format,$xanth_box->user_defined);
	}
	else
	{
		return false;
	}
}

/**
* Update an existing box.
*/
function xanth_box_update($xanth_box)
{
	if(xanth_exists_box($xanth_box->name))
	{
		xanth_db_query("UPDATE box SET content = '%s',content_format_name = '%s',title = '%s' WHERE boxName = '%s'",$xanth_box->content,$xanth_box->content_format,$xanth_box->title,$xanth_box->name);
	}
}


/**
* Delete an existing box.
*/
function xanth_box_delete($xanth_box)
{
	xanth_db_query("DELETE FROM box WHERE boxName = '%s'",$xanth_box->name);
	xanth_db_query("DELETE FROM boxtoarea WHERE boxName = '%s'",$xanth_box->name);
}


/**
* List all box in an area.
*/
function xanth_box_list($area = '')
{
	$boxes = array();
	if($area == '')
	{
		$result = xanth_db_query("SELECT * FROM box");
	}
	else
	{
		$result = xanth_db_query("SELECT * FROM box,boxtoarea WHERE box.boxName = boxtoarea.boxName AND boxtoarea.area = '%s'",$area);
	}
	
	while($row = xanth_db_fetch_array($result))
	{
		$current_box = new xanthBox($row['boxName'],$row['title'],$row['content'],$row['content_format_name'],$row['is_user_defined']);
		if($current_box->user_defined)
		{
			//retrieve built-in box content
			xanth_broadcast_event(EVT_CORE_CREATE_BOX_CONTENT_ . $current_box->name,'core',array(&$current_box->content));
		}
		else
		{
			$current_box->content = xanth_apply_content_format($current_box->content,$row['content_format_name']);
		}
		$boxes[] = $current_box;
	}
	return $boxes;
}

?>