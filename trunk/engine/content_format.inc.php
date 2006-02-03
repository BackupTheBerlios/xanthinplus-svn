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

class xContentFormat
{
	var $name;
	var $stripped_html;
	var $php_source;
	var $new_line_to_line_break;
	var $bbcode;
	
	function xContentFormat($name,$stripped_html,$php_source,$new_line_to_line_break)
	{
		$this->name = $name;
		$this->stripped_html = $stripped_html;
		$this->php_source = $php_source;
		$this->new_line_to_line_break = $new_line_to_line_break;
	}
	
	function insert()
	{
		xanth_db_query("INSERT INTO content_format(name,stripped_html,php_source,new_line_to_line_break) VALUES ('%s',%d,%d,%d)",
			$this->name,$this->stripped_html,$this->php_source,$this->new_line_to_line_break);
	}
	
	function delete()
	{
		xanth_db_query("DELETE FROM content_format WHERE name = '%s'",$this->name);
	}
	
	function update()
	{
		xanth_db_query("UPDATE content_format SET stripped_html = %d,php_source = %d,new_line_to_line_break =%d WHERE name = '%s'",
			$this->stripped_html,$this->php_source,$this->new_line_to_line_break,$this->name);
	}
	
	function find_all()
	{
		$formats = array();
		$result = xanth_db_query("SELECT * FROM content_format");
		while($row = xanth_db_fetch_object($result))
		{
			$formats[] = new xContentFormat($row->name,$row->stripped_html,$row->php_source,$row->new_line_to_line_break);
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
			$format = new xContentFormat($row->name,$row->stripped_html,$row->php_source,$row->new_line_to_line_break);
			return $format;
		}
		
		return NULL;
	}
	
	/**
	*
	*/
	function apply_to($content)
	{
		if($this->php_source)
		{
			ob_start();
			eval($content);
			return ob_get_clean();
		}
		elseif($this->stripped_html)
		{
			$cont = strip_tags($content,'<strong>','<ul>','<li>','<br>');
			
			if($this->new_line_to_line_break)
				$cont = nl2br($cont);
			
			return $cont;
		}
		else //full html
		{
			if($this->new_line_to_line_break)
				$cont = nl2br($content);
			
			return $cont;
		}
	}
}




?>