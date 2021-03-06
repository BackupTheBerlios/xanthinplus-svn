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

function xanth_db_install_weight_settings()
{
	//depends on role
	return 100;
}

function xanth_db_install_settings()
{
	//settings
	xanth_db_query("
		CREATE TABLE settings (
		site_name VARCHAR(256) NOT NULL,
		site_description VARCHAR(512) NOT NULL,
		site_keywords VARCHAR(128) NOT NULL,
		site_theme VARCHAR(32) NOT NULL
		)TYPE=InnoDB");
	
	xanth_db_query("INSERT INTO settings (site_name,site_description,site_keywords,site_theme) VALUES ('','','','')");
	
	$access = new xAccessRule('manage settings','Settings');
	$access->insert();
}


?>