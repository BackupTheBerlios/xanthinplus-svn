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


$g_xanth_ob_before_notifications = '';
$g_xanth_ob_after_notifications = '';


define('NOTIFICATION_NOTICE',2);
define('NOTIFICATION_WARNING',4);
define('NOTIFICATION_ERROR',8);


/**
 *
 */
class xNotificationEntry
{
	var $m_severity;
	var $m_message;
	var $m_duration;
	
	function xNotificationEntry($severity,$message,$duration)
	{
		$this->m_severity = $severity;
		$this->m_message = $message;
		$this->m_duration = $duration;
	}
}


/**
 * 
 */
class xNotificationsManager
{
	
	function xNotificationsManager()
	{
		assert(false);	
	}
	
	/**
	 *
	 * @param int $severity One between:
	 * - NOTIFICATION_NOTICE
	 * - NOTIFICATION_WARNING
	 * - NOTIFICATION_ERROR
	 * @param string $message
	 * @static
	 */
	function add($severity,$message,$duration = 1)
	{
		if(!isset($_SESSION['xanth_notifications']))
			$_SESSION['xanth_notifications'] = new xNotifications(); 
		
		$_SESSION['xanth_notifications']->m_entries[] = new xNotificationEntry($severity,$message,$duration);
	}
	
	
	/**
	 * @private
	 */
	function _renderAll()
	{
		if(!isset($_SESSION['xanth_notifications']))
			$_SESSION['xanth_notifications'] = new xNotifications(); 
			
		$notifications = $_SESSION['xanth_notifications'];
		$notifications->preconditions();
		$notifications->process();
		$notifications->filter();
		
		if($notifications->m_checked && $notifications->m_processed && $notifications->m_filtered !== NULL)
			return xModuleManager::invoke('xm_render',array(& $notifications->m_filtered));
			
		return '';
	}
	
	/**
	 * @access private
	 */
	function _clear()
	{
		$ret = new xNotifications();
		if(isset($_SESSION['xanth_notifications']))
		{
			foreach($_SESSION['xanth_notifications']->m_entries as $entry)
			{
				$entry->m_duration--;
				if($entry->m_duration > 0)
					$ret->m_entries[] = $entry;
			}
		}
		$_SESSION['xanth_notifications'] = $ret;
	}
	
	
	/**
	 * @internal
	 */
	function postProcessing()
	{
		global $g_xanth_ob_before_notifications;
		global $g_xanth_ob_after_notifications;
		
		$g_xanth_ob_after_notifications = ob_get_clean();
		ob_start();
		
		echo $g_xanth_ob_before_notifications;
		echo xNotificationsManager::_renderAll();
		xNotificationsManager::_clear();
		echo $g_xanth_ob_after_notifications;
	}
}


/**
* A static element that display all notifications emitted during ALL script execution. All
* means notifications emitted before and after that call of render() function.
*/
class xNotifications extends xWidget
{
	var $m_entries = array();
	
	/**
	 * Contructor
	 */
	function xNotifications()
	{	
		$this->xWidget('default');
	}
	
	
	/**
	 * Filter out entries 
	 */
	function _filter()
	{
		foreach($this->m_entries as $k => $v)
			$this->m_entries[$k]->m_message = htmlspecialchars($v->m_message);
	}
	
	
	/**
	 * Call this in the place where you want to put notifications visualization.
	 *
	 * @param &string $lastoutput The data not yet sent to the output buffer.
	 */
	function render(&$lastoutput)
	{
		global $g_xanth_ob_before_notifications;
		$g_xanth_ob_before_notifications = ob_get_clean();
		$g_xanth_ob_before_notifications .= $lastoutput;
		$lastoutput = '';
		ob_start();
	}
};



?>