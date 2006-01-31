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
* @ingroup Events
* This event is emitted on page creation, you can catch it when implementing your template, and printing you desired page structure.\n
* Additional arguments are:\n
* 1) $areas : A mapped array containing the content of every area.
* 2) $elements: a mapped array containing a set of generic elements that you can include in your page (eg. counters,page title,metatags,navigation links)
*/
define('EVT_THEME_PAGE_TEMPLATE','evt_theme_page_template');


/**
* @ingroup Events
* This event is emitted on a content entry creation, you can catch it when implementing your template, and printing you desired content entry structure. \n
* Additional arguments are:\n
* arguments[0]: the entry object.
*/
define('EVT_THEME_ENTRY_TEMPLATE','evt_theme_content_entry_template');

/**
* @ingroup Events
* An event for asking the structure that a box must have.\n
* Additional arguments are:\n
* 1) $title: the box title
* 2) $body: the box content
*/
define('EVT_THEME_BOX_TEMPLATE','evt_theme_box_template');

/**
* @ingroup Events
* An event for asking the structure that an area must have.This is a special event , you must append to it the name of the area.\n
* Additional arguments are:\n
* 1) $boxes: an array containing all boxes assigned to the area.
* 2) $content: The main content related to a page.
* 3) $elements: a mapped array containing a set of generic elements that you can include in your page (eg. counters,navigation links)
*/
define('EVT_THEME_AREA_TEMPLATE_','evt_theme_area_template_');


/**
* @ingroup Events
* An event for asking the structure that an area must have.This is a special event , you must append to it the name of the area.\n
* Additional arguments are:\n
* 1) &$arealist: a reference to an array to fill with area names.
*/
define('EVT_THEME_AREA_LIST','evt_theme_area_list');



class xTheme
{
	var $path;
	var $name;
	
	function xTheme($path,$name)
	{
		$this->path = $path;
		$this->name = $name;
	}

	/**
	*
	*/
	function exists()
	{
		return is_dir($this->path . $this->name);
	}


	/**
	*
	*/
	function set_default()
	{
		if($this->exists())
		{
			xanth_db_query("UPDATE themes SET is_default = 0");
			$result = xanth_db_query("SELECT is_default FROM themes WHERE name = '%s'",$this->name);
			if($row = xanth_db_fetch_array($result))
			{
				if(!$row['is_default'])
					xanth_db_query("UPDATE themes SET is_default = 1 WHERE name = '%s'",$this->name);
			}
			else
			{
				xanth_db_query("INSERT INTO themes(name,path,is_default) VALUES('%s','%s',%d)",$this->name,$this->path,1);
			}
			
			return true;
		}
		return false;
	}
	
	/**
	 * Returns an array of objects xTheme representing all existing themes \n
	 */
	function find_existing()
	{
		$themes = array();
		
		//read builtin directory
		$dir = './themes/';
		$dirs_data = xanth_list_dirs($dir);
		if(is_array($dirs_data))
		{
			foreach($dirs_data as $dir_data)
			{
				$themes[] = new xTheme($dir_data['path'],$dir_data['name']);
			}
		}
		else
		{
			xanth_log(LOG_LEVEL_FATAL_ERROR,"Theme directory directory $dir not found","Core",__FUNCTION__);
		}
		
		return $themes;
	}
	
	/**
	  * Returns the current default theme.
	 */
	function find_default()
	{
		$enabled_theme = NULL;
		foreach(xTheme::find_existing() as $theme)
		{
			$result = xanth_db_query("SELECT * FROM themes WHERE is_default = 1");
			if($row = xanth_db_fetch_array($result))
			{
				if($row['is_default'] !== 0)
				{
					return new xTheme($row['path'],$row['name']);
				}
			}
		}
	}

	/**
	* 
	*/
	function init()
	{
		if($this->exists())
		{
			include_once($this->path . $this->name . "/" . $this->name . ".theme.php");
			xanth_theme_init_default();
		}
	}
};




?>