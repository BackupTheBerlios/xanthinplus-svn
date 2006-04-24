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
class xTheme
{
	var $name;
	
	/** Mapped array element->view*/
	var $themed_elements;
	
	function xTheme($name,$themed_elements)
	{
		$this->name = $name;
		$this->themed_elements = $themed_elements;
	}
	
	/**
	*
	*/
	function insert()
	{
		xanth_db_start_transaction();
		
		xanth_db_query("INSERT INTO theme(name) VALUES ('%s')",$this->name);
		
		foreach($this->themed_elements as $element => $view)
		{
			xanth_db_query("INSERT INTO theme_to_elements(theme_name,visual_element,view_mode) VALUES ('%s','%s','%s')",
				$this->name,$element,$view);
		}
		
		xanth_db_commit();
	}
	
	function update()
	{
		xanth_db_start_transaction();
		
		//delete old visual elements
		xanth_db_query("DELETE FROM theme_to_elements WHERE theme_name = '%s'",$this->name);
		
		//now reinsert
		foreach($this->themed_elements as $element => $view)
		{
			xanth_db_query("INSERT INTO theme_to_elements(theme_name,visual_element,view_mode) VALUES ('%s','%s','%s')",
				$this->name,$element,$view);
		}
		
		xanth_db_commit();
	}
	
	/**
	*
	*/
	function delete()
	{
		xanth_db_query("DELETE FROM theme WHERE name = '%s'",$this->name);
	}
	
	
	function get($name)
	{
		$themed_elements = array();
		$result = xanth_db_query("SELECT * FROM theme_to_elements WHERE theme_name = '%s'",$name);
		while($row = xanth_db_fetch_object($result))
		{
			$themed_elements[$row->visual_element] = $row->view_mode;
		}
		return new xTheme($name,$themed_elements);
	}
	
	
	/**
	*
	*/
	function find_all()
	{
		$themes = array();
		$result = xanth_db_query("SELECT * FROM theme");
		while($row = xanth_db_fetch_object($result))
		{
			$themed_elements = array();
			$result2 = xanth_db_query("SELECT * FROM theme_to_elements WHERE theme_name = '%s'",$row->name);
			while($row2 = xanth_db_fetch_object($result2))
			{
				$themed_elements[$row2->visual_element] = $row2->view_mode;
			}
			
			$themes[] = new xTheme($row->name,$themed_elements);
		}
		return $themes;
	}
	
	/**
	*
	*/
	function get_view_mode_procedure($element)
	{
		if(isset($this->themed_elements[$element]))
		{
			$result = xanth_db_query("SELECT * FROM view_mode WHERE id = %d",$this->themed_elements[$element]);
			if($row = xanth_db_fetch_object($result))
			{
				return $row->display_procedure;
			}
			
			return NULL;
		}
		else
		{
			//return the default view mode for element
			$result = xanth_db_query("SELECT * FROM view_mode WHERE relative_visual_element = '%s' AND default_for_element = %d",
				$element,TRUE);
				
			if($row = xanth_db_fetch_object($result))
			{
				return $row->display_procedure;
			}
			
			xanth_log(LOG_LEVEL_FATAL_ERROR,'Default view mode for visual element '.$element. ' not found',__CLASS__.'::'.__FUNCTION__);
			return NULL;
		}
	}
	
	/**
	*
	*/
	function get_default()
	{
		global $default_theme;
		if(!isset($default_theme))
		{
			$default_theme = xTheme::get(xSettings::get('site_theme'));
		}
		return $default_theme;
	}
}

?>