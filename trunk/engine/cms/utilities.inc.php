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
function _objWeightCompare($a, $b)
{
    if ($a->m_weight == $b->m_weight) 
	{
        return 0;
    }
	
    return ($a->m_weight < $b->m_weight) ? -1 : 1;
}


/**
 * Analyze the type of the current path alias/no alias and format properly the provided relative path
 *
 * @deprecated
 * @param string $path
 * @return string
 */
function xanth_relative_path($path)
{
	//todo
	return '?p='.$path;
}


/**
* An element to count the exexution time of the script. Render it at the foot of your page.
*/
class xExecutionTime extends xElement
{
	/**
	* Contructor
	*/
	function xExecutionTime()
	{
		$this->xElement();
	}
	
	// DOCS INHERITHED  ========================================================
	function render()
	{
		global $g_execution_started;
		return '' . xExecutionTime::_getmicrotime() - $g_execution_started;
	}
	
	/**
	 * Call this method when the execution of the script starts
	 *
	 * @static
	 */
	function executionStarted()
	{
		global $g_execution_started;
		$g_execution_started = xExecutionTime::_getmicrotime();
	}
	
	/**
	 * Necessary for PHP4
	 *
	 * @access private
	 */
	function _getmicrotime()
	{
	   list($usec, $sec) = explode(' ', microtime());
	   return ((float)$usec + (float)$sec);
	}
};



/**
 * Parse and extract data from an array given the path in a string
 *
 */
class xArrayString
{

	/**
	 * @access private
	 */
	function _parseKeys($array_string)
	{
		$keys = array();
		//parse array structure
		if(preg_match('#^([A-Z0-9_-]+)((\[[A-Z0-9_-]*\])*)$#i',$array_string,$pieces))
		{
			$keys[] = $pieces[1];
			if(!empty($pieces[2]))
			{
				if(preg_match_all('#\[([A-Z0-9_-]*)\]*#i',$pieces[2],$pieces))
				{
					array_unshift($pieces[1],$keys[0]);
					$keys = $pieces[1];
				}
			}
		}
		
		return $keys;
	}
	
	
	/**
	 * Exctract the value given by the string from the array
	 * ex.
	 * $array_string = 'name[key1][key2]';
	 * $array = array('name' => array('key1' => array('key2' => value)));
	 * returns value;
	 * @static
	 */
	function extractValue($array,$array_string)
	{
		$keys = xArrayString::_parseKeys($array_string);
		$value = $array;
		foreach($keys as $key)
		{
			$k = $key;
			if(is_numeric($k))
				$k = (int) $k;
		
			if(isset($value[$k]))
				$value = $value[$k];
			else
				return NULL;
		}
		return $value;
	}
	
	/**
	 * Generate an array from the given array string and value.
	 * @static
	 */
	function generateArray($array_string,$value,&$out_array)
	{
		$keys = xArrayString::_parseKeys($array_string);
		$out_array = xArrayString::_generateArray($keys,$value,$out_array);
	}
	
	
	
	function _generateArray($keys,$value,$out_array)
	{
		$key = array_shift($keys);
		if($key === NULL)
			return $value;
		
		$pass = array();
		if(isset($out_array[$key]))
			$pass = $out_array[$key];
			
		$out_array[$key] = xArrayString::_generateArray($keys,$value,$pass);
		return $out_array;
	}
};




/**
* Redirect to a new web page
*/
class xJavaScriptRedirect extends xElement
{
	/**
	 * @var strnig
	 */
	var $m_location;
	
	/**
	 * @var bool
	 */
	var $m_isPath;
	
	/**
	 * @var int
	 */
	var $m_delay;
	
	
	/**
	* Contructor
	*/
	function xJavaScriptRedirect($location,$isPath,$delay)
	{
		$this->xElement();
		
		$this->m_location = $location;
		$this->m_isPath = $isPath;
		$this->m_delay = $delay;
	}
	
	// DOCS INHERITHED  ========================================================
	function render()
	{
		$location = $this->m_location;
		if($this->m_isPath)
		{
			$location = 'http://' . $_SERVER['HTTP_HOST'] . rtrim(dirname($_SERVER['PHP_SELF']), '/\\') .
			'/' . $location;
		}

		return 
		'<script type="text/javascript">
		<!--
		function delayer(){
			window.location = "' . $location . '"
		}
		setTimeout(\'delayer()\','. $this->m_delay * 1000 . ')
		//-->
		</script>';
	}
};



?>
