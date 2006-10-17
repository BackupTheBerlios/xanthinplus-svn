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
$g_xanth_notifications = array();


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
	
	
	function xNotificationEntry($severity,$message)
	{
		$this->m_severity = $severity;
		$this->m_message = $message;
	}

}

/**
* A static element that display all notifications emitted during ALL script execution. All
* means notifications emitted before and after that call of render() function.
*/
class xNotifications
{
	/**
	* Contructor
	*/
	function xNotifications()
	{
		$this->xElement();
	}
	
	/**
	 * Render this static element.
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
	
	/**
	 *
	 * @param int $severity One between:
	 * - NOTIFICATION_NOTICE
	 * - NOTIFICATION_WARNING
	 * - NOTIFICATION_ERROR
	 * @param string $message
	 */
	function add($severity,$message)
	{
		global $g_xanth_notifications;
		$g_xanth_notifications[] = new xNotificationEntry($severity,$message);
	}
	
	
	/**
	 * @private
	 */
	function _renderAll()
	{
		global $g_xanth_notifications;
		
		$notifications = array();
		foreach($g_xanth_notifications as $notification)
		{
			$notifications[] = array('severity' => $notification->m_severity,'message' => $notification->m_message);
		}
		
		return xTheme::render1('renderNotifications',$notifications);
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
		echo xNotifications::_renderAll();
		echo $g_xanth_ob_after_notifications;
	}
};


?>
