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

function xanth_db_install_weight_entry()
{
	//depend from content format module and category
	return 100;
}

function xanth_db_install_entry()
{

	//entry type
	xanth_db_query("
		CREATE TABLE entry_type (
		name VARCHAR(32) NOT NULL,
		display_mode VARCHAR(32) NOT NULL,
		PRIMARY KEY (name)
		)TYPE=InnoDB");
	
	
	//entry
	xanth_db_query("
		CREATE TABLE entry (
		id INT UNSIGNED NOT NULL AUTO_INCREMENT,
		title VARCHAR(256) NOT NULL,
		type VARCHAR(32) NOT NULL,
		author VARCHAR(64) NOT NULL,
		content TEXT NOT NULL,
		content_format VARCHAR(64) NOT NULL,
		published TINYINT NOT NULL,
		description VARCHAR(512) NOT NULL,
		keywords VARCHAR(128) NOT NULL,
		creation_time TIMESTAMP NOT NULL,
		PRIMARY KEY (id),
		INDEX(type),
		INDEX(content_format),
		FOREIGN KEY(content_format) REFERENCES content_format(name) ON DELETE RESTRICT
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
		
	
	//install a new visual element
	$element = new xVisualElement('entry');
	$element->insert();
	
	//...and the default view mode
	$proc = '
		return \'<div class="title">\'.$entry->title.\'</div><div class="body">\'.$entry->content.\'</div>\';
	';
	
	$view = new xViewMode(0,'Default entry view','entry',TRUE,$proc);
	$view->insert();
}


?>