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

function xanth_db_install_weight_entry_type()
{
	//depends on role
	return 100;
}

function xanth_db_install_entry_type()
{
	//entry type
	xanth_db_query("
		CREATE TABLE entry_type (
		name VARCHAR(32) NOT NULL,
		view_mode_id INT UNSIGNED,
		PRIMARY KEY (name),
		INDEX(view_mode_id),
		FOREIGN KEY (view_mode_id) REFERENCES view_mode(id) ON DELETE SET NULL
		)TYPE=InnoDB");
		
	$access = new xAccessRule('manage entry type','Entry Type');
	$access->insert();

	//some default tipes
	$type = new xEntryType('StaticEntry');
	$type->insert();
}


?>