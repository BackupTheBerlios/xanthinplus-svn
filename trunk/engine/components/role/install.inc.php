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

function xanth_db_install_weight_role()
{
	//no dependencies
	return 0;
}

function xanth_db_install_role()
{

	//Roles
	xanth_db_query("
		CREATE TABLE role (
		name VARCHAR(32) NOT NULL,
		description VARCHAR(255) NOT NULL,
		PRIMARY KEY(name)
		)TYPE=InnoDB");
	$role = new xRole('administrator','Administrator');$role->insert();
	$role = new xRole('authenticated','Authenticated user');$role->insert();
	$role = new xRole('anonymous','Anonymous visitor');$role->insert();
	
	//Access rules
	xanth_db_query("
		CREATE TABLE role_access_rule (
		roleName VARCHAR(32) NOT NULL,
		access_rule VARCHAR(64) NOT NULL,
		UNIQUE(roleId,access_rule),
		INDEX(roleId),
		FOREIGN KEY (roleName) REFERENCES role(name) ON DELETE CASCADE
		)TYPE=InnoDB");
}


?>