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
 * Initialize a database connection.
 */
function xanth_db_connect($host,$db,$user,$pass,$port)
{
	// Check if MySQL support is present in PHP
	if (!function_exists('mysql_connect')) 
		exit('PHP MySQL support not enabled');

	// Allow for non-standard MySQL port.
	if(isset($port)) 
		$host = $host .':'. $port;

	$connection = mysql_connect($host, $user, $pass, TRUE);
	if(!$connection) 
		exit('Unable to connect to database server');

	if(!mysql_select_db($db))
		exit('Unable to select database');

	return $connection;
}

/**
 * Helper function for xanth_db_query().
 */
function _xanth_db_query($query) 
{
	$result = mysql_query($query);

	if(mysql_errno())
	{
		trigger_error(mysql_error() ."\nquery: ". $query, E_USER_WARNING);
		return FALSE;
	}
	
	return $result;
}

/**
 * Fetch one result row from the previous query as an object.
 *
 * @param $result
 *   A database query result resource, as returned from xanth_db_query().
 * @return
 *   An object representing the next row of the result. The attributes of this
 *   object are the table fields selected by the query.
 */
function xanth_db_fetch_object($result) 
{
	if ($result) 
	{
		return mysql_fetch_object($result);
	}
}

/**
 * Fetch one result row from the previous query as an array.
 *
 * @param $result
 *   A database query result resource, as returned from xanth_db_query().
 * @return
 *   An associative array representing the next row of the result. The keys of
 *   this object are the names of the table fields selected by the query, and
 *   the values are the field values for this result row.
 */
function xanth_db_fetch_array($result) 
{
	if ($result) 
	{
		return mysql_fetch_array($result, MYSQL_ASSOC);
	}
}

/**
 * Determine how many result rows were found by the preceding query.
 *
 * @param $result
 *   A database query result resource, as returned from xanth_db_query().
 * @return
 *   The number of result rows.
 */
function xanth_db_num_rows($result) 
{
	if ($result) 
	{
		return mysql_num_rows($result);
	}
}

/**
 * Determine whether the previous query caused an error.
 */
function xanth_db_error() 
{
	return mysql_errno();
}

/**
 * Returns a properly formatted Binary Large OBject value.
 *
 * @param $data
 *   Data to encode.
 * @return
 *  Encoded data.
 */
function xanth_db_encode_blob($data) 
{
	return "'" . mysql_real_escape_string($data) . "'";
}

/**
 * Returns text from a Binary Large Object value.
 *
 * @param $data
 *   Data to decode.
 * @return
 *  Decoded data.
 */
function xanth_db_decode_blob($data) 
{
	return $data;
}

/**
 * Prepare user input for use in a database query, preventing SQL injection attacks.
 */
function xanth_db_escape_string($text) 
{
	return mysql_real_escape_string($text);
}

/**
 * Lock a table.
 */
function xanth_db_lock_table($table) 
{
	xanth_db_query('LOCK TABLES {%s} WRITE', $table);
}

/**
 * Unlock all locked tables.
 */
function xanth_db_unlock_tables()
{
	xanth_db_query('UNLOCK TABLES');
}


/**
*
*/
function xanth_db_log($level,$component,$message,$filename,$line)
{
	//manual check to prevent deadlocks
	if(!is_int($level) || !is_int($line))
		return;
	
	$message = xanth_db_escape_string($message);
	$filename = xanth_db_escape_string($filename);
	$component = xanth_db_escape_string($component);
	
	$result = mysql_query("INSERT INTO xanth_log(level,component,message,filename,line,timestamp) VALUES($level,'$component','$message','$filename',$line,NOW())");
	if(!$result)
		exit('Logging failed:'. mysql_error());
}


?>