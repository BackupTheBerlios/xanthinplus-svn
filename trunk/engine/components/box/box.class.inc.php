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
* @ingroup Hooks
* You can use a secondary hook id to refer to a specific box.
* Must return the content of the box.
*/
define('MONO_HOOK_CREATE_BOX_CONTENT','mono_hook_create_box_content');


class xBox
{
	var $id;
	var $title;
	var $user_defined;
	var $content;
	var $content_format;
	
	function xBox($id,$title,$content,$content_format,$user_defined)
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
			$result = xanth_db_query("SELECT * FROM box,boxtoarea WHERE box.id = boxtoarea.boxId AND boxtoarea.area = '%s'",$area);
		}
		
		while($row = xanth_db_fetch_array($result))
		{
			$current_box = new xBox($row['id'],$row['title'],$row['content'],$row['content_format_name'],$row['is_user_defined']);
			if($current_box->get_user_defined())
			{
				//retrieve built-in box content
				$content = xanth_invoke_mono_hook(MONO_HOOK_CREATE_BOX_CONTENT,$current_box->id);
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