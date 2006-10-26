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
	* @var int
	* @access protected
	*/
	var $m_transaction_nesting;
	
	/**
	* @var bool
	* @access protected
	*/
	var $m_transaction_failed;
	
	/**
	* Constructor
	*/
	function xDB()
	{
		$this->m_query_count = 0;
		$this->m_transaction_nesting = 0;
		$this->m_transaction_failed = FALSE;
	}
	
	/**
	* Initialize a databse connection.
	*
	* @param string $host databse hostname
	* @param string $user database username
	* @param string $pass database password for username
	* @param string $port database listening port (give blank for default)
	* @abstract
	*/
	function connect($host,$user,$pass,$port = '')
	{
		//must override this function
		assert(FALSE);
	}
	
	/**
	* select a database
	* @abstract
	*/
	function selectDB($name)
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
	function errno() 
	{
		assert(FALSE);
	}
	
	/**
	 * Returns the last error explanation
	 *
	 * @return string
	 * @abstact
	 */
	function error() 
	{
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
	* Get the callback query function.
	*
	* @abstract
	* @return string
	*/
	function _getQueryCallbackFunction()
	{
		assert(false);
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
		if($this->m_transaction_nesting > 0 && $this->m_transaction_failed === TRUE)
			return false;
			
		$this->_queryIncrementCount();
		
		$args = func_get_args();
		array_shift($args);
		if(isset($args[0]) && is_array($args[0])) // 'All arguments in one array' syntax
			$args = $args[0];
		
		$foo = $this->_getQueryCallbackFunction();
		
		$foo($args, TRUE);
		$query = preg_replace_callback('/(%d|%s|%%|%f|%b)/', $foo, $query);
		$result = $this->_query($query);
		
		if($result === FALSE)
		{
			$this->m_transaction_failed = TRUE;
		}
			
		return $result;
	}

	/**
	 * Automatically construct and executes a query from an associative arrays. A transaction is created if
	 * Inserting/Updating multiple tables.
	 *
	 * @param string $action on between 'SELECT','INSERT','UPDATE'.
	 
	 
	 * @param array $records An array with this structure :\n
	 * $params[<table name>][<column_name>]["type"] = a param substituted into the query using printf() syntax.\n
	 * $params[<table name>][<column_name>]["connector"] = A boolean connector to select this column (AND/OR).\n
	 * $params[<table name>][<column_name>]["value"] = The value of the column. If this is NULL in a select query,
	 * simply this column will be ignored, in a insert/update query it sets the column to null.\n
	 * $params[<table name>][<column_name>]["join"] = A table.columnname to join this column with.\n
	 * @param array $where An array as for $records
	 * @param string $append
	 */
	function autoQuery($action,$records,$where,$extra_query = '',$extra_values = array(),$debug = false)
	{
		$out = '';
		$values = array();
		switch($action)
		{
			case 'UPDATE':
			
				$this->startTransaction();
				
				foreach($records as $table_name => $column)
				{
					$out1 = '';
					$out2 = '';
					
					$first1 = TRUE;
					foreach($column as $colname => $param)
					{
						if(!$first1)
							$out1 .= ',';
						else
							$first1 = FALSE;
						
						
						if(isset($param['value']))
						{
							$out1 .= $colname . '=' . $param['type'];
							$values[] = $param['value'];
						}
						else
						{
							$out1 .= $colname . ' SET NULL ';
						}
					}
					
					$first1 = TRUE;
					foreach($where[$table_name] as $colname => $param)
					{
						if(isset($param['value']))
						{
							if(!$first1)
								$out2 .= ' ' . $param['connector'] . ' ';
							else
								$first1 = FALSE;
						
							$out2 .= $colname . '=' . $param['type'];
							$values[] = $param['value'];
						}
					}
					
					$out =  'UPDATE  ' . $table_name . ' SET ' . $out1 . ' WHERE '.$out2. ' '. $extra_query;
					array_merge($values,$extra_values);
					$this->query($out,$values);
					
					if($debug)
						echo "<br/>Query: $out";
				}
				
				return $this->commitTransaction();
				
				
				
			case 'INSERT':
			
				$this->startTransaction();
				
				foreach($records as $table_name => $column)
				{
					$out2 = '';
					$out1 = '';
					
					$first1 = TRUE;
					foreach($column as $colname => $param)
					{
						if(isset($param['value']))
						{
							if(!$first1)
							{
								$out1 .= ',';
								$out2 .= ',';
							}
							else
							{
								$first1 = FALSE;
							}
							
							$out1 .= $colname;
							$out2 .= $param['type'];
							$values[] = $param['value'];
						}
					}
					
					$out =  'INSERT INTO  ' . $table_name . ' (' . $out1 . ') VALUES (' .$out2 . ') ' . $extra_query;
					array_merge($values,$extra_values);
					$this->query($out,$values);
					
					if($debug)
						echo "<br/>Query: $out";
				}
				
				return $this->commitTransaction();
		
			case 'SELECT':
				$out1 = '';
				$out2 = '';
				$out1 .= 'SELECT * FROM ';
				
				$first1 = TRUE;
				$first2 = TRUE;
				foreach($where as $table_name => $column)
				{
					if(!$first1)
						$out1 .= ',';
					else
						$first1 = FALSE;
					
					$out1 .= $table_name;
					
					foreach($column as $colname => $param)
					{
						//set column value
						if(isset($param['value']))
						{
							if(!$first2)
							{
								$out2 .= ' ' . $param['connector'] . ' ';
							}
							else
							{
								$first2 = FALSE;
							}
							
							$out2 .= $table_name . '.' . $colname .'='. $param['type'];
							$values[] = $param['value'];
						}
						
						//now join
						if(isset($param['join']))
						{
							foreach($param['join'] as $join)
							{
								if(!$first2)
								{
									$out2 .= ' ' . $param['connector'] . ' ';
								}
								else
								{
									$first2 = false;
								}
								
								$out2 .= $table_name . '.' . $colname .'='. $join;
							}
						}
					}
					if(!empty($out2))
						$out = $out1 . ' WHERE ' . $out2;
					else
						$out = $out1;
				}
				$out .=  ' ' . $extra_query;
				array_merge($values,$extra_values);
				if($debug)
					echo "<br/>Query: $out";
				return $this->query($out,$values);
				
			default: 
				assert(FALSE);
		}
		
		
	}

	/**
	 * Starts a new transaction. Transaction nesting is allowed but nested transactions are ignored.
	 * If a query fails during execution, the transaction is set to failed and on commitTransaction() 
	 * it will be rolled back. After a transaction is failed, all the query inside it will be ignored.
	 *
	 * @return int The nesting level
	 */
	function startTransaction()
	{
		if($this->m_transaction_nesting <= 0) //first transaction
		{
			$this->_startTransaction();
			$this->m_transaction_nesting = 1;
			$this->m_transaction_failed = FALSE;
		}
		else
		{
			$this->m_transaction_nesting++;
		}
		
		return $this->m_transaction_nesting;
	}

	/**
	 * Commit the current transaction or if the transaction is failed executes a rollback.
	 *
	 * @return bool If transaction failed it return false, true otherwise.
	 */
	function commitTransaction()
	{
		if($this->m_transaction_nesting === 1)
		{
			$this->m_transaction_nesting = 0;
			
			if($this->m_transaction_failed === TRUE)
			{
				$this->_rollback();
				return FALSE;
			}
			else
			{
				$this->_commit();
				return TRUE;
			}
		}
		else
		{
			$this->m_transaction_nesting--;
			
			if($this->m_transaction_failed === TRUE)
				return FALSE;
			else
				return TRUE;
		}
	}

	/**
	 * Explicitly set to failed a transaction.
	 */
	function failTransaction()
	{
		$this->m_transaction_failed = TRUE;
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