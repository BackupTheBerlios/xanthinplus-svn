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
	//depends on view mode
	return 100;
}

function xanth_db_install_category()
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
		
	//category
	xanth_db_query("
		CREATE TABLE category (
		id INT UNSIGNED NOT NULL AUTO_INCREMENT,
		title VARCHAR(255) NOT NULL,
		description TEXT NOT NULL,
		view_mode_id INT UNSIGNED,
		parent_id INT UNSIGNED,
		PRIMARY KEY (id),
		UNIQUE(title),
		INDEX(parent_id),
		INDEX(view_mode_id),
		FOREIGN KEY(parent_id) REFERENCES category(id) ON DELETE CASCADE,
		FOREIGN KEY(view_mode_id) REFERENCES view_mode(id) ON DELETE SET NULL
		)TYPE=InnoDB");
		
	//category to entry type
	xanth_db_query("
		CREATE TABLE category_to_entry_type (
		cat_id INT UNSIGNED NOT NULL,
		entry_type VARCHAR(32) NOT NULL,
		UNIQUE(cat_id,entry_type),
		INDEX(cat_id),
		INDEX(entry_type),
		FOREIGN KEY(cat_id) REFERENCES category(id) ON DELETE CASCADE,
		FOREIGN KEY(entry_type) REFERENCES entry_type(name) ON DELETE CASCADE
		)TYPE=InnoDB");
}


?>