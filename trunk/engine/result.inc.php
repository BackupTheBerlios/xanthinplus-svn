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
 * Represent the result of a fucntion.
 */
class xResult
{
	/**
	 * @var mixed
	 */
	var $m_value;
	
	/**
	 * @var string
	 */
	var $m_error;
	
	
	function xResult($value,$error = NULL)
	{
		$this->m_value = $value;
		$this->m_error = $error;
	}
	
	
	/**
	 * Returns true if the current result represent an error
	 */
	function isError()
	{
		return !empty($this->m_error);
	}
}



/**
 * A set of results
 */
class xResultSet
{
	var $m_results;
	
	function xResultSet($results = array())
	{
		$this->m_results = $results;
	}
	
	
	/**
	 * Returns true if the result set contains no results
	 */
	function isEmpty()
	{
		return empty($this->m_results);
	}
	
	/**
	 * Returns true if the current result set contains errors
	 * 
	 * @return bool
	 */
	function containsErrors()
	{
		foreach($this->m_results as $result)
			if($result->isError())
				true;
		
		return false;
	}
	
	
	/**
	 * Returns an array containing all errors contained in results if 
	 * they are not empty.
	 */
	function getErrors()
	{
		$ret = array();
		foreach($this->m_results as $result)
			if(!empty($result->m_error))
				$ret[] = $result->m_error;
		
		return $ret;
	}
	
	/**
	 * Returns an array containing all not NULL values in this result set.
	 * 
	 * @param bool $merge_arrays
	 */
	function getValues($merge_arrays = false)
	{
		$ret = array();
		
		if($merge_arrays)
		{
			foreach($this->m_results as $result)
				if($result->m_value !== NULL)
					if(is_array($result->m_value))
						$ret = array_merge($ret,$result->m_value);
					else
						$ret[] = $result->m_value;
		}
		else
		{
			foreach($this->m_results as $result)
				if($result->m_value !== NULL)
					$ret[] = $result->m_value;
		}
		
		return $ret;
	}
	
	
	/**
	 * Returns an array containing all not NULL values contained in a result without errors 
	 * in this result set.
	 * 
	 * @param bool $merge_arrays
	 */
	function getValidValues($merge_arrays = false)
	{
		$ret = array();
		
		if($merge_arrays)
		{
			foreach($this->m_results as $result)
				if($result->m_value !== NULL && !$result->isError())
					if(is_array($result->m_value))
						$ret = array_merge($ret,$result->m_value);
					else
						$ret[] = $result->m_value;
		}
		else
		{
			foreach($this->m_results as $result)
				if($result->m_value !== NULL && !$result->isError())
					$ret[] = $result->m_value;
		}
		
		return $ret;
	}
}

?>