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

//------------------------------------------------------------------------------------------------------------------//
// Log functions
//------------------------------------------------------------------------------------------------------------------//
class xLogEntry
{
	var $level;
	var $component;
	var $message;
	var $filename;
	var $line;
	
	function xLogEntry($level,$component,$message,$filename,$line)
	{
		$this->level = $level;
		$this->component = $component;
		$this->message = $message;
		$this->filename = $filename;
		$this->line = $line;
	}
};

/**
 * Enqueue a log entry for async display.
 */
function xanth_add_screen_log($level, $component,$message,$filename,$line)
{
	global $xanth_screen_log;
	if(!isSet($xanth_screen_log))
		$xanth_screen_log = array();
		
	$xanth_screen_log[] = new xLogEntry($level,$component,$message,$filename,$line);
}

/**
 * Get screen log entries as anarray of objects xLogEntry
 *
 * @param $level (OPTIONAL)
 */
function xanth_get_screen_log($level = -1)
{
	global $xanth_screen_log;
	if(!isSet($xanth_screen_log))
		$xanth_screen_log = array();
		
	if($level == -1)
	{
		return $xanth_screen_log;
	}
	else
	{
		$retArr = array();
		foreach($xanth_screen_log as $log_entry)
		{
			if($log_entry->level == $level)
				$retArr[] = $log_entry;
		}
		return $retArr;
	}
}

/**
 * Clear screen log queue.
 *
 * @param $level (OPTIONAL)
 */
function xanth_clear_screen_log($level = -1)
{
	global $xanth_screen_log;
	if($level == -1)
	{
		unset($xanth_screen_log);
	}
	else
	{
		for($i = 0;$i < count($xanth_screen_log);)
		{
			if($xanth_screen_log[$i]->level == $level)
				array_splice($xanth_screen_log,$i,1);
			else
				$i++;
		}
	}
}


define('LOG_LEVEL_FATAL_ERROR',2);
define('LOG_LEVEL_ERROR',4);
define('LOG_LEVEL_WARNING',8);
define('LOG_LEVEL_NOTICE',16);
define('LOG_LEVEL_INFORMATIONAL',32);
define('LOG_LEVEL_DEBUG',64);

/**
* Function for logging messages and error. For every logging level a specific action will be taken. \n
* LOG_LEVEL_FATAL_ERROR: Application will die immediately and message will be displayed only on screen.\n
* LOG_LEVEL_ERROR: Application will stop execution, but a basic environment will be created for displaying message on screen,a log entry is added in db\n
* LOG_LEVEL_WARNING/LOG_LEVEL_NOTICE: Application will continue execution and a message is displayed on a region of screen,a log entry is added in db\n
* LOG_LEVEL_INFORMATIONAL: Application will log a message only in database\n
* LOG_LEVEL_DEBUG: Print debug message only in database if $debug is defined in config\n
*/
function xanth_log($level,$message,$component = '',$filename_or_function = '',$line = 0)
{
	if($level == LOG_LEVEL_FATAL_ERROR)
	{
		exit("Fatal Error on component $component ($filename_or_function@$line), $message");
	}
	
	if($level == LOG_LEVEL_ERROR || $level == LOG_LEVEL_WARNING || $level == LOG_LEVEL_NOTICE)
	{
		xanth_add_screen_log($level,$component,$message,$filename_or_function,$line);
	}
	
	if($level > LOG_LEVEL_FATAL_ERROR && $level < LOG_LEVEL_DEBUG)
	{
		xanth_db_log($level,$component,$message,$filename_or_function,$line);
	}
	
	if($level == LOG_LEVEL_DEBUG && xanth_conf_get('debug', false))
	{
		xanth_db_log($level,$component,$message,$filename_or_function,$line);
	}
}

/**
 * Callback function that log errors,warnigs,and notices. Note that in xanthine this is called only from code where core.php.inc is not included
 * and from unsuppressed php error, for other needs use xanth_log instead.
 */
function xanth_php_error_handler($errno, $message, $filename, $line) 
{
	if($errno == E_USER_ERROR)
	{
		xanth_log(LOG_LEVEL_ERROR,$message,'PHP', $filename, $line);
	}
	elseif($errno == E_USER_WARNING || $errno == E_WARNING || $errno == E_NOTICE)
	{
		xanth_log(LOG_LEVEL_WARNING,$message,'PHP', $filename, $line);
	}
	elseif($errno == E_USER_NOTICE)
	{	
		xanth_log(LOG_LEVEL_NOTICE,$message,'PHP', $filename, $line);
	}
}

?>