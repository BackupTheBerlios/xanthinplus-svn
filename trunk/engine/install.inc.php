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
		)TYPE=InnoDB");
		
	//sessions
	xanth_db_query("
		CREATE TABLE sessions (
		session_id VARCHAR(32) NOT NULL,
		session_data TEXT NOT NULL,
		session_timestamp TIMESTAMP NOT NULL,
		PRIMARY KEY  (session_id)
		)TYPE=InnoDB");
	
	//Roles
	xanth_db_query("
		CREATE TABLE role (
		id INT UNSIGNED NOT NULL,
		name VARCHAR(32) NOT NULL,
		description VARCHAR(255) NOT NULL,
		PRIMARY KEY(id)
		)TYPE=InnoDB");
	$role = new xRole('Administrator','Administrator');$role->insert();
	$role = new xRole('Authenticated','Authenticated user');$role->insert();
	$role = new xRole('Anonymous','Anonymous visitor');$role->insert();
	
	//Access rules
	xanth_db_query("
		CREATE TABLE role_access_rule (
		roleId INT UNSIGNED NOT NULL,
		access_rule VARCHAR(64) NOT NULL,
		UNIQUE(roleId,access_rule),
		INDEX(roleId),
		FOREIGN KEY (roleId) REFERENCES role(id) ON DELETE CASCADE
		)TYPE=InnoDB");
		
	//Users
	xanth_db_query("
		CREATE TABLE user (
		username VARCHAR(32) NOT NULL,
		password VARCHAR(32) NOT NULL,
		email VARCHAR(128) NOT NULL,
		PRIMARY KEY (username),
		UNIQUE(email)
		)TYPE=InnoDB");
		
	//User to role
	xanth_db_query("
		CREATE TABLE user_to_role (
		username VARCHAR(32) NOT NULL,
		roleId INT UNSIGNED NOT NULL,
		UNIQUE(username,roleId),
		INDEX(username),
		INDEX(roleId),
		FOREIGN KEY (username) REFERENCES user(username) ON DELETE CASCADE,
		FOREIGN KEY (roleId) REFERENCES role(id) ON DELETE CASCADE,
		)TYPE=InnoDB");
		
	//Modules
	xanth_db_query("
		CREATE TABLE modules (
		name VARCHAR(32) NOT NULL,
		path VARCHAR(255) NOT NULL,
		enabled TINYINT NOT NULL,
		PRIMARY KEY  (name)
		)TYPE=InnoDB");
		
	
	//content format
	xanth_db_query("
		CREATE TABLE content_format (
		name VARCHAR(64) PRIMARY KEY NOT NULL,
		stripped_html TINYINT NOT NULL,
		php_source TINYINT NOT NULL,
		new_line_to_line_break TINYINT NOT NULL
		)TYPE=InnoDB");
	
	
	//box
	xanth_db_query("
		CREATE TABLE box (
		id INT UNSIGNED AUTO_INCREMENT NOT NULL,
		title VARCHAR(255),
		content TEXT,
		content_format VARCHAR(64) NOT NULL,
		is_user_defined TINYINT NOT NULL,
		PRIMARY KEY(id),
		FOREIGN KEY(content_format) REFERENCES content_format(name),
		INDEX(content_format)
		)TYPE=InnoDB");
		
	//create builtint box
	//xanth_create_box(new xBox(''));
	
	
	//box to area mapping
	xanth_db_query("
		CREATE TABLE boxtoarea (
		boxId INT UNSIGNED NOT NULL,
		area VARCHAR(255) NOT NULL,
		UNIQUE (boxId,area),
		FOREIGN KEY(boxId) REFERENCES box(id) ON DELETE CASCADE,
		INDEX(boxId)
		)TYPE=InnoDB");
	
	//themes
	xanth_db_query("
		CREATE TABLE themes (
		name VARCHAR(32) NOT NULL,
		path VARCHAR(255) NOT NULL,
		is_default TINYINT NOT NULL,
		PRIMARY KEY  (name)
		)TYPE=InnoDB");
	$theme = new xTheme('./themes/','default_theme');
	$theme->set_default();
	
	//category
	xanth_db_query("
		CREATE TABLE category (
		id INT UNSIGNED NOT NULL AUTO_INCREMENT,
		title VARCHAR(255) NOT NULL,
		parent INT UNSIGNED,
		PRIMARY KEY (id),
		INDEX(parent),
		FOREIGN KEY(parent) REFERENCES category(id) ON DELETE CASCADE
		)TYPE=InnoDB");
	
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
		title VARCHAR(255) NOT NULL,
		type VARCHAR(64) NOT NULL,
		author VARCHAR(64) NOT NULL,
		content TEXT NOT NULL,
		content_format VARCHAR(64) NOT NULL,
		creation_time TIMESTAMP NOT NULL,
		PRIMARY KEY  (id),
		INDEX(type),
		FOREIGN KEY(type) REFERENCES entryType(name) ON DELETE RESTRICT
		)TYPE=InnoDB");
		
	xanth_db_query("
		CREATE TABLE categorytoentry (
		entryId INT UNSIGNED NOT NULL,
		catId INT UNSIGNED NOT NULL,
		UNIQUE(entryId,catId),
		INDEX(entryId),
		INDEX(catId),
		FOREIGN KEY(entryId) REFERENCES entry(id) ON DELETE CASCADE,
		FOREIGN KEY(catId) REFERENCES category(id) ON DELETE CASCADE
		)TYPE=InnoDB");
}




?>