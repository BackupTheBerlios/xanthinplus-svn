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
* This event is emitted on page creation, you can catch it when implementing your template, and printing your desired page template.\n
* Must return the templated page.
* @param $arguments[0] : A mapped array containing the content of every area.
* @param $arguments[1] : The title of the page
* @param $arguments[2] : page metadata as mapped array
*/
define('MONO_HOOK_PAGE_TEMPLATE','mono_hook_page_template');


/**
* @ingroup Hooks
* This event is emitted on a content entry creation, you can catch it when implementing your template, and printing you desired content entry structure. \n
* Must return the templated entry.
* Additional arguments are:\n
* arguments[0]: the entry object.
*/
define('MONO_HOOK_ENTRY_TEMPLATE','mono_hook_entry_template');

/**
* @ingroup Hooks
* An event for asking the structure that a box must have.\n
* Must return the templated box.
* Additional arguments are:\n
* 1) $title: the box title
* 2) $body: the box content
*/
define('MONO_HOOK_BOX_TEMPLATE','mono_hook_box_template');

/**
* @ingroup Hooks
* An event for asking the structure that an area must have.This is a special event , you must append to it the name of the area.
* You can refer to a specific area by using a secondary hook id.
* Must return the templated box.
* Additional arguments are:\n
* 1) $boxes: an array containing all boxes assigned to the area.
* 2) $content: The main content related to a page.
* 3) $elements: a mapped array containing a set of generic elements that you can include in your page (eg. counters,navigation links)
*/
define('MONO_HOOK_AREA_TEMPLATE','mono_hook_area_template');


/**
* @ingroup Hooks
* An event for asking the structure that an area must have.This is a special event , you must append to it the name of the area.\n
* Returns an array containing the areas.
*/
define('MONO_HOOK_TEMPLATE_AREAS_LIST','mono_hook_template_areas_list');



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