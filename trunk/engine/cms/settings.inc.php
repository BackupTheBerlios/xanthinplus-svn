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

$g_xanth_settings = array();

/**
 * Permits fast access to cms settings
 */
class xSettings
{
	function xStettings()
	{
		assert(false);
	}
	
	/**
	 * 
	 * @static
	 */
	function insertNew($name,$value)
	{
		xDB::getDB()->query("INSERT INTO settings (name,value) VALUES ('%s','%s')",$name,$value);
	}
	
	
	/**
	 * 
	 * @static
	 */
	function save()
	{
		global $g_xanth_settings;
		
		//dynamically construct the query
		$val_array = array();
		
		foreach($g_xanth_settings as $sett_name => $sett_value) 
		{
			xDB::getDB()->query("UPDATE settings SET value = '%s' WHERE name = '%s'",$sett_value,$sett_name);
		}
	}
	
	/**
	 * 
	 * @static
	 */
	function load()
	{
		global $g_xanth_settings;
		
		$result = xDB::getDB()->query("SELECT * FROM settings");
		while($row = xDB::getDB()->fetchObject($result))
		{
			$g_xanth_settings[$row->name] = $row->value;
		}
	}
	
	/**
	 *
	 * @static
	 */
	function get($name)
	{
		global $g_xanth_settings;
		return $g_xanth_settings[$name];
	}
	
	
	/**
	 *
	 * @static
	 *
	 */
	function set($name,$value)
	{
		global $g_xanth_settings;
		$g_xanth_settings[$name] = $value;
	}
	
	
	/**
	 * @return array(name(string) => value(string))
	 * @static
	 */
	function listAll()
	{
		global $g_xanth_settings;
		return $g_xanth_settings;
	}
}

?>