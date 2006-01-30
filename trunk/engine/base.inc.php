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
 * Return a configuration variable.
 *
 * @param $name  The name of the variable to return.
 * @param $default  The default value to use if this variable has never been set.
 * @return  The value of the variable.
 */
function xanth_conf_get($name, $default) 
{
	global $xanth_conf;
	return isset($xanth_conf[$name]) ? $xanth_conf[$name] : $default;
}

/**
 * Set a  configuration variable.
 *
 * @param $name  The name of the variable to set.
 * @param $value  The value to set.
 */
function xanth_conf_set($name, $value)
{
	global $xanth_conf;
	$xanth_conf[$name] = $value;
}

/**
 * Return a global variable.
 *
 * @param $name  The name of the variable to return.
 * @param $default  The default value to use if this variable has never been set.
 * @return  The value of the variable.
 */
function xanth_global_var_get($name, $default) 
{
	global $xanth_global_var;
	return isset($xanth_global_var[$name]) ? $xanth_global_var[$name] : $default;
}

/**
 * Set a  global variable.
 *
 * @param $name  The name of the variable to set.
 * @param $value  The value to set.
 */
function xanth_global_var_set($name, $value)
{
	global $xanth_global_var;
	$xanth_global_var[$name] = $value;
}

/**
 * Unset a  global variable.
 */
function xanth_global_var_unset($name)
{
	global $xanth_global_var;
	unset($xanth_global_var[$name]);
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
   if(eregi("^[a-zA-Z0-9]+[_a-zA-Z0-9-]*(\.[_a-z0-9-]+)*@[a-z??????0-9]+(-[a-z??????0-9]+)*(\.[a-z??????0-9-]+)*(\.[a-z]{2,4})$", $email))
   {
       return TRUE;
   }
   return FALSE;
}


?>