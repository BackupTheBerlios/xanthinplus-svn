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


if(version_compare(PHP_VERSION,"5.0") < 0)
{
	require(dirname(__FILE__) . '/php4compat.inc.php');
}
else
{
	require(dirname(__FILE__) . '/php5compat.inc.php');
}


/**
 * Defines some basic object features,and permits the use of __contruct
 * under php4.
 */
class xObject
{
	/**
	 * A hack to support __construct() on PHP 4. 
	 * Child class must not have php4 styòe contructor.
	 */
	function xObject()
	{
		$args = func_get_args();
		call_user_func_array(array(&$this, '__construct'), $args);
	}

	/**
	 * Class constructor, override in descendant classes.
	 */
	function __construct()
	{
	}


	/**
	 * @param string The name of the property
	 * @param mixed The value of the property to set
	 */
	function set($property, $value)
	{
		$this->$property = $value;
    }
    
    
	/**
	 * @param string The name of the property
	 * @return mixed The value of the property
	 */
	function get($property)
	{
		return $this->$property;
	}
	
	/**
	 * @param string The name of the property
	 * @return mixed The value of the property
	 */
	function &getRef($property)
	{
		return $this->$property;
	}
	
	
	/**
	 * @return bool
	 */
	function isA($class_name)
	{
		return xanth_instanceof($this,$class_name);
	}
	
	
	/**
	 * @return bool
	 */
	function equals(&$object)
	{
		if(is_object($object) && (serialize($this) == serialize($object)))
			return TRUE;
		else
			return FALSE;
	}
	
	
	/**
	 * @return string
	 */
	function __toString() 
	{
		ob_start();
		var_dump($this);
		return ob_get_clean();
	}
}



/**
 * Permits interaction with configuration variables.
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



/**
 * Provide methods to generate unique ids in relation to a table name.
 */
class xUniqueId
{	
	function xUniqueId()
	{	
		assert(FALSE);
	}
	
	/**
	 * Create a new association between a table and a unique id generator.
	 */
	function createNew($tablename)
	{
		$db =& xDB::getDB();
		$db->query("INSERT INTO uniqueid (tablename,currentid) VALUES ('%s',%d)",$tablename,0);
	}
	
	
	/**
	 * Generate a unique id in relation to a table name. For a correct use put this inside a transaction.
	 *
	 * @param string $tablename
	 * @return int
	 */
	function generate($tablename)
	{
		$db =& xDB::getDB();
		$ret = 0;
	
		$result = $db->query("SELECT currentid FROM uniqueid WHERE tablename = '%s'",$tablename);

		if($row = $db->fetchObject($result))
		{
			$ret = $row->currentid + 1;
			
			$db->query("UPDATE uniqueid SET currentid = %d WHERE tablename = '%s'",$ret,$tablename);
		}
		
		return $ret;
	}
};


/**
 * 
 */
function &ref($ref)
{
	return $ref;
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