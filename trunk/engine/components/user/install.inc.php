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
	
	return 100;
}

function xanth_db_install_user()
{
	//Users
	xanth_db_query("
		CREATE TABLE user (
		username VARCHAR(32) NOT NULL,
		password VARCHAR(64) NOT NULL,
		email VARCHAR(128) NOT NULL,
		cookie_token VARCHAR(64),
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
		FOREIGN KEY (roleId) REFERENCES role(id) ON DELETE CASCADE
		)TYPE=InnoDB");
}


?>