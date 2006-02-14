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
	var $area;
	
	function xBox($name,$title,$content,$content_format,$user_defined,$area = NULL)
	{
		$this->name = $name;
		$this->title = $title;
		$this->content_format = $content_format;
		$this->user_defined = $user_defined;
		$this->content = $content;
		$this->area = $area;
	}
	
	/**
	*
	*/
	function insert()
	{
		$field_names = "name,title,is_user_defined,content,content_format";
		$field_values = "'%s','%s',%d,'%s','%s'";
		$values = array($this->name,$this->title,$this->user_defined,$this->content,$this->content_format);
		
		if(!empty($this->area))
		{
			$field_names .= ',area';
			$field_values .= ",'%s'";
			$values[] = $this->area;
		}
		
		xanth_db_query("INSERT INTO box($field_names) VALUES($field_values)",$values);
	}
	
	
	/**
	* Update an existing box.
	*/
	function update()
	{
		$fields = "content_format = '%s',title = '%s',content = '%s'";
		$values = array($this->content_format,$this->title,$this->content);
		
		if(!empty($this->area))
		{
			$fields .= ",area = '%s'";
			$values[] = $this->area;
		}
		
		$values[] = $this->name;
		xanth_db_query("UPDATE box SET $fields WHERE name = '%s'",$values);
	}
	
	
	/**
	* Delete an existing box.
	*/
	function delete()
	{
		xanth_db_query("DELETE FROM box WHERE name = '%s'",$this->name);
	}
	
	/**
	*
	*/
	function find_all()
	{
		return xBox::find();
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
			$result = xanth_db_query("SELECT * FROM box WHERE area = '%s'",$area);
		}
		
		while($row = xanth_db_fetch_array($result))
		{
			$current_box = new xBox($row['name'],$row['title'],$row['content'],$row['content_format'],$row['is_user_defined'],$row['area']);
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