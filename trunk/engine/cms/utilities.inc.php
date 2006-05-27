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
 * Filter all empty values from an array
 *
 * @param array(mixed) $in_array
 * @return array(mixed)
 */
function xanth_filter_empty_values($in_array)
{
	$ret = array();
	
	foreach($in_array as $elem)
	{
		if(!empty($elem))
			$ret[] = $elem;
	}
	
	return $ret;
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
	function onRender()
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



?>
