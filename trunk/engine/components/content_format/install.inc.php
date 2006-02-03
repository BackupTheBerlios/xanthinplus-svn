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

function xanth_db_install_weight_content_format()
{
	//no dependencies
	return 0;
}

function xanth_db_install_content_format()
{
	//content format
	xanth_db_query("
		CREATE TABLE content_format (
		name VARCHAR(64) NOT NULL,
		description VARCHAR(256) NOT NULL,
		PRIMARY KEY(name)
		)TYPE=InnoDB");
		
		
	$cf = new xContentFormat('Php source','Php scripts are allowed and executed.');
	$cf->insert();
	
	$cf = new xContentFormat('Full Html','All html tags are allowed.');
	$cf->insert();
	
	$cf = new xContentFormat('BBCode','Enable the use of a slightly modified version of BBCode tags. 
		Also converts all special html chars in html entities and line breaks in br');
	$cf->insert();
	
	$cf = new xContentFormat('Filtered text','Converts all special html chars in html entities and line breaks in br.');
	$cf->insert();
}


?>