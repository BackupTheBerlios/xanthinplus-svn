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

function xanth_db_install_weight_user()
{
	//depend from role module
	//depend from box module
	
	return 200;
}

function xanth_db_install_user()
{
	//Users
	xanth_db_query("
		CREATE TABLE user (
		id INT UNSIGNED AUTO_INCREMENT NOT NULL,
		username VARCHAR(32) NOT NULL,
		password VARCHAR(64) NOT NULL,
		email VARCHAR(128) NOT NULL,
		cookie_token VARCHAR(64) NOT NULL,
		PRIMARY KEY (id),
		UNIQUE(username),
		INDEX(username),
		UNIQUE(email)
		)TYPE=InnoDB");
		
	//User to role
	xanth_db_query("
		CREATE TABLE user_to_role (
		userid INT UNSIGNED NOT NULL,
		roleName VARCHAR(32) NOT NULL,
		UNIQUE(userid,roleName),
		INDEX(userid),
		INDEX(roleName),
		FOREIGN KEY (userid) REFERENCES user(id) ON DELETE CASCADE,
		FOREIGN KEY (roleName) REFERENCES role(name) ON DELETE CASCADE
		)TYPE=InnoDB");
		
	$user = new xUser('','admin','root@localhost.com');
	$user->insert('pass');
	$user->add_in_role('administrator');
	
	//create a box for login
	$login_box = new xBox('login_box','Login',NULL,'Full Html',0);
	$login_box->insert();
	$login_box->assign_to_area('sidebar left');
}


?>