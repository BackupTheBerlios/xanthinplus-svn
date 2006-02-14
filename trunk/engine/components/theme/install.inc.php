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

function xanth_db_install_weight_theme()
{
	//depends on view mode
	return 100;
}

function xanth_db_install_theme()
{
	//theme
	xanth_db_query("
		CREATE TABLE theme (
		name VARCHAR(32) NOT NULL,
		PRIMARY KEY (name)
		)TYPE=InnoDB");
		
	//theme to elements
	xanth_db_query("
		CREATE TABLE theme_to_elements (
		theme_name VARCHAR(32) NOT NULL,
		visual_element VARCHAR(32) NOT NULL,
		view_mode INT UNSIGNED NOT NULL,
		UNIQUE (theme_name,visual_element),
		INDEX(theme_name),INDEX(visual_element),INDEX(view_mode),
		FOREIGN KEY (theme_name) REFERENCES theme(name) ON DELETE CASCADE,
		FOREIGN KEY (visual_element) REFERENCES visual_element(name) ON DELETE CASCADE,
		FOREIGN KEY (view_mode) REFERENCES view_mode(id) ON DELETE CASCADE
		)TYPE=InnoDB");
		
	//theme to elements
	xanth_db_query("
		CREATE TABLE theme_area (
		name VARCHAR(32) NOT NULL,
		PRIMARY KEY (name)
		)TYPE=InnoDB");
	
	//theme areas
	$area = new xThemeArea('sidebar left');
	$area->insert();
	$area = new xThemeArea('content');
	$area->insert();
	$area = new xThemeArea('footer');
	$area->insert();
}


?>