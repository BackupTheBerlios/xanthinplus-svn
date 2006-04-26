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
* Base class for all DB classes. A DB class provide an abstraction layer to Database access.
*/
class xDB
{
	//! @private
	var $m_query_count;
	
	//! @private
	var $m_is_transaction_started;
	
	/**
	* Constructor
	*/
	function xDB()
	{
		$this->m_query_count = 0;
		$this->m_is_transaction_started = false;
	}
	
	/**
	* Initialize a databse connection. Override this in your implementation.
	*
	* @param $host (string) databse hostname
	* @param $db (string) database name
	* @param $user (string) database username
	* @param $pass (string) database password for username
	* @param $port (string) database listening port (give blank for default)
	* @return Nothing.
	*/
	function connect($host,$db,$user,$pass,$port = '')
	{
		//must override this function
		assert(FALSE);
	}
	
	/**
	* Execute a raw query on current selected database. Override this in your implementation.
	*
	* @param query (string) the query
	* @private
	*/
	function _query($query)
	{
		//must override this function
		assert(FALSE);
	}
	
	
	/**
	 * Fetch one result row from the previous query as an object.  Override this in your implementation.
	 *
	 * @param $result (result resource) A database query result resource, as returned from xDB->query().
	 * @return (object) An object representing the next row of the result. The attributes of this  object are the table fields selected by the query.
	 */
	function fetchObject($result)
	{
		//must override this function
		assert(FALSE);
	}

	
	/**
	 * Fetch one result row from the previous query as an array. Override this in your implementation.
	 *
	 * @param $result (result resource) A database query result resource, as returned from xDB->query().
	 * @return
	 *   An associative array representing the next row of the result. The keys of
	 *   this object are the names of the table fields selected by the query, and
	 *   the values are the field values for this result row.
	 */
	function fetchArray($result) 
	{
		//must override this function
		assert(FALSE);
	}
	
	/**
	 * Determine how many result rows were found by the preceding query. Override this in your implementation.
	 *
	 * @param $result (result resource) A database query result resource, as returned from xDB->query().
	 * @return The number of result rows.
	 */
	function numRows($result) 
	{
		//must override this function
		assert(FALSE);
	}
	
	
	/**
	 * Returns the error number from the last xDB function, or 0 (zero) if no error occurred.  Override this in your implementation.
	 * @return (int) the error code
	 */
	function lastError() 
	{
		//must override this function
		assert(FALSE);
	}
	
	/**
	 * Returns a properly formatted Binary Large OBject value.  Override this in your implementation.
	 *
	 * @param $data  Data to encode.
	 * @return  Encoded data.
	 */
	function encodeBlob($data) 
	{
		//must override this function
		assert(FALSE);
	}

	/**
	 * Returns text from a Binary Large Object value. Override this in your implementation.
	 *
	 * @param $data  Data to decode.
	 * @return Decoded data.
	 */
	function decodeBlob($data) 
	{
		//must override this function
		assert(FALSE);
	}

	/**
	 * Prepare user input for use in a database query, preventing SQL injection attacks. Override this in your implementation.
	 *
	 * @param  $text (string) the text to be escaped.
	 * @return (string) Returns the escaped string, or FALSE on error. 
	 */
	function escapeString($text) 
	{
		//must override this function
		assert(FALSE);
	}
	
	/**
	 * Lock a table.  Override this in your implementation.
	 * 
	 * @param $table (string) the name of the table to be locked.
	 * @return Nothing
	 */
	function lockTable($table) 
	{
		//must override this function
		assert(FALSE);
	}

	/**
	 * Unlock all locked tables. Override this in your implementation.
	 *
	 *  @return Nothing
	 */
	function unlockTables()
	{
		//must override this function
		assert(FALSE);
	}

	/**
	* Starts a new transaction. This function is private, only for internal use. Override this in your implementation.
	*
	* @private
	* @return nothing
	*/
	function _startTransaction()
	{
		//must override this function
		assert(FALSE);
	}

	/**
	* Executes a commit. This function is private, only for internal use. Override this in your implementation.
	*
	* @return nothing
	* @private
	*/
	function _commit()
	{
		//must override this function
		assert(FALSE);
	}

	/**
	* Executes a rollback. This function is private, only for internal use. Override this in your implementation.
	*
	* @return nothing
	* @private
	*/
	function _rollback()
	{
		//must override this function
		assert(FALSE);
	}

	/**
	* Return last inserted id or NULL on error. Override this in your implementation.
	*
	* @return (int) the last inserted id or NULL on error.
	*/
	function getLastId()
	{
		//must override this function
		assert(FALSE);
	}

	/**
	* Decode a timestamp string as returned from db into a unix timestamp integer. Override this in your implementation.
	*
	* @param $db_timestamp (string) timestamp string as returned from db
	* @return (int) the decoded unix timestamp
	*/
	function decodeTimestamp($db_timestamp)
	{
		//must override this function
		assert(FALSE);
	}


	/**
	* Encode a unix timestamp into a string timestamp in the format accepted from the db. Override this in your implementation.
	*
	* @param $timestamp (int) the unix timestamp to be encoded
	* @return (string) the encoded timestamp in the format accepted from the db
	*/
	function encodeTimestamp($timestamp)
	{
		//must override this function
		assert(FALSE);
	}


	/**
	* Save a log message into db. This function should not rise any php error log so should not use other function from xanthin framework.  Override this in your implementation.
	*
	* @param $logentry (xLogEntry)  The xLogEntry object to be logged.
	* @return nothing
	*/
	function log($logentry)
	{
		//must override this function
		assert(FALSE);
	}
	
	/**
	 * Helper function for db_query(). Does not need to override this method in your implementation.
	 *
	 * @private
	 * @static
	 */
	function _query_callback($match, $init = FALSE) 
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
			return $this->escapeString(array_shift($args));
		case '%%':
			return '%';
		case '%f':
			return (float) array_shift($args);
		case '%b': // binary data
			return $this->encodeBlob(array_shift($args));
		}
	}
	
	/**
	* Increment the actual query count,in the current script execution. Does not need to override this method in your implementation.
	*
	* @private
	*/
	function _queryIncrementCount() 
	{
		$this->m_query_count++;
	}

	/**
	* Reset the actual query count,in the current script execution. Does not need to override this method in your implementation.
	*/
	function queryResetCount() 
	{
		$this->m_query_count = 0;
	}

	/**
	* Get the current query count,in the current script execution. Does not need to override this method in your implementation.
	*/
	function queryGetCount() 
	{
		return $this->m_query_count;
	}

	/**
	 * Runs a basic query in the active database.
	 *
	 * User-supplied arguments to the query should be passed in as separate
	 * parameters so that they can be properly escaped to avoid SQL injection
	 * attacks.
	 *
	 * Does not need to override this method in your implementation.
	 *
	 * @param $query (string) A string containing an SQL query.
	 * @param ... A variable number of arguments which are substituted into the query
	 *   using printf() syntax. Instead of a variable number of query arguments,
	 *   you may also pass a single array containing the query arguments.
	 * @return (result resource) A database query result resource, or FALSE if the query was not
	 *   executed correctly.
	 */
	function query($query) 
	{
		$this->_queryIncrementCount();
		
		$args = func_get_args();
		array_shift($args);
		if(isset($args[0]) and is_array($args[0])) // 'All arguments in one array' syntax
		{ 
			$args = $args[0];
		}
		
		xDB::_query_callback($args, TRUE);
		$query = preg_replace_callback('/(%d|%s|%%|%f|%b)/', 'xDB::_query_callback', $query);
		$result = $this->_query($query);
		
		if($result == FALSE)
		{
			//rollback from transaction
			if(! empty($this->m_is_transaction_started))
			{
				$this->_rollback();
				$this->m_is_transaction_started = FALSE;
			}
		}

		return $result;
	}


	/**
	* Starts a new transaction. Does not need to override this method in your implementation.
	*
	* @return Nothing.
	*/
	function startTransaction()
	{
		if(empty($this->m_is_transaction_started))
		{
			$this->_startTransaction();
			$this->m_is_transaction_started = TRUE;
		}
	}

	/**
	* Executes a commit. Does not need to override this method in your implementation.
	*
	* @return Nothing.
	*/
	function commit()
	{
		if(! empty($this->m_is_transaction_started))
		{
			$this->_commit();
			$this->m_is_transaction_started = FALSE;
		}
	}

	/**
	* Executes a rollback. Does not need to override this method in your implementation.
	*
	* @return Nothing.
	*/
	function rollback()
	{
		if(! empty($this->m_is_transaction_started))
		{
			$this->_rollback();
			$this->m_is_transaction_started = FALSE;
		}
	}
	
	/**
	* Returns the current global xDB object. Does not need to override this method in your implementation.
	*
	* @static
	*/
	function getDB()
	{
		global $g_current_db;
		return $g_current_db;
	}
	
	/**
	* Sets the current global xDB object. Does not need to override this method in your implementation.
	*
	* @param $db (xDB)
	* @static
	*/
	function setDB($db)
	{
		global $g_current_db;
		$g_current_db = $db;
	}
	
};//end xDB


?>