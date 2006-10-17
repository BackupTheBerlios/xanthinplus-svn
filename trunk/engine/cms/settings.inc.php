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
	function dbInsert($name,$value)
	{
		xSettingsDAO::insert($name,$value);
	}
	
	
	/**
	 * Save all current settings
	 *
	 * @return void 
	 * @static
	 */
	function dbSave()
	{
		global $g_xanth_settings;
		xSettingsDAO::save($g_xanth_settings);
	}
	
	/**
	 * Load all setting from db.
	 *
	 * @return void
	 * @static
	 */
	function dbLoad()
	{
		global $g_xanth_settings;
		
		$g_xanth_settings = xSettingsDAO::load();
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