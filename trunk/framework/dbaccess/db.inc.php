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
class xDB extends xObject
{
	/**
	 * @var int
	 * @access protected
	 */
	var $m_query_dump = array();
	
	/**
	 * @var int
	 * @access protected
	 */
	var $m_transaction_nesting = 0;
	
	/**
	 * @var bool
	 * @access protected
	 */
	var $m_transaction_failed = FALSE;
	
	
	/**
	 * Constructor
	 */
	function __construct()
	{
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
	function _dumpAddQuery($query) 
	{
		$this->m_query_dump[] = $query;
	}

	/**
	* Reset the actual query count,in the current script execution.
	*/
	function dumpReset() 
	{
		$this->m_query_dump = array();
	}

	/**
	* Get the current query count,in the current script execution.
	*
	* @return int
	*/
	function dumpGet()
	{
		return $this->m_query_dump;
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
		
		$args = func_get_args();
		array_shift($args);
		if(isset($args[0]) && is_array($args[0])) // 'All arguments in one array' syntax
			$args = $args[0];
		
		$foo = $this->_getQueryCallbackFunction();
		
		$foo($args, TRUE);
		$query = preg_replace_callback('/(%d|%s|%%|%f|%b)/', $foo, $query);
		if(xConf::get('debug',true))
			$this->_dumpAddQuery($query);
			
		$result = $this->_query($query);
		
		if($result === FALSE)
		{
			xLog::log('Database',LOG_LEVEL_ERROR,'Database errno: '.$this->errno().'. Error: '.$this->error(),
				__FILE__,__LINE__);
			$this->m_transaction_failed = TRUE;
		}
			
		return $result;
	}
	
	
	/**
	 * Automatically construct and executes a query from an associative arrays. A transaction is created if
	 * Inserting/Updating multiple tables.
	 *
	 * @param string $action on between 'SELECT','INSERT','UPDATE'.
	 *
	 *
	 * @param array $records An array with this structure :\n
	 * $params[<table name>][<column_name>]["type"] = a param substituted into the query using printf() syntax (eg. '%s').\n
	 * $params[<table name>][<column_name>]["connector"] = A boolean connector to select this column (eg. AND/OR)(OPTIONAL).\n
	 * $params[<table name>][<column_name>]["value"] = The value of the column. If this is NULL in a select query,
	 * simply this column will be ignored, in a insert/update query it sets the column to null.\n
	 * $params[<table name>][<column_name>]["comparator"] = A comparator for use with value (eg =,<>,<,>)(OPTIONAL).\n
	 * $params[<table name>][<column_name>]["join"] = A table.columnname to join this column with (eg. table.column)(OPTIONAL).\n
	 * \n
	 * You can add another array $params[<table name>][<column_name>]["value"][] for join,value,connector,comparator 
	 * to generate a query between round brackets(only valid for where). In this case you can define 
	 * an outer connector ($params[<table name>][<column_name>]["outer_connector"] = AND/OR).
	 * @param array $where An array as for $records
	 * @param string $append
	 * @deprecated
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
					$out_update = '';
					$out_where = '';
					
					$isfirst_update = TRUE;
					foreach($column as $colname => $param)
					{
						if(!$isfirst_update)
							$out_update .= ',';
						else
							$isfirst_update = FALSE;
						
						
						if(isset($param['value']))
						{
							$out_update .= $colname . '=' . $param['type'];
							$values[] = $param['value'];
						}
						else
						{
							$out_update .= $colname . ' SET NULL ';
						}
					}
					
					$isfirst_where = TRUE;
					foreach($where[$table_name] as $colname => $param)
					{
						$connector = 'AND';
						if(isset($param['connector']))
							$connector = $param['connector'];
						
						$comparator = '=';
						if(isset($param['comparator']))
							$comparator = $param['comparator'];
							
						if(isset($param['value']))
						{
							if(!$isfirst_where)
								$out_where .= ' ' . $connector . ' ';
							else
								$isfirst_where = FALSE;
						
							$out_where .= $colname . $comparator . $param['type'];
							$values[] = $param['value'];
						}
					}
					
					if(!empty($out_where))
						$out_where = ' WHERE ' . $out_where;
					
					$out =  'UPDATE  ' . $table_name . ' SET ' . $out_update . $out_where . ' '. $extra_query;
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
					$out_values = '';
					$out_bind = '';
					
					$isfirst = TRUE;
					foreach($column as $colname => $param)
					{
						if(isset($param['value']))
						{
							if(!$isfirst)
							{
								$out_values .= ',';
								$out_bind .= ',';
							}
							else
							{
								$isfirst = FALSE;
							}
							
							$out_bind .= $colname;
							$out_values .= $param['type'];
							$values[] = $param['value'];
						}
					}
					
					$out =  'INSERT INTO  ' . $table_name . ' (' .$out_bind  . ') VALUES (' .$out_values . ') ' . $extra_query;
					array_merge($values,$extra_values);
					$this->query($out,$values);
					
					if($debug)
						var_dump($out);
				}
				
				return $this->commitTransaction();
		
			case 'SELECT':
				$out_select = 'SELECT * FROM ';
				$out_where = '';
				
				$isfirst_select = TRUE;
				$isfirst_where = TRUE;
				foreach($where as $table_name => $column)
				{
					if(!$isfirst_select)
						$out_select .= ',';
					else
						$isfirst_select = FALSE;
					
					$out_select .= $table_name;
					
					$connector = 'AND';
					if(isset($param['connector']))
						$connector = $param['connector'];
					
					$comparator = '=';
					if(isset($param['comparator']))
						$comparator = $param['comparator'];
							
					//set column value
					if(isset($param['value']))
					{
						if(!$isfirst_where)
							$out_where .= ' ' . $connector . ' ';
						else
							$isfirst_where = FALSE;
						
						$out_where .= $table_name . '.' . $colname . $comparator . $param['type'];
						$values[] = $param['value'];
					}
					
					//now join
					if(isset($param['join']))
					{
						foreach($param['join'] as $join)
						{
							if(!$isfirst)
								$out_where .= ' ' . $connector . ' ';
							else
								$isfirst_where = false;
							
							$out_where .= $table_name . '.' . $colname . $comparator . $join;
						}
					}
				}
				if(!empty($out_where))
					$out = $out_select . ' WHERE ' . $out_where;
				else
					$out = $out_select;
					
				$out .=  ' ' . $extra_query;
				array_merge($values,$extra_values);
				if($debug)
					var_dump($out);
				return $this->query($out,$values);
				
			default: 
				assert(FALSE);
		}
		
		
	}

	/**
	 * @access private
	 */
	function _generateWhereClause($where,&$values)
	{
		$out = '';
		$first = true;
		foreach($where as $clause)
		{
			if(isset($clause['clause'])) //simple clause
			{
				if(array_key_exists('value',$clause))
					if($clause['value'] === NULL)
						continue;
					else
						if(is_array($clause['value']))
							$values = array_merge($values,$clause['value']);
						else
							$values[] = $clause['value'];
				
				if(!$first)
					$out .= ' ' . $clause['connector'] . ' ';
				else
					$first = false;
					
				$out .= $clause['clause'];
			}
			else 						//nested clause
			{
				$connector = $clause['connector'];
				unset($clause['connector']);
				
				if(!$first)
					$out .= ' ' . $connector . ' ';
				else
					$first = false;
					
				$out .= '(' . $this->_generateWhereClause($clause,$values) . ')';
			}
		}
		
		if(!empty($out))
			$out = ' WHERE ' . $out;
		return $out;
	}
	
	/**
	 * @access private
	 */
	function _generateOrderClause($order,&$values)
	{
		$out = '';
		$first = true;
		foreach($order as $clause)
		{
			if(isset($clause['column']))
			{
				if(!$first)
					$out .= ',';
				else
					$first = false;
				
				if(strcasecmp($clause['direction'],'asc') !== 0 && strcasecmp($clause['direction'],'desc') !== 0)
				{
					xLog::log('Database',LOG_LEVEL_ERROR,'Invalid direction for ORDER clause: "'.$clause['direction'].'"',
						__FILE__,__LINE__);
				}
				else
				{
					$out .= '%s ' . $clause['direction'];
					$values[] = $clause['column'];
				}
			}
		}
		
		if(!empty($out))
			$out = ' ORDER BY ' . $out;
		return $out;
	}
	
	/**
	 * @access private
	 */
	function _generateLimitClause($limit,&$values)
	{
		if(!empty($limit))
		{
			$out = ' LIMIT %d,%d';
			$values[] = $limit['offset'];
			$values[] = $limit['elements'];
			
			return $out;
		}
		
		return '';
	}
	
	
	
	
	/**
	 * 
	 * @param string $columns The columns to select.
	 * @param string $tables The tables to select colums from
	 * @param array $where An array so structured: <br>
	 * <code>
	 * $where[0]["clause"] = "table.column = '%s'"; 
	 * $where[0]["connector"] = "AND";
	 * $where[0]["value"] = $val (or an array of values);
	 * </code>
	 * You can also create nested where clause by defining another sub array:
	 * <code>
	 * $where[0]["connector"] = "AND";
	 * $where[0][0]["clause"] = "table.column = '%s'";
	 * $where[0][0]["connector"] = "AND";
	 * $where[0][0]["value"] = $val;
	 * <code>
	 * <b>NOTE:</b> If ['value'] exists and is null the whole clause will be ingored,
	 * if ['value'] does not exists at all the clause is inserted normally but no values are inserted.
	 * @param array $limit An array so structured:
	 * <code>
	 * $limit['offset'] = 0
	 * $limit['elements'] = 10
	 * </code>
	 * @param array $order An array so structured:
	 * <code>
	 * $order[0]['column'] = 0
	 * $order[0]['direction'] = [ASC | DESC]
	 * </code> 
	 */
	function autoQuerySelect($columns,$tables,$where,$order = array(),$limit = array(),$debug = FALSE)
	{
		$values = array();
		$out_where = $this->_generateWhereClause($where,$values);
		$out_order = $this->_generateOrderClause($order,$values);
		$out_limit = $this->_generateLimitClause($limit,$values);
		
		$query = 'SELECT '.$columns.' FROM '.$tables.$out_where.$out_order.$out_limit;
		
		if($debug)
		{
			echo "Query: " . $query . ' , VALUES: ';
			var_dump($values);
		}
		
		return $this->query($query,$values);
	}
	
	
	/**
	 * 
	 * @param string $table The table to insert into.
	 * @param string $records An array so structured:
	 * <code>
	 * $records[0]["name"] = "column_name";
	 * $records[0]["type"] = "'%s'";
	 * $records[0]["value"] = $val;
	 * </code>
	 * <b>NOTE:</b> If ['value'] exists and is null, NULL is inserted,
	 * if ['value'] does not exists the whole clause is ignored so default is inserted.
	 */
	function autoQueryInsert($table,$records,$debug = FALSE)
	{
		$values = array();
		
		$first = true;
		$out_columns = '';
		$out_values = '';
		foreach($records as $record)
		{
			$type = $record['type'];
			if(array_key_exists('value',$record))
			{
				if($record['value'] === NULL)
					$type = 'NULL';
				else
					$values[] = $record['value'];
			}
			else
				continue;
			
			if(!$first)
			{
				$out_columns .= ',';
				$out_values .= ',';
			}			
			else
				$first = false;
			
			$out_columns .= $record['name'];
			$out_values .= $type;
		}
		
		$query = 'INSERT INTO '.$table.' ('.$out_columns.') VALUES ('.$out_values.')';
		
		if($debug)
		{
			echo "Query: " . $query . ' , VALUES: ';
			var_dump($values);
		}
		
		return $this->query($query,$values);
	}
	
	/**
	 * 
	 * @param string $table The table to insert into.
	 * @param array() $where As in autoQuerySelect()
	 * @param string $records As in autoQueryInsert()
	 *
	 * <b>NOTE:</b> If ['value'] exists and is null NULL is inserted,
	 * if ['value'] does not exists the whole clause is ignored so default is inserted.
	 */
	function autoQueryUpdate($table,$records,$where,$debug = FALSE)
	{
		$values = array();
		
		$first = true;
		$out_update = '';
		foreach($records as $record)
		{
			$set = $record['name'] . '=' . $record['type'];
			if(array_key_exists('value',$record))
				if($record['value'] === NULL)
					$set = $record['name'] . ' SET NULL';
				else
					$values[] = $record['value'];
			
			if(!$first)
				$out_update .= ',';		
			else
				$first = false;
				
			$out_update .= $set;
		}
		
		$out_where = $this->_generateWhereClause($where,$values);
		if(!empty($out_where))
			$out_where = ' WHERE ' . $out_where;
			
		$query = 'UPDATE '.$table.' SET '.$out_update . $out_where;
		
		if($debug)
		{
			echo "Query: " . $query . ' , VALUES: ';
			var_dump($values);
		}
		
		return $this->query($query,$values);
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