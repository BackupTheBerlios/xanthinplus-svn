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
 * Settings
 */
class xSettingsDAO
{
	function xSettingsDAO()
	{
		assert(FALSE);
	}
	
	/**
	 * Insert a new setting item
	 *
	 * @param array(name,value)  $settings
	 * @return bool FALSE on error
	 * @static 
	 */
	function insert($name,$value)
	{
		$db =& xDB::getDB();
		return $db->query("INSERT INTO settings (name,value) VALUES ('%s','%s')",$name,$value);
	}
	
	
	/**
	 * Save all passed settings
	 *
	 * @static
	 */
	function save($settings)
	{
		$db =& xDB::getDB();
		foreach($settings as $sett_name => $sett_value) 
		{
			$db->query("UPDATE settings SET value = '%s' WHERE name = '%s'",$sett_value,$sett_name);
		}
	}
	
	
	/**
	 *
	 */
	function load()
	{
		$db =& xDB::getDB();
		$settings = array();
		$result = $db->query("SELECT * FROM settings");
		while($row = $db->fetchObject($result))
		{
			$settings[$row->name] = $row->value;
		}
		
		return $settings;
	}
};











?>