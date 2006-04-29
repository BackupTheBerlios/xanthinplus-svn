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
* A log entry.
*/
class xLogEntry
{
	var $level;
	var $message;
	var $filename;
	var $line;
	
	function xLogEntry($level,$message,$filename,$line)
	{
		$this->level = $level;
		$this->message = $message;
		$this->filename = $filename;
		$this->line = $line;
	}
};






/**
* A class containing static functions for managing screen logging
*/
class xScreenLog
{
	/**
	 * Enqueue a log entry for async display.
	 * @param xLogEntry $log_entry the log entry
	 * @static
	 */
	function add($log_entry)
	{
		global $xanth_screen_log;
		if(!isSet($xanth_screen_log))
			$xanth_screen_log = array();
		$xanth_screen_log[] = $log_entry;
	}

	/**
	 * Get screen log entries as an array of objects xLogEntry
	 * @param int $level  if not equal to -1, only log entries with this level will be returned.
	 * @return array(xLogEntry) An array of requested log entries
	 * @static 
	 */
	function get($level = -1)
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
	 * Clears screen log queue.
	 * @param int $level if not equal to -1,clears only log entries with this level.
	 * @static
	 */
	function clear($level = -1)
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

};


/**
* The xLog class contains static functions for message and error logging.
*/
class xLog
{
	define('LOG_LEVEL_FATAL_ERROR',2);
	define('LOG_LEVEL_ERROR',4);
	define('LOG_LEVEL_WARNING',8);
	define('LOG_LEVEL_NOTICE',16);
	define('LOG_LEVEL_USER_MESSAGE',32);
	define('LOG_LEVEL_AUDIT',64);
	define('LOG_LEVEL_DEBUG',128);

	/**
	* Function for logging messages and error. For every logging level a specific action will be taken.
	* - LOG_LEVEL_FATAL_ERROR: Application will die immediately and message will be displayed only on screen.
	* - LOG_LEVEL_ERROR: Application will stop execution, but a basic environment will be created for displaying message on screen,a log entry is added in db
	* - LOG_LEVEL_WARNING/LOG_LEVEL_NOTICE: Application will continue execution and a message is displayed on a region of screen,a log entry is added in db
	* - LOG_LEVEL_USER_MESSAGE: A message is displayed to the user
	* - LOG_LEVEL_AUDIT: Application will log a message only in database
	* - LOG_LEVEL_DEBUG: Print debug message only in database if $debug is defined in config
	*
	* @param int $level One of the predefined level constants
	* @param string $message Description og the log entry
	* @param string$filename The filename where the log was generated (can use the __FILE__ keyword)
	* @param string $line The line where the log was generated (can use the __LINE__ keyword)
	* @static
	*/
	function log($level,$message,$filename = '',$line = 0)
	{
		$log_entry = new xLogEntry($level,$message,$filename,$line);
		
		if($level == LOG_LEVEL_FATAL_ERROR)
		{
			exit("Fatal Error ($filename_or_function@$line), $message");
		}
		
		if($level == LOG_LEVEL_ERROR || $level == LOG_LEVEL_WARNING || $level == LOG_LEVEL_NOTICE || $level == LOG_LEVEL_USER_MESSAGE)
		{
			xScreenLog::add($log_entry);
		}
		
		if($level > LOG_LEVEL_FATAL_ERROR && $level < LOG_LEVEL_DEBUG && $level != LOG_LEVEL_USER_MESSAGE)
		{
			xDB::getDB()->log($log_entry);
		}
		
		if($level == LOG_LEVEL_DEBUG && xanth_conf_get('debug', false))
		{
			xDB::getDB()->log($log_entry);
		}
	}
};




/**
 * Callback function that log errors,warnigs,and notices. Note that in xanthine this is called only from code where core.php.inc is not included
 * and from unsuppressed php error, for other needs use xanth_log instead.
 */
function xanth_php_error_handler($errno, $message, $filename, $line) 
{
	if($errno == E_USER_ERROR)
	{
		xLog::log(LOG_LEVEL_ERROR,$message, $filename, $line);
	}
	elseif($errno == E_USER_WARNING || $errno == E_WARNING || $errno == E_NOTICE)
	{
		xLog::log(LOG_LEVEL_WARNING,$message, $filename, $line);
	}
	elseif($errno == E_USER_NOTICE)
	{
		xLog::log(LOG_LEVEL_NOTICE,$message, $filename, $line);
	}
}



?>