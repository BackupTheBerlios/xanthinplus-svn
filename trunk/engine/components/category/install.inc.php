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
	//depends on content module
	return 200;
}

function xanth_db_install_category()
{
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
		
	//category to entry
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