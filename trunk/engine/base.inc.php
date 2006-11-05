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
* Permits interaction with globally defined configuration variables.
*/
class xConf
{
	/**
	 * Return a configuration variable.
	 *
	 * @param string $name The name of the variable to return.
	 * @param mixed $default The default value to use if this variable has never been set.
	 * @return mixed The value of the variable.
	 * @static
	 */
	function get($name, $default) 
	{
		global $g_xanth_conf;
		return isset($g_xanth_conf[$name]) ? $g_xanth_conf[$name] : $default;
	}

	/**
	 * Set a  configuration variable.
	 *
	 * @param string $name The name of the variable to set.
	 * @param mixed $value The value to set.
	 * @static
	 */
	function set($name, $value)
	{
		global $g_xanth_conf;
		$g_xanth_conf[$name] = $value;
	}
	
};

$xanth_working_dir = $_SERVER['DOCUMENT_ROOT'] . xConf::get('installation_path','xanthin');


/**
 *
 */
function xanth_instanceof($object,$class_name)
{
	//todo
	return is_a($object,$class_name);
	/**
	if(version_compare(PHP_VERSION,"5.0.0") < 0)
	{
		return is_a($object,$class_name);
	}
	else
	{
		return $object instanceof $class_name;
	}*/
}


/**
*
*/
function xanth_valid_email($email)
{
   if(preg_match("#^([a-z0-9&\-_.]+?@[\w\-]+\.([\w\-\.]+\.)?[\w]+)$#i", $email))
   {
       return TRUE;
   }
   return FALSE;
}


/**
 * @internal
 */
function _xanth_fix_gpc_magic(&$item)
{
	if (is_array($item)) 
    	array_walk($item, '_fix_gpc_magic');
	else 
		$item = stripslashes($item);
}


/**
 * Correct double-escaping problems caused by "magic quotes".
 */
function xanth_fix_gpc_magic()
{
	static $fixed = false;
	if (@get_magic_quotes_gpc() == 1)
	{
		array_walk($_GET, '_xanth_fix_gpc_magic');
		array_walk($_POST, '_xanth_fix_gpc_magic');
		array_walk($_COOKIE, '_xanth_fix_gpc_magic');
		array_walk($_REQUEST, '_xanth_fix_gpc_magic');
		$fixed = true;
	}
}


?>