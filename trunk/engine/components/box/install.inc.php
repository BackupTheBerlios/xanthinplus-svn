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

function xanth_db_install_weight_box()
{
	//depends from content_format module
	return 100;
}

function xanth_db_install_box()
{
	//box
	xanth_db_query("
		CREATE TABLE box (
		name VARCHAR(64) NOT NULL,
		title VARCHAR(255),
		content TEXT,
		content_format VARCHAR(64) NOT NULL,
		is_user_defined TINYINT NOT NULL,
		PRIMARY KEY(name),
		INDEX(content_format),
		FOREIGN KEY(content_format) REFERENCES content_format(name)
		)TYPE=InnoDB");
		
	//create builtint box
	//xanth_create_box(new xBox(''));
	
	
	//box to area mapping
	xanth_db_query("
		CREATE TABLE boxtoarea (
		boxName VARCHAR(64) NOT NULL,
		area VARCHAR(255) NOT NULL,
		UNIQUE (boxId,area),
		INDEX(boxId),
		FOREIGN KEY(boxName) REFERENCES box(name) ON DELETE CASCADE
		)TYPE=InnoDB");
		
}


?>