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
 * Represent stack trace
 */
class xStackTrace
{
	var $m_trace;
	
	function xStackTrace($trace)
	{
		$this->m_trace = $trace;
	}
	
	/**
	 * Retrieve the current stack trace
	 *
	 * @param int $ntoremove the number of stack trace to remove from the beginning
	 * @return xStackTrace
	 * @static
	 */
	function getCurrent($ntoremove)
	{
		$trace = debug_backtrace();
		for($i = 0;$i < $ntoremove;$i++)
		{
			unset($trace[$i]);
		}
		
		return new xStackTrace($trace);
	}
	
	/**
	 * Returns a human readable representation of this stack trace
	 *
	 * @return string
	 */
	function renderTrace()
	{
		$output = '';
		foreach($this->m_trace as $stack)
		{
			$class = isset($stack['class'])?$stack['class']:'';
			$type = isset($stack['type'])?$stack['type']:'';
			$output .= '<i>'. $class .$type.$stack['function'] . '</i> in file ' . $stack['file'] .
				'@' . $stack['line'] . '<br/>';
		}
		
		return $output;
	}
};



/**
* A log entry.
*/
class xLogEntry
{
	var $m_id;
	var $m_level;
	var $m_message;
	var $m_filename;
	var $m_line;
	var $m_stacktrace;
	
	function xLogEntry($id,$level,$message,$filename,$line,$stacktrace = NULL)
	{
		$this->m_id = $id;
		$this->m_level = $level;
		$this->m_message = $message;
		$this->m_filename = $filename;
		$this->m_line = $line;
		$this->m_stacktrace = $stacktrace;
	}
	
	
	/**
	 * Enqueue a log entry for async screen display.
	 * @param xLogEntry $log_entry the log entry
	 */
	function insertToScreen()
	{
		global $xanth_screen_log;
		if(!isSet($xanth_screen_log))
			$xanth_screen_log = array();
		$xanth_screen_log[] = $this;
	}

	/**
	 * Get screen log entries as an array of objects xLogEntry
	 * @param int $level  if not equal to -1, only log entries with this level will be returned.
	 * @return array(xLogEntry) An array of requested log entries
	 * @static 
	 */
	function getFromScreen($level = -1)
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
	function clearScreen($level = -1)
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
	
	/**
	 * Render the screen log into a printable string. 
	 *
	 * @return string
	 * @static
	 */
	function renderFromScreen()
	{
		$output = "";
		foreach(xLogEntry::getFromScreen() as $entry)
		{
			$output .= '<div class="log"><table border="1"><tr><td>Log</td><td>';
			
			$output .= "<table border='1'><tr><th>id</th><th>level</th><th>message</th><th>filename</th><th>line</th></tr>";
			$output .= '<tr><td>' . $entry->m_id . '</td><td>' . $entry->m_level . '</td><td>' .
				$entry->m_message . '</td><td>' . $entry->m_filename .'</td><td>' . $entry->m_line . '</td></tr>';
			$output .= "</table>";
			$output .= "</td><tr>";
			
			$output .= "<tr><td>Stacktrace</td><td>";
			if($entry->m_stacktrace != NULL)
			{
				$output .= $entry->m_stacktrace->renderTrace();			
			}
			$output .= "</td><tr>";
			$output .= "</table></div>";
		}
		
		return $output;
	}
	
	/**
	 *
	 */
	function renderFromDB()
	{
		$output = "";
		foreach(xLogEntry::dbFindAll() as $entry)
		{
			$output .= "<table border='1'><tr><td>Log</td><td>";
			
			$output .= "<table border='1'><tr><th>id</th><th>level</th><th>message</th><th>filename</th><th>line</th></tr>";
			$output .= '<tr><td>' . $entry->m_id . '</td><td>' . $entry->m_level . '</td><td>' .
				$entry->m_message . '</td><td>' . $entry->m_filename .'</td><td>' . $entry->m_line . '</td></tr>';
			$output .= "</table>";
			$output .= "</td><tr>";
			
			$output .= "<tr><td>Stacktrace</td><td>";
			if($entry->m_stacktrace != NULL)
			{
				$output .= $entry->m_stacktrace->renderTrace();			
			}
			$output .= "</td><tr>";
			$output .= "</table>";
		}
		
		return $output;
	}
	
	/**
	 * Insert this log entry into db
	 */
	function insert()
	{
		//manual check to prevent deadlocks
		if(!is_int($this->m_level) || !is_int($this->m_line))
			return;
		
		xDB::getDB()->query("INSERT INTO xanth_log(level,message,filename,line,timestamp,stacktrace) VALUES(%d,'%s','%s',%d,NOW(),".
			xDB::getDB()->encodeBlob(serialize($this->m_stacktrace)).")",
			$this->m_level ,$this->m_message,$this->m_filename,$this->m_line);
			
		$this->m_id = xDB::getDB()->getLastId();
	}
	
	/**
	 * Delete a log entry from db. Based on id.
	 */
	function delete()
	{
		xDB::getDB()->query("DELETE FROM xanth_log WHERE id = %d",$this->m_id);
	}
	
	/**
	 * Retrieves all logs.
	 */
	function dbFindAll()
	{
		$entries = array();
		$result = xDB::getDB()->query("SELECT * FROM xanth_log");
		while($row = xDB::getDB()->fetchObject($result))
		{
			$entries[] = new xLogEntry($row->id,$row->level,$row->message,$row->filename,
				$row->line,unserialize(xDB::getDB()->decodeBlob($row->stacktrace)));
		}
		return $entries;
	}
};



define('LOG_LEVEL_FATAL_ERROR',2);
define('LOG_LEVEL_ERROR',4);
define('LOG_LEVEL_WARNING',8);
define('LOG_LEVEL_NOTICE',16);
define('LOG_LEVEL_USER_MESSAGE',32);
define('LOG_LEVEL_AUDIT',64);
define('LOG_LEVEL_DEBUG',128);

/**
* The xLog class contains static functions for message and error logging.
*/
class xLog
{

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
		$log_entry = new xLogEntry(0,$level,$message,$filename,$line);
		
		if($level == LOG_LEVEL_FATAL_ERROR)
		{
			exit("Fatal Error ($filename_or_function@$line), $message");
		}
		
		if($level == LOG_LEVEL_ERROR || $level == LOG_LEVEL_WARNING || $level == LOG_LEVEL_NOTICE || $level == LOG_LEVEL_USER_MESSAGE)
		{
			$log_entry->m_stacktrace = xStackTrace::getCurrent(2);
			$log_entry->insertToScreen();
		}
		
		if($level > LOG_LEVEL_FATAL_ERROR && $level < LOG_LEVEL_DEBUG && $level != LOG_LEVEL_USER_MESSAGE)
		{
			$log_entry->insert();
		}
		
		if($level == LOG_LEVEL_DEBUG && xanth_conf_get('debug', false))
		{
			$log_entry->insert();
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