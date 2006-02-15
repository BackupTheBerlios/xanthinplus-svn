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
*
*/
class xThemeArea
{
	var $name;
	var $view_mode;
	
	function xThemeArea($name,$view_mode = NULL)
	{
		$this->name = $name;
		$this->view_mode = $view_mode;
	}
	
	/**
	*
	*/
	function insert()
	{
		$field_names = "name";
		$field_values = "'%s'";
		$values = array($this->name);
		
		if(!empty($this->view_mode))
		{
			$field_names .= ',view_mode';
			$field_values .= ",'%d'";
			$values[] = $this->view_mode;
		}
		xanth_db_query("INSERT INTO theme_area($field_names) VALUES($field_values)",$values);
	}
	
	/**
	*
	*/
	
	function delete()
	{
		xanth_db_query("DELETE FROM theme_area WHERE name = '%s'",$this->name);
	}
	
	/**
	*
	*/
	function find_all()
	{
		$elems = array();
		$result = xanth_db_query("SELECT * FROM theme_area");
		while($row = xanth_db_fetch_object($result))
		{
			$elems[] = new xThemeArea($row->name,$row->view_mode);
		}
		return $elems;
	}
	
	/**
	*
	*/
	function get($area_name)
	{
		$elems = array();
		$result = xanth_db_query("SELECT * FROM theme_area WHERE name = '%s'",$area_name);
		if($row = xanth_db_fetch_object($result))
		{
			return new xThemeArea($row->name,$row->view_mode);
		}
		return NULL;
	}
	
	
	/**
	*
	*/
	function render($boxes,$page_content)
	{
		if($this->view_mode === NULL)
		{
			//apply theme default
			$theme = xTheme::get_default();
			return eval($theme->get_view_mode_procedure('area'));
		}
		else
		{
			//apply specified view mode
			$view_mode = xViewMode::get($this->view_mode);
			return eval($view_mode->display_procedure);
		}
	}
}

?>