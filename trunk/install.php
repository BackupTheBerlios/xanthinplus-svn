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


function weight_cmp($a,$b)
{
	if($a[0] < $b[0])
	{
		return -1;
	}
	
	if($a[0] > $b[0])
	{
		return 1;
	}
	
	return 0;
}

function xanth_install_db()
{
	//install core db
	require_once('./engine/install.inc.php');
	xanth_db_install_core();
	
	$weighted_components = array();
	foreach(xanth_component_list_existing() as $component)
	{
		include_once($component->path . '/install.inc.php');
		$weight_func = 'xanth_db_install_weight_' . $component->name;
		$weighted_components[] = array($weight_func(),$component);
	}
	
	usort($weighted_components,'weight_cmp');
	foreach($weighted_components as $component)
	{
		$inst_func = 'xanth_db_install_' . $component[1]->name;
		$inst_func();
	}
	
	
	$weighted_modules = array();
	foreach(xanth_module_list_existing() as $module)
	{
		include_once($module->path . '/install.inc.php');
		$weight_func = 'xanth_db_install_weight_' . $module->name;
		$weighted_modules[] = array($weight_func(),$module);
	}
	
	usort($weighted_modules,'weight_cmp');
	foreach($weighted_modules as $module)
	{
		$inst_func = 'xanth_db_install_' . $module[1]->name;
		$inst_func();
	}
}

xanth_db_connect(xanth_conf_get('db_host',''),xanth_conf_get('db_name',''),xanth_conf_get('db_user',''),xanth_conf_get('db_pass',''),xanth_conf_get('db_port',''));
xanth_install_db();

echo "xanthin+ successfully installed";

?>