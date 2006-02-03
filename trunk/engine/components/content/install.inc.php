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

function xanth_db_install_weight_content()
{
	//depend from content format module
	return 100;
}

function xanth_db_install_content()
{
	//entry type
	xanth_db_query("
		CREATE TABLE entryType (
		name VARCHAR(32) NOT NULL,
		PRIMARY KEY (name)
		)TYPE=InnoDB");
	
	//entry
	xanth_db_query("
		CREATE TABLE entry (
		id INT UNSIGNED NOT NULL AUTO_INCREMENT,
		title VARCHAR(256) NOT NULL,
		type VARCHAR(64) NOT NULL,
		author VARCHAR(64) NOT NULL,
		content TEXT NOT NULL,
		content_format VARCHAR(64) NOT NULL,
		creation_time TIMESTAMP NOT NULL,
		PRIMARY KEY  (id),
		INDEX(type),
		INDEX(content_format),
		FOREIGN KEY(content_format) REFERENCES content_format(name) ON DELETE RESTRICT
		)TYPE=InnoDB");
		
		//FOREIGN KEY(type) REFERENCES entryType(name) ON DELETE RESTRICT
}


?>