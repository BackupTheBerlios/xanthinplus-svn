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
		$this->id = $id;
		$this->title = $title;
		$this->content_format = $content_format;
		$this->user_defined = $user_defined;
		$this->content = $content;
	}
	
	/**
	*
	*/
	function insert()
	{
		xanth_db_query("INSERT INTO box(id,title,content,content_format_name,is_user_defined) VALUES(%d,'%s','%s','%s',%d)",
			$this->id,$this->title,$this->content,$this->content_format,$this->user_defined);
	}
	
	
	/**
	* Update an existing box.
	*/
	function update()
	{
		xanth_db_query("UPDATE box SET content = '%s',content_format_name = '%s',title = '%s' WHERE id = '%s'",
			$this->content,$this->content_format,$this->title,$this->id);
	}
	
	
	/**
	* Delete an existing box.
	*/
	function delete()
	{
		xanth_db_query("DELETE FROM box WHERE id = '%s'",$this->id);
	}
	
	/**
	* List all box in an area.
	*/
	function find($area = '')
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
				xanth_broadcast_event(EVT_CORE_CREATE_BOX_CONTENT_ . $current_box->id,'core',array(&$content));
				$current_box->content = $content;
			}
			else
			{
				$current_box->content = xanth_apply_content_format($current_box->content,$row['content_format']);
			}
			$boxes[] = $current_box;
		}
		return $boxes;
	}
	
};











?>