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

require_once('conf.inc.php');
require_once('engine/globals.inc.php');
require_once('engine/base.inc.php');
require_once('engine/db.inc.php');
require_once('engine/log.inc.php');
require_once('engine/event.inc.php');
require_once('engine/module.inc.php');
require_once('engine/theme.inc.php');
require_once('engine/session.inc.php');
require_once('engine/core.inc.php');
require_once('engine/box.inc.php');
require_once('engine/entry.inc.php');
require_once('engine/element.inc.php');

/**
* @defgroup Core Core
*/

/**
* Init function
* @ingroup Core
*/
function xanth_init()
{
	set_error_handler('xanth_php_error_handler');
	xanth_db_connect(xanth_conf_get('db_host',''),xanth_conf_get('db_name',''),xanth_conf_get('db_user',''),xanth_conf_get('db_pass',''),xanth_conf_get('db_port',''));
	session_set_save_handler("on_session_start","on_session_end","on_session_read","on_session_write","on_session_destroy","on_session_gc");
	session_start();
	xanth_init_modules();
	xanth_init_theme(xanth_get_default_theme());
	
	xanth_broadcast_event(EVT_CORE_CREATE_PAGE,'core');
	
	//print log
	foreach(xanth_get_screen_log() as $entry)
	{
		echo '<br />' . $entry->level . ' ' . $entry->component . ' ' . $entry->message . ' ' . $entry->filename . '@' . $entry->line;
	}
}


?>