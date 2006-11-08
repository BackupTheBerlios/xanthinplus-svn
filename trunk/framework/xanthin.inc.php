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

require_once($_SERVER['DOCUMENT_ROOT'] . $g_xanth_conf['installation_path'] . '/framework/base.inc.php');
require_once($xanth_working_dir . '/framework/bbcode.inc.php');
require_once($xanth_working_dir . '/framework/error.inc.php');
require_once($xanth_working_dir . '/framework/widget.inc.php');
require_once($xanth_working_dir . '/framework/contentfilter.inc.php');
require_once($xanth_working_dir . '/framework/install.inc.php');
require_once($xanth_working_dir . '/framework/log.inc.php');
require_once($xanth_working_dir . '/framework/language.inc.php');
require_once($xanth_working_dir . '/framework/module.inc.php');
require_once($xanth_working_dir . '/framework/operation.inc.php');
require_once($xanth_working_dir . '/framework/framework.dao.php');
require_once($xanth_working_dir . '/framework/path.inc.php');
require_once($xanth_working_dir . '/framework/session.inc.php');
require_once($xanth_working_dir . '/framework/theme.inc.php');
require_once($xanth_working_dir . '/framework/uniqueid.inc.php');
require_once($xanth_working_dir . '/framework/utf8.inc.php');
require_once($xanth_working_dir . '/framework/utilities.inc.php');

require_once($xanth_working_dir . '/framework/dbaccess/db.inc.php');
require_once($xanth_working_dir . '/framework/dbaccess/mysql_db.inc.php');


/**
 *
 */
function xanth_main()
{
	ob_start();

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
	
	// Setting the Content-Type header with charset
	header('Content-Type: text/html; charset=utf-8');

	//error handler
	set_error_handler('xanth_php_error_handler');
	
	xanth_fix_gpc_magic();
	
	//session
	session_set_save_handler("on_session_start","on_session_end","on_session_read","on_session_write","on_session_destroy","on_session_gc");
	session_start();
	
	//broadcast onPageCreation event
	//todo check for errors
	xModuleManager::invokeAll('xm_onInit',array());
			
	//extract current path
	$path = xPath::getCurrent();
	
	
	session_write_close();
	$content = ob_get_clean();
	echo $content;
	
	/*
	$handle = fopen('out', 'a');
	fwrite($handle, $content);
    fclose($handle);
    */
}

?>
