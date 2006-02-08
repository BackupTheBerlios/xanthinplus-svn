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

function xanth_db_install_weight_category()
{
	//no deps
	return 0;
}

function xanth_db_install_category()
{
	//category
	xanth_db_query("
		CREATE TABLE category (
		id INT UNSIGNED NOT NULL AUTO_INCREMENT,
		title VARCHAR(255) NOT NULL,
		description TEXT NOT NULL,
		view_mode_id INT UNSIGNED NOT NULL,
		parent_id INT UNSIGNED,
		PRIMARY KEY (id),
		INDEX(parent_id),
		FOREIGN KEY(parent_id) REFERENCES category(id) ON DELETE CASCADE,
		FOREIGN KEY(view_mode_id) REFERENCES view_mode(id) ON DELETE RESTRICT
		)TYPE=InnoDB");
}


?>