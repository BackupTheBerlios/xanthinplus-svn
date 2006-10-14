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
* @todo
*/
function xanth_include_modules()
{
}

/**
*
*/
function xanth_main()
{
	xExecutionTime::executionStarted();
	
	ob_start();
	
	//select DB
	if(xConf::get('db_type','mysql') == 'mysql')
	{
		$db = new xDBMysql();
		$db->connect(xConf::get('db_host',''),xConf::get('db_name',''),xConf::get('db_user',''),xConf::get('db_pass',''),xConf::get('db_port',''));
		xDB::setDB($db);
	}
	else
	{
		exit('Unknown database type');
	}
	xDB::getDB()->queryResetCount();
	
	//error handler
	set_error_handler('xanth_php_error_handler');
	
	xanth_fix_gpc_magic();
	
	//session
	session_set_save_handler("on_session_start","on_session_end","on_session_read","on_session_write","on_session_destroy","on_session_gc");
	session_start();
	
	
	
	//extract current path
	$path = xPath::getCurrent();
	
	//start execution	
	$page = xPage::fetchPage($path);
	xTheme::load(xSettings::get('site_theme',''));
	echo $page->render();
	
	session_write_close();
	
	xNotifications::postProcessing();
	
	ob_end_flush();
}

?>
