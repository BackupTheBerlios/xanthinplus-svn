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

function xanth_db_install_weight_page()
{
	//depende on view mode
	return 100;
}

function xanth_db_install_page()
{

	//install a new visual element
	$element = new xVisualElement('page');
	$element->insert();
	
	//...and the default view mode
	$proc = '
		$output = \'<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">\'."\n".
		 \'<html>\'."\n".
		 \'<head>\'."\n".
		 \'<title>\'.$page_title.\'</title>\'."\n".
		 \'<meta name="keywords" content="\'.$page_metadata[\'keywords\'].\'" />\' . "\n".
		 \'<meta name="description" content="\'.$page_metadata[\'description\'].\'" />\'. "\n".
		 \'<style type="text/css" media="all">@import "themes/default_theme/style.css";</style>\' . "\n".
		 \'</head>\'."\n".
		 \'<body>\'."\n".
		 \'<table id="page-table"><tr>\' . "\n".
		 \'<td id="left-sidebar">\'. $page_areas[\'sidebar left\'] . \'</td>\'.
		 \'<td id="content">\'. $page_areas[\'content\'] .\'</td>\'.
		 \'</tr></table>\'."\n".
		 \' </body>\'."\n".
		 \'</html>\'."\n";
		return $output;
	';
	
	$view = new xViewMode(0,'Default page view','page',TRUE,$proc);
	$view->insert();
}


?>