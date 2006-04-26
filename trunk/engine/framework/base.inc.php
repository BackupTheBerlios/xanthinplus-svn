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
	 * @param $name (string) The name of the variable to return.
	 * @param $default (mixed) The default value to use if this variable has never been set.
	 * @return  (mixed) The value of the variable.
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
	 * @param $name (string)The name of the variable to set.
	 * @param $value  (mixed) The value to set.
	 * @static
	 */
	function set($name, $value)
	{
		global $g_xanth_conf;
		$g_xanth_conf[$name] = $value;
	}
	
};


/**
*
*/
function xanth_instanceof($object,$class_name)
{
	if(version_compare(PHP_VERSION,"5.0.0") >= 0)
	{
		return is_a($object,$class_name);
	}
	else
	{
		return $object instanceof $class_name;
	}
}


/**
*
*/
function xanth_get_working_dir()
{
	return $_SERVER['DOCUMENT_ROOT'] . xanth_conf_get('db_doc_path','');
}


/**
 * Returns an array of mapped array representing all existing dir in a specified path \n
 * $ret[0] = array(name,path)
 */
function xanth_list_dirs($path)
{
	$dirs = array();
	$path = $path . '/';
	$path = str_replace("//", "/", $path);
	
	$dh = opendir($path);
	if(!$dh)
		return NULL;
		
	//read builtin directory
	while(($file = readdir($dh)) !== false) 
	{
		if(is_dir($path . $file) && $file{0} !== '.')
		{
			$dirs[] = array('name' => $file,'path' => $path . $file . '/');
		}
	}
	closedir($dh);

	return $dirs;
}

/**
 * Returns an array of mapped array representing all existing files (not dirs) in a specified path \n
 * $ret[0] = array(name,path)
 */
function xanth_list_files($path)
{
	$files = array();
	
	$dh = opendir($path);
	if(!$dh)
		return NULL;
		
	//read builtin directory
	while(($file = readdir($dh)) !== false) 
	{
		if(is_file($path . $file) && $file{0} !== '.')
		{
			$files[] = array('name' => $file,'path' => $path . $file);
		}
	}
	closedir($dh);

	return $files;
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

?>