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
require_once(dirname(__FILE__) . '/error.inc.php');
require_once(dirname(__FILE__) . '/component.inc.php');
require_once(dirname(__FILE__) . '/log.inc.php');
require_once(dirname(__FILE__) . '/module.inc.php');
require_once(dirname(__FILE__) . '/framework.dao.php');
require_once(dirname(__FILE__) . '/path.inc.php');
require_once(dirname(__FILE__) . '/session.inc.php');
require_once(dirname(__FILE__) . '/headermanager.inc.php');
require_once(dirname(__FILE__) . '/utf8.inc.php');
require_once(dirname(__FILE__) . '/utilities.inc.php');
require_once(dirname(__FILE__) . '/template.inc.php');
require_once(dirname(__FILE__) . '/framework.comp.php');

require_once(dirname(__FILE__) . '/dbaccess/db.inc.php');
require_once(dirname(__FILE__) . '/dbaccess/mysql_db.inc.php');
require_once(dirname(__FILE__) . '/dbaccess/daomanager.inc.php');


/**
 * 
 */
class xApplication
{
	/**
	 * 
	 */
	var $m_module_manager = NULL;
	
	/**
	 * 
	 */
	var $m_theme_manager = NULL;
	
	
	/**
	 * 
	 */
	var $m_dao_manager = NULL;
	
	/**
	 * 
	 */
	function &getModuleManager()
	{
		return $this->m_module_manager;
	}
	
	/**
	 * 
	 */
	function &getDAOManager()
	{
		return $this->m_dao_manager;
	}
	
	/**
	 * 
	 */
	function &getThemeManager()
	{
		return $this->m_theme_manager;
	}
	
	
	/**
	 * 
	 */
	function &getInstance()
	{
		global $g_xanthin;
		if(!isset($g_xanthin))
			$g_xanthin = new xApplication();
			
		return $g_xanthin;
	}
	
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
	function initSession()
	{
		//session
		session_set_save_handler("on_session_start","on_session_end","on_session_read","on_session_write","on_session_destroy","on_session_gc");
		session_start();
	}
	
	
	/**
	 * 
	 * @todo Migliorare le prestazioni per il fetch dei componenti
	 */
	function initModules()
	{
		$this->m_module_manager = new xModuleManager();
		$this->m_module_manager->initModules('engine','comp',false,false,array(new xFrameworkComponent()));
		
		if(xConf::get('db_type','mysql') == 'mysql')
			$this->m_dao_manager = new xDAOManager(xConf::get('db_type','mysql'));
			
		$comp = new xModuleManager();
		$comp->initModules('extensions','ext',true,true);
		
		$this->m_module_manager->merge($comp);
		$this->m_module_manager->invokeAll('xm_initModules',array());
	}
	
	
	/**
	 * 
	 */
	function initUtilities()
	{
		
		
		$this->m_module_manager->invokeAll('xm_initUtilities',array());
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
	function finalSession()
	{
		session_write_close();
	}
	
	/**
	 * 
	 */
	function finalModules()
	{
		$this->m_module_manager->invokeAll('xm_finalModules',array());
	}
	
	
	/**
	 * Entry point for xanthin application framework
	 */
	function main()
	{
		xTimer::start('script_execution_time');
		
		ob_start();
	
		$this->initDatabase();
		$this->initSession();
		$this->initModules();
		
		xanth_fix_gpc_magic();
		//error handler
		set_error_handler('xanth_php_error_handler');
		
		// Setting the Content-Type header with charset
		header('Content-Type: text/html; charset=utf-8');
		
		$path = xPath::getCurrent();
		$this->m_module_manager->invoke('xm_createPage',array(&$path));
		
		$this->finalModules();
		$this->finalSession();
		$this->finalDatabase();
		
		ob_end_flush();
		
		if(xConf::get('debug',false))
		{
			$db =& xDB::getDB(); 
			echo '<br><br><br>Execution Time: ' . xTimer::stop('script_execution_time').' Queries: '. var_export($db->dumpGet(),true);
			xLogEntry::renderFromScreen();
		}
	}
	
	
	
	/**
	 *
	 */
	function install()
	{
		ob_start();
		//select DB
		if(xConf::get('db_type','mysql') == 'mysql')
		{
			$db = new xDBMysql();
			$db->connect(xConf::get('db_host',''),xConf::get('db_user',''),xConf::get('db_pass',''),xConf::get('db_port',''));
			xDB::setDB($db);
			
			$name = xConf::get('db_name','');
			$db->query("DROP DATABASE $name");
			$db->query("CREATE DATABASE $name");
			
			$db->selectDB($name);
		}
		else
		{
			exit('Unknown database type');
		}
		
		//error handler
		set_error_handler('xanth_php_error_handler');
		
		
		$comp = new xModuleManager();
		$comp->initModules('engine','comp',false,false,array(new xFrameworkComponent()));
		$comp->invokeAll('xm_install',array($name = xConf::get('db_name','')));
		
		$comp = new xModuleManager();
		$comp->initModules('extensions','ext',true,true);
		$comp->invokeAll('xm_install',array($name = xConf::get('db_name','')));
		
		//print log
		echo xLogEntry::renderFromScreen();
		
		echo "Xanthin Successfully installed";
		ob_end_flush();
	}
}


/**
 * Wrapper
 */
function &x_getDAO($name)
{
	$app =& xApplication::getInstance();
	$dao_m =& $app->getDAOManager();
	return $dao_m->getDAO($name);
}


/**
 * Wrapper
 */
function &x_getModuleManager()
{
	$app =& xApplication::getInstance();
	return $app->getModuleManager();
}


/**
 * Wrapper
 */
function &x_geThemeManager()
{
	$app =& xApplication::getInstance();
	return $app->getThemeManager();
}

?>