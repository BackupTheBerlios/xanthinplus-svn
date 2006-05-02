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
	/**
	* @var int
	* @access protected
	*/
	var $m_query_count;
	
	/**
	* @var bool
	* @access protected
	*/
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
	* Initialize a databse connection.
	*
	* @param string $host databse hostname
	* @param string $db database name
	* @param string $user database username
	* @param string $pass database password for username
	* @param string $port database listening port (give blank for default)
	* @abstract
	*/
	function connect($host,$db,$user,$pass,$port = '')
	{
		//must override this function
		assert(FALSE);
	}
	
	/**
	* Execute a raw query on current selected database.
	*
	* @param string $query The query
	* @access protected
	* @abstract
	*/
	function _query($query)
	{
		//must override this function
		assert(FALSE);
	}
	
	
	/**
	 * Fetch one result row from the previous query as an object.
	 *
	 * @param result_resource $result A database query result resource, as returned from xDB->query().
	 * @return object An object representing the next row of the result. The attributes of this  object are the table fields selected by the query.
	 * @abstract
	 */
	function fetchObject($result)
	{
		//must override this function
		assert(FALSE);
	}

	
	/**
	 * Fetch one result row from the previous query as an array.
	 *
	 * @param result_resource $result A database query result resource, as returned from xDB->query().
	 * @return array 
	 *   An associative array representing the next row of the result. The keys of
	 *   this object are the names of the table fields selected by the query, and
	 *   the values are the field values for this result row.
	 * @abstract
	 */
	function fetchArray($result) 
	{
		//must override this function
		assert(FALSE);
	}
	
	/**
	 * Determine how many result rows were found by the preceding query.
	 *
	 * @param result_resource $result A database query result resource, as returned from xDB->query().
	 * @return int The number of result rows.
	 * @abstract
	 */
	function numRows($result) 
	{
		//must override this function
		assert(FALSE);
	}
	
	
	/**
	 * Returns the error number from the last xDB function, or 0 (zero) if no error occurred.
	 *
	 * @return int The error code
	 * @abstract
	 */
	function lastError() 
	{
		//must override this function
		assert(FALSE);
	}
	
	/**
	 * Returns a properly formatted Binary Large OBject value.
	 *
	 * @param mixed $data  Data to encode.
	 * @return string Encoded data.
	 * @abstract
	 */
	function encodeBlob($data) 
	{
		//must override this function
		assert(FALSE);
	}

	/**
	 * Returns text from a Binary Large Object value.
	 *
	 * @param mixed $data Data to decode.
	 * @return string Decoded data.
	 * @abstract
	 */
	function decodeBlob($data) 
	{
		//must override this function
		assert(FALSE);
	}

	/**
	 * Prepare user input for use in a database query, preventing SQL injection attacks. 
	 *
	 * @param string $text the text to be escaped.
	 * @return string Returns the escaped string, or FALSE on error. 
	 * @abstract
	 */
	function escapeString($text)
	{
		//must override this function
		assert(FALSE);
	}
	
	/**
	 * Lock a table.
	 * 
	 * @param string $table the name of the table to be locked.
	 * @abstract
	 */
	function lockTable($table) 
	{
		//must override this function
		assert(FALSE);
	}

	/**
	 * Unlock all locked tables.
	 *
	 * @abstract
	 */
	function unlockTables()
	{
		//must override this function
		assert(FALSE);
	}

	/**
	* Starts a new transaction. This function is private, only for internal use.
	*
	* @access protected
	* @abstract
	*/
	function _startTransaction()
	{
		//must override this function
		assert(FALSE);
	}

	/**
	* Executes a commit. This function is private, only for internal use.
	*
	* @access protected
	* @abstract
	*/
	function _commit()
	{
		//must override this function
		assert(FALSE);
	}

	/**
	* Executes a rollback. This function is private, only for internal use.
	*
	* @access protected
	* @abstract
	*/
	function _rollback()
	{
		//must override this function
		assert(FALSE);
	}

	/**
	* Return last inserted id or NULL on error.
	*
	* @return int The last inserted id or NULL on error.
	* @abstract
	*/
	function getLastId()
	{
		//must override this function
		assert(FALSE);
	}

	/**
	* Decode a timestamp string as returned from db into a unix timestamp integer.
	*
	* @param string $db_timestamp Timestamp string as returned from db
	* @return int The decoded unix timestamp
	* @abstract
	*/
	function decodeTimestamp($db_timestamp)
	{
		//must override this function
		assert(FALSE);
	}


	/**
	* Encode a unix timestamp into a string timestamp in the format accepted from the db.
	*
	* @param int $timestamp The unix timestamp to be encoded
	* @return string The encoded timestamp in the format accepted from the db
	* @abstract
	*/
	function encodeTimestamp($timestamp)
	{
		//must override this function
		assert(FALSE);
	}


	/**
	* Save a log message into db. This function should not rise any php error log so should not 
	* use other function from xanthin framework.
	*
	* @param xLogEntry $logentry The xLogEntry object to be logged.
	* @abstract
	*/
	function log($logentry)
	{
		//must override this function
		assert(FALSE);
	}
	
	/**
	* Increment the actual query count,in the current script execution.
	*
	* @access protected
	*/
	function _queryIncrementCount() 
	{
		$this->m_query_count++;
	}

	/**
	* Reset the actual query count,in the current script execution.
	*/
	function queryResetCount() 
	{
		$this->m_query_count = 0;
	}

	/**
	* Get the current query count,in the current script execution.
	*
	* @return int
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
	 * @param string $query A string containing an SQL query.
	 * @param mixed ... A variable number of arguments which are substituted into the query
	 *   using printf() syntax. Instead of a variable number of query arguments,
	 *   you may also pass a single array containing the query arguments.
	 * @return result_resource A database query result resource, or FALSE if the query was not
	 *   executed correctly.
	 */
	function query($query) 
	{
		//must override this function
		assert(FALSE);
	}


	/**
	* Starts a new transaction.
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
	* Executes a commit.
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
	* Executes a rollback.
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
	* Returns the current global xDB object.
	*
	* @static
	*/
	function &getDB()
	{
		global $g_current_db;
		return $g_current_db;
	}
	
	/**
	* Sets the current global xDB object.
	*
	* @param xDB $dd
	* @static
	*/
	function setDB($db)
	{
		global $g_current_db;
		$g_current_db = $db;
	}
	
};//end xDB

	
?>