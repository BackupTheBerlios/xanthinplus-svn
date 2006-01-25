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


require_once('./engine/xanthin.inc.php');


function xanth_install_db()
{
	//install core db
	require_once('./engine/install.inc.php');
	xanth_db_install_core();
	
	foreach(xanth_list_existing_modules() as $module)
	{
		include_once($module->path . '/install.inc.php');
		$inst_func = 'xanth_install_db_' . $module->name;
		$inst_func();
	}
}

xanth_db_connect(xanth_conf_get('db_host',''),xanth_conf_get('db_name',''),xanth_conf_get('db_user',''),xanth_conf_get('db_pass',''),xanth_conf_get('db_port',''));
xanth_install_db();

echo "xanthin+ successfully installed";

?>