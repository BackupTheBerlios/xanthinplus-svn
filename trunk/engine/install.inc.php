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
* @file Installation procedures for core
*/

function xanth_db_install_core()
{
	//log
	xanth_db_query("
		CREATE TABLE xanth_log (
		level MEDIUMINT NOT NULL,
		component VARCHAR(32) NOT NULL,
		message TEXT NOT NULL,
		filename  VARCHAR(255) NOT NULL,
		line MEDIUMINT NOT NULL,
		timestamp TIMESTAMP
		)");
		
	//sessions
	xanth_db_query("
		CREATE TABLE sessions (
		session_id VARCHAR(32) NOT NULL,
		session_data TEXT NOT NULL,
		session_timestamp TIMESTAMP NOT NULL,
		PRIMARY KEY  (session_id)
		)");
		
	//Modules
	xanth_db_query("
		CREATE TABLE modules (
		name VARCHAR(32) NOT NULL,
		path VARCHAR(255) NOT NULL,
		enabled TINYINT NOT NULL,
		PRIMARY KEY  (name)
		)");
		
	
	//content format
	xanth_db_query("
		CREATE TABLE content_format (
		name VARCHAR(64) PRIMARY KEY NOT NULL,
		stripped_html TINYINT NOT NULL,
		php_source TINYINT NOT NULL,
		new_line_to_line_break TINYINT NOT NULL
		)");
	
	//box
	xanth_db_query("
		CREATE TABLE box (
		boxName VARCHAR(64) NOT NULL,
		title VARCHAR(255),
		content TEXT,
		content_format_name VARCHAR(64) NOT NULL,
		is_user_defined TINYINT NOT NULL,
		PRIMARY KEY(boxName),
		FOREIGN KEY(content_format_name) REFERENCES content_format(name),
		INDEX(content_format_name)
		)");
		
	//create builtint box
	//xanth_create_box(new xanthBox(''));
	
	
	//box to area mapping
	xanth_db_query("
		CREATE TABLE boxToArea (
		boxName VARCHAR(64) NOT NULL,
		area VARCHAR(255) NOT NULL,
		UNIQUE (boxName,area),
		FOREIGN KEY(boxName) REFERENCES box(boxName),
		INDEX(boxName)
		)");
	
	//themes
	xanth_db_query("
		CREATE TABLE themes (
		name VARCHAR(32) NOT NULL,
		path VARCHAR(255) NOT NULL,
		is_default TINYINT NOT NULL,
		PRIMARY KEY  (name)
		)");
	
	xanth_theme_set_default(new xanthTheme('./themes/','default_theme'));
}




?>