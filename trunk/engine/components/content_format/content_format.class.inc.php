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
* In this hook you must implement a method to apply a content format to a content. As secondary hook id you specify the
* name of the content format you would manage.
* @param arg[0] The content to witch apply the content format.
* @return The eleborated content or FALSE on failure.
*/
define('MONO_HOOK_CONTENT_FORMAT_APPLY','mono_hook_content_format_apply');

/**
*
*/
class xContentFormat
{
	var $name;
	var $description;
	var $last_error = '';
	
	function xContentFormat($name,$description)
	{
		$this->name = $name;
		$this->description = $description;
	}
	
	function insert()
	{
		xanth_db_query("INSERT INTO content_format(name,description) VALUES ('%s','%s')",
			$this->name,$this->description);
	}
	
	function delete()
	{
		xanth_db_query("DELETE FROM content_format WHERE name = '%s'",$this->name);
	}
	
	function update()
	{
		xanth_db_query("UPDATE content_format SET description = '%s' WHERE name = '%s'",
			$this->description,$this->name);
	}
	
	function find_all()
	{
		$formats = array();
		$result = xanth_db_query("SELECT * FROM content_format");
		while($row = xanth_db_fetch_object($result))
		{
			$formats[] = new xContentFormat($row->name,$row->description);
		}
		
		return $formats;
	}
	
	/**
	* Return a new xContentFormat object or NULL
	*/
	function load($name)
	{
		$result = xanth_db_query("SELECT * FROM content_format WHERE name = '%s'",$name);
		if($row = xanth_db_fetch_object($result))
		{
			$format = new xContentFormat($row->name,$row->description);
			return $format;
		}
		
		return NULL;
	}
	
	/**
	*
	*/
	function get_last_error()
	{
		return $this->last_error;
	}
	
	/**
	* @return The eleborated content or FALSE on failure. Check if the last error is empty to see if effectively there was an error.
	*/
	function apply_to($content)
	{
		$this->last_error = '';
		$error = '';
		$result = xanth_invoke_mono_hook('MONO_HOOK_CONTENT_FORMAT_APPLY',$this->name,array($content,&$error));
		$this->last_error = $error;
		return $result;
	}
}




?>