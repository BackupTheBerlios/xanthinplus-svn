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

require_once(dirname(__FILE__) . '/base.inc.php');
require_once(dirname(__FILE__) . '/bbcode.inc.php');
require_once(dirname(__FILE__) . '/error.inc.php');
require_once(dirname(__FILE__) . '/widget.inc.php');
require_once(dirname(__FILE__) . '/contentfilter.inc.php');
require_once(dirname(__FILE__) . '/install.inc.php');
require_once(dirname(__FILE__) . '/log.inc.php');
require_once(dirname(__FILE__) . '/language.inc.php');
require_once(dirname(__FILE__) . '/module.inc.php');
require_once(dirname(__FILE__) . '/operation.inc.php');
require_once(dirname(__FILE__) . '/framework.dao.php');
require_once(dirname(__FILE__) . '/path.inc.php');
require_once(dirname(__FILE__) . '/session.inc.php');
require_once(dirname(__FILE__) . '/headermanager.inc.php');
require_once(dirname(__FILE__) . '/notifications.inc.php');
require_once(dirname(__FILE__) . '/uniqueid.inc.php');
require_once(dirname(__FILE__) . '/utf8.inc.php');
require_once(dirname(__FILE__) . '/utilities.inc.php');

require_once(dirname(__FILE__) . '/dbaccess/db.inc.php');
require_once(dirname(__FILE__) . '/dbaccess/mysql_db.inc.php');


class xXanthin
{
	/**
	 * 
	 */
	function initDatabase()
	{
		//select DB
		if(xConf::get('db_type','mysql') == 'mysql')
		{
			$db = new xDBMysql();
			$db->connect(xConf::get('db_host',''),xConf::get('db_user',''),xConf::get('db_pass',''),xConf::get('db_port',''));
			$db->selectDB(xConf::get('db_name',''));
			xDB::setDB($db);
		}
		else
		{
			exit('Unknown database type');
		}
	}
	
	/**
	 * 
	 */
	function initUtilities()
	{
		xanth_fix_gpc_magic();
		
		//error handler
		set_error_handler('xanth_php_error_handler');
		
		xModuleManager::invokeAll('xm_initUtilities',array());
	}
	
	/**
	 * 
	 */
	function initSession()
	{
		//session
		session_set_save_handler("on_session_start","on_session_end","on_session_read","on_session_write","on_session_destroy","on_session_gc");
		session_start();
	}
	
	
	/**
	 * 
	 */
	function initModules()
	{
		xModuleManager::initModules(true,true);
		xModuleManager::invokeAll('xm_initModules',array());
	}
	
	
	/**
	 * 
	 */
	function finalDatabase()
	{
	}
	
	
	/**
	 * 
	 */
	function finalUtilities()
	{
		xNotificationsManager::postProcessing();
		xModuleManager::invokeAll('xm_finalUtilities',array());
	}
	
	/**
	 * 
	 */
	function finalSession()
	{
		session_write_close();
	}
	
	/**
	 * 
	 */
	function finalModules()
	{
		xModuleManager::invokeAll('xm_finalModules',array());
	}
	
	
	/**
	 * Entry point for xanthin application framework
	 */
	function main()
	{
		ob_start();
	
		xXanthin::initDatabase();
		xXanthin::initSession();
		xXanthin::initUtilities();
		xXanthin::initModules();
		
		// Setting the Content-Type header with charset
		header('Content-Type: text/html; charset=utf-8');
		
		$path = xPath::getCurrent();
		xModuleManager::invoke('xm_createPage',array(&$path));
		
		xXanthin::finalModules();
		xXanthin::finalUtilities();
		xXanthin::finalSession();
		xXanthin::finalDatabase();
		
		ob_end_flush;
	}
	
}



//###########################################################################
//###########################################################################
//###########################################################################


/**
 * Module to manage basic fucntionalities.
 * <br><strong>Weight = -900</strong>.
 */
 
/*
class xModuleXanthin extends xModule
{
	function xModuleXanthin()
	{
		$this->xModule(-900);
	}
}
xModuleManager::registerModule(new xModuleXanthin());
*/

?>
