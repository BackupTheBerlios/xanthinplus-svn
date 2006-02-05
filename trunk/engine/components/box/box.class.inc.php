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



class xBox
{
	var $name;
	var $title;
	var $user_defined;
	var $content;
	var $content_format;
	
	function xBox($name,$title,$content,$content_format,$user_defined)
	{
		$this->name = $name;
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
		xanth_db_query("INSERT INTO box(name,title,content,content_format,is_user_defined) VALUES('%s','%s','%s','%s',%d)",
			$this->name,$this->title,$this->content,$this->content_format,$this->user_defined);
	}
	
	
	/**
	* Update an existing box.
	*/
	function update()
	{
		xanth_db_query("UPDATE box SET content = '%s',content_format = '%s',title = '%s' WHERE name = '%s'",
			$this->content,$this->content_format,$this->title,$this->name);
	}
	
	
	/**
	* Delete an existing box.
	*/
	function delete()
	{
		xanth_db_query("DELETE FROM box WHERE name = '%s'",$this->name);
	}
	
	
	function assign_to_area($area_name)
	{
		//delete previous assignments
		xanth_db_query("DELETE FROM boxtoarea WHERE boxName = '%s'",$this->name);
		
		//new assignation
		xanth_db_query("INSERT INTO boxtoarea(boxName,area) VALUES ('%s','%s')",$this->name,$area_name);
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
			$result = xanth_db_query("SELECT * FROM box,boxtoarea WHERE box.name = boxtoarea.boxName AND boxtoarea.area = '%s'",$area);
		}
		
		while($row = xanth_db_fetch_array($result))
		{
			$current_box = new xBox($row['name'],$row['title'],$row['content'],$row['content_format'],$row['is_user_defined']);
			if(!($current_box->user_defined))
			{
				//retrieve built-in box content
				$current_box->content = xanth_invoke_mono_hook(MONO_HOOK_CREATE_BOX_CONTENT,$current_box->name);
			}
			else
			{
				$content_format = new xContentFormat($row['content_format'],'');
				$current_box->content = $content_format->apply_to($current_box->content);
			}
			$boxes[] = $current_box;
		}
		return $boxes;
	}
	
};











?>