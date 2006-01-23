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
 * Returns an array of mapped array representing all existing themes \n
 * $ret[0] = array(name,path)
 */
function xanth_list_existing_themes()
{
	$themes = array();
	
	//read builtin directory
	$dir = xanth_get_working_dir() . '/themes/';
	if($dh = opendir($dir))
	{
        $themes = array_merge($themes,xanth_list_dirs($dh));
        closedir($dh);
    }
	else
	{
		xanth_log(LOG_LEVEL_FATAL_ERROR,"Theme directory directory $dir not found","Core",__FILE__,__LINE__);
	}
	
	return $themes;
}

/**
*
*/
function xanth_theme_exists($name,$path)
{
	return is_dir($path . $name);
}


/**
*
*/
function xanth_set_default_theme($path,$name)
{
	if(xanth_theme_exists($name,$path))
	{
		xanth_db_query("UPDATE themes SET is_default = 0");
		$result = xanth_db_query("SELECT is_default FROM themes WHERE name = '%s'",$name);
		if($row = xanth_db_fetch_array($result))
		{
			if(!$row['is_default'])
				xanth_db_query("UPDATE themes SET is_default = 1 WHERE name = '%s'",$name);
		}
		else
		{
			xanth_db_query("INSERT INTO themes(name,path,is_default) VALUES('%s','%s',%d)",$name,$path,1);
		}
		
		return true;
	}
	return false;
}

/**
 * Returns the current default theme as a mapped array of type: \n
 * $ret = array(path,name)
 */
function xanth_get_default_theme()
{
	$enabled_theme = NULL;
	foreach(xanth_list_existing_modules() as $theme)
	{
		$result = xanth_db_query("SELECT is_default FROM themes");
		if($row = xanth_db_fetch_array($result))
		{
			if($row['is_default'] !== 0)
			{
				$enabled_theme = array($row['path'],$row['name']);
			}
		}
	}
	return $enabled_theme;
}


/**
* 
*/
function xanth_include_theme($theme)
{
	
}


?>