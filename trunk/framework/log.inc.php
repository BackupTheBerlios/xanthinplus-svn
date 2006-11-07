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
			$class = isset($stack['class']) ? $stack['class']:'';
			$type = isset($stack['type']) ? $stack['type']:'';
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
	var $m_referer;
	var $m_url;
	var $m_time;
	var $m_ip;
	
	function xLogEntry($id,$level,$message,$filename,$line,$referer,$url,$time,$ip,$stacktrace = NULL)
	{
		$this->m_id = $id;
		$this->m_level = $level;
		$this->m_message = $message;
		$this->m_filename = $filename;
		$this->m_line = $line;
		$this->m_referer = $referer;
		$this->m_url = $url;
		$this->m_time = $time;
		$this->m_ip = $ip;
		$this->m_stacktrace = $stacktrace;
	}
	
	
	/**
	 * Enqueue a log entry for async screen display.
	 * @param xLogEntry $log_entry the log entry
	 */
	function insertToScreen()
	{
		global $xanth_screen_log;
		if(!isset($xanth_screen_log))
			$xanth_screen_log = array();
		$xanth_screen_log[] = $this;
	}

	/**
	 * Get screen log entries as an array of objects xLogEntry
	 * @return array(xLogEntry) An array of requested log entries
	 * @static 
	 */
	function getFromScreen()
	{
		global $xanth_screen_log;
		if(!isSet($xanth_screen_log))
			$xanth_screen_log = array();
			
		return $xanth_screen_log;
	}

	/**
	 * Clears screen log queue.
	 * @static
	 */
	function clearScreen()
	{
		global $xanth_screen_log;
		$xanth_screen_log = array();
	}
	
	/**
	 * Render the screen log into a printable string. 
	 *
	 * @return string
	 * @static
	 */
	function renderFromScreen()
	{
		$output = '<div class="log">';
		foreach(xLogEntry::getFromScreen() as $entry)
		{
			$output .= 
			'<div class = "log-entry screen-log-level-'.xLog::getLevelString($entry->m_level).'">
			<ul>
				<li><span class="log-entry-name">ID</span>: <span class="log-entry-value">'.$entry->m_id.'</span></li>
				<li><span class="log-entry-name">Level</span>: <span class="log-entry-value">'.$entry->m_level.'</span></li>
				<li><span class="log-entry-name">Message</span>: <span class="log-entry-value">'.$entry->m_message.'</span></li>
				<li><span class="log-entry-name">Filename</span>: <span class="log-entry-value">'.$entry->m_filename.'</span></li>
				<li><span class="log-entry-name">Line</span>: <span class="log-entry-value">'.$entry->m_line.'</span></li>
				<li><span class="log-entry-name">Url</span>: <span class="log-entry-value">'.$entry->m_url.'</span></li>
				<li><span class="log-entry-name">Ip</span>: <span class="log-entry-value">'.$entry->m_ip.'</span></li>
				<li><span class="log-entry-name">Referer</span>: <span class="log-entry-value">'.$entry->m_referer.'</span></li>
				<li><span class="log-entry-name">Time</span>: <span class="log-entry-value">'.strftime('%c',$entry->m_time).'</span></li>
				<li><span class="log-entry-name">Stack Trace</span>: <span class="log-entry-value"><br/>'.
					$entry->m_stacktrace->renderTrace(). '</span></li>
			</ul>
			</div>
			';
		}
		
		$output .= '</div>';
		return $output;
	}
	
	/**
	 * Insert this log entry into db
	 */
	function insert()
	{
		$db =& xDB::getDB();
		//manual check to prevent deadlocks
		if(!is_int($this->m_level) || !is_int($this->m_line))
			return;
		
		$i = 0;
		$records[$i]["name"] = "level";
		$records[$i]["type"] = "%d";
		$records[$i]["value"] = $this->m_level;
		
		$i++;
		$records[$i]["name"] = "message";
		$records[$i]["type"] = "'%s'";
		$records[$i]["value"] = $this->m_message;
		
		$i++;
		$records[$i]["name"] = "filename";
		$records[$i]["type"] = "'%s'";
		$records[$i]["value"] = $this->m_filename;
		
		$i++;
		$records[$i]["name"] = "line";
		$records[$i]["type"] = "%d";
		$records[$i]["value"] = $this->m_line;
		
		$i++;
		$records[$i]["name"] = "time";
		$records[$i]["type"] = "'%s'";
		$records[$i]["value"] = $db->encodeTimestamp($this->m_time);
		
		$i++;
		$records[$i]["name"] = "ip";
		$records[$i]["type"] = "'%s'";
		$records[$i]["value"] = $this->m_ip;
		
		$i++;
		$records[$i]["name"] = "referer";
		$records[$i]["type"] = "'%s'";
		$records[$i]["value"] = $this->m_referer;
		
		$i++;
		$records[$i]["name"] = "url";
		$records[$i]["type"] = "'%s'";
		$records[$i]["value"] = $this->m_url;
		
		$i++;
		$records[$i]["name"] = "stacktrace";
		$records[$i]["type"] = "%b";
		$records[$i]["value"] = $db->encodeBlob(serialize($this->m_stacktrace));
		
		
		$db->autoQueryInsert('xanth_log',$records);
		$this->m_id = $db->getLastId();
	}
	
	/**
	 * Delete a log entry from db. Based on id.
	 */
	function delete()
	{
		$db =& xDB::getDB();
		$db->query("DELETE FROM xanth_log WHERE id = %d",$this->m_id);
	}
	
	/**
	 * Retrieves all logs.
	 */
	function dbFindAll()
	{
		$db =& xDB::getDB();
		$entries = array();
		$result = $db->query("SELECT * FROM xanth_log");
		while($row = $db->fetchObject($result))
		{
			$entries[] = new xLogEntry($row->id,$row->level,$row->message,$row->filename,
				$row->line,unserialize($db->decodeBlob($row->stacktrace)));
		}
		return $entries;
	}
};



define('LOG_LEVEL_FATAL_ERROR',2);
define('LOG_LEVEL_ERROR',4);
define('LOG_LEVEL_WARNING',8);
define('LOG_LEVEL_NOTICE',16);
define('LOG_LEVEL_DEBUG',32);

/**
* The xLog class contains static functions for message and error logging.
*/
class xLog
{

	/**
	* Function for logging messages and errors. The log will be added in db (it will be not added 
	* if level is DEBUG and the conf variable "debug" is set to false), a log is shown on screen 
	* if conf variable "display_log" is set to true. If the level is LOG_LEVEL_FATAL_ERROR the
	* application will die immediately.
	* 
	* @param int $level One of the predefined level constants
	* - LOG_LEVEL_FATAL_ERROR
	* - LOG_LEVEL_ERROR
	* - LOG_LEVEL_WARNING
	* - LOG_LEVEL_NOTICE
	* - LOG_LEVEL_DEBUG
	* @param string $message Description og the log entry
	* @param string $filename The filename where the log was generated (can use the __FILE__ keyword)
	* @param string $line The line where the log was generated (can use the __LINE__ keyword)
	* @static
	*/
	function log($level,$message,$filename = '',$line = 0)
	{
		$log_entry = new xLogEntry(0,$level,$message,$filename,$line,
			isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '',
			$_SERVER['REQUEST_URI'],time(),$_SERVER['REMOTE_ADDR'],NULL);
		
		if($level != LOG_LEVEL_DEBUG || xConf::get('debug', false))
		{
			$log_entry->m_stacktrace = xStackTrace::getCurrent(2);
			
			if(xConf::get('display_log', false))
				$log_entry->insertToScreen();
			
			$log_entry->insert();
		}
		
		if($level == LOG_LEVEL_FATAL_ERROR)
			exit("Fatal Error, please contact the webmaster");
	}
	
	/**
	 * Return a string representation of the provided log level
	 */
	function getLevelString($level)
	{
		switch($level)
		{
			case LOG_LEVEL_FATAL_ERROR:
				return 'error';
			case LOG_LEVEL_FATAL_ERROR:
				return 'fatal_error';
			case LOG_LEVEL_WARNING:
				return 'warning';
			case LOG_LEVEL_DEBUG:
				return 'debug';
			case LOG_LEVEL_NOTICE:
				return 'notice';
			default:
				return 'unknown';
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
		xLog::log(LOG_LEVEL_ERROR,$message, $filename, $line);
	elseif($errno == E_USER_WARNING || $errno == E_WARNING || $errno == E_NOTICE)
		xLog::log(LOG_LEVEL_WARNING,$message, $filename, $line);
	elseif($errno == E_USER_NOTICE)
		xLog::log(LOG_LEVEL_NOTICE,$message, $filename, $line);
}



?>