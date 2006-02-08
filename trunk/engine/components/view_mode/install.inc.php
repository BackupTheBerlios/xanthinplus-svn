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

function xanth_db_install_weight_view_mode()
{
	//no dependencies
	return 0;
}

function xanth_db_install_view_mode()
{
	//visual element
	xanth_db_query("
		CREATE TABLE visual_element (
		name VARCHAR(32) NOT NULL,
		PRIMARY KEY (name)
		)TYPE=InnoDB");
	
	//display mode
	xanth_db_query("
		CREATE TABLE view_mode (
		id INT UNSIGNED AUTO_INCREMENT,
		name VARCHAR(32) NOT NULL,
		relative_visual_element VARCHAR(32) NOT NULL,
		default_for_element TINYINT UNSIGNED NOT NULL,
		display_procedure TEXT NOT NULL,
		PRIMARY KEY (id),
		UNIQUE(relative_visual_element,default_for_element),
		INDEX(relative_visual_element),
		FOREIGN KEY (relative_visual_element) REFERENCES visual_element(name) ON DELETE CASCADE
		)TYPE=InnoDB");
}


?>