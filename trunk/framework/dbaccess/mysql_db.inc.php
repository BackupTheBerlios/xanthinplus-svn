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
* Represent the abstraction layer to the mysql db.
*/
class xDBMysql extends xDB
{
	//! @private
	var $m_connection = NULL;
	
	
	/**
	 * Constructor
	 */
	function __construct()
	{
		parent::__construct();
	}
	
	
	// DOCS INHERITHED  ========================================================
	function connect($host,$user,$pass,$port = '')
	{
		// Check if MySQL support is present in PHP
		if (!function_exists('mysql_connect')) 
			exit('PHP MySQL support not enabled');

		// Allow for non-standard MySQL port.
		if(isset($port)) 
			$host = $host .':'. $port;

		$this->m_connection = mysql_connect($host, $user, $pass, TRUE);
		echo mysql_error();
		if(!$this->m_connection) 
			exit('Unable to connect to database server');
		
		//set connection encoding
		if(!mysql_query("SET NAMES 'utf8'"))
			exit('Unable to select utf8 encoding for db conenction');
	}
	
	
	// DOCS INHERITHED  ========================================================
	function selectDB($name)
	{
		if(!mysql_select_db($name))
			exit('Unable to select database');
	}
	
	
	// DOCS INHERITHED  ========================================================
	function _query($query)
	{
		return mysql_query($query,$this->m_connection);
	}
	
	
	// DOCS INHERITHED  ========================================================
	function fetchObject($result)
	{
		if($result) 
		{
			return mysql_fetch_object($result);
		}
	}

	
	// DOCS INHERITHED  ========================================================
	function fetchArray($result) 
	{
		if ($result) 
		{
			return mysql_fetch_array($result);
		}
	}
	
	// DOCS INHERITHED  ========================================================
	function numRows($result) 
	{
		if ($result) 
		{
			return mysql_num_rows($result);
		}
	}
	
	// DOCS INHERITHED  ========================================================
	function errno() 
	{
		return mysql_errno($this->m_connection);
	}
	
	// DOCS INHERITHED  ========================================================
	function error() 
	{
		return mysql_error($this->m_connection);
	}
	
	// DOCS INHERITHED  ========================================================
	function encodeBlob($data) 
	{
		return "'" . mysql_real_escape_string($data) . "'";
	}

	// DOCS INHERITHED  ========================================================
	function decodeBlob($data) 
	{
		return $data;
	}

	// DOCS INHERITHED  ========================================================
	function escapeString($text) 
	{
		return mysql_real_escape_string($text);
	}

	// DOCS INHERITHED  ========================================================
	function _startTransaction()
	{
		$this->query('START TRANSACTION');
	}

	// DOCS INHERITHED  ========================================================
	function _commit()
	{
		$this->query('COMMIT');
	}

	// DOCS INHERITHED  ========================================================
	function _rollback()
	{
		$this->query('ROLLBACK');
	}
	
	// DOCS INHERITHED  ========================================================
	function getLastId()
	{
		$result = $this->query('SELECT LAST_INSERT_ID() as id');
		if($row = $this->fetchArray($result))
		{
			return $row['id'];
		}
		
		return NULL;
	}
	
	
	// DOCS INHERITHED  ========================================================
	function _getQueryCallbackFunction()
	{
		return 'xanth_mysql_query_callback';
	}

	// DOCS INHERITHED  ========================================================
	function decodeTimestamp($db_timestamp)
	{
		preg_match('/(\d{4})-(\d{2})-(\d{2}) (\d{2}):(\d{2}):(\d{2})/',$db_timestamp,$pieces);
		$unix_timestamp = mktime($pieces[4], $pieces[5], $pieces[6],$pieces[2], $pieces[3], $pieces[1]);
		return($unix_timestamp);
	}


	// DOCS INHERITHED  ========================================================
	function encodeTimestamp($timestamp)
	{
		return date('Y-m-d H-i-s',$timestamp);
	}
	

};//end xDBMysql


/**
 * Helper function for db_query().
 *
 * @access private
 * @static
 */
function xanth_mysql_query_callback($match, $init = FALSE) 
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
		return xDBMysql::escapeString(array_shift($args));
	case '%%':
		return '%';
	case '%f':
		return (float) array_shift($args);
	case '%b': // binary data
		return xDBMysql::encodeBlob(array_shift($args));
	}
}


?>