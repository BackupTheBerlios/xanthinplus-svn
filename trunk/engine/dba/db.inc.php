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

//include right lib
if(xanth_conf_get('db_type','mysql') == 'mysql')
{
	require_once('mysql_db.inc.php');
}
else
{
	
}

/**
 * Helper function for db_query().
 */
function _xanth_db_query_callback($match, $init = FALSE) 
{
	static $args = NULL;
	if($init) 
	{
		$args = $match;
		return;
	}

	switch($match[1]) 
	{
	case '%d': 
		return (int) array_shift($args);
	case '%s':
		return xanth_db_escape_string(array_shift($args));
	case '%%':
		return '%';
	case '%f':
		return (float) array_shift($args);
	case '%b': // binary data
		return xanth_db_encode_blob(array_shift($args));
	}
}


/**
 * Runs a basic query in the active database.
 *
 * User-supplied arguments to the query should be passed in as separate
 * parameters so that they can be properly escaped to avoid SQL injection
 * attacks.
 *
 * @param $query
 *   A string containing an SQL query.
 * @param ...
 *   A variable number of arguments which are substituted into the query
 *   using printf() syntax. Instead of a variable number of query arguments,
 *   you may also pass a single array containing the query arguments.
 * @return
 *   A database query result resource, or FALSE if the query was not
 *   executed correctly.
 */
function xanth_db_query($query) 
{
	$args = func_get_args();
	array_shift($args);
	if(isset($args[0]) and is_array($args[0])) // 'All arguments in one array' syntax
	{ 
		$args = $args[0];
	}
	
	_xanth_db_query_callback($args, TRUE);
	$query = preg_replace_callback('/(%d|%s|%%|%f|%b)/', '_xanth_db_query_callback', $query);
	return _xanth_db_query($query);
}




?>