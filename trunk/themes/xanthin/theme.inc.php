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


/**
* An object that contains methods for render xElement objects.
* @see xDummyTheme For all implementable methods
*/
class xThemeXanthin extends xTheme
{
	function xThemeXanthin()
	{
	}
	
	/**
	 * @see xDefaultModule
	 */
	function getCss()
	{
		return "themes/xanthin/default.css";
	}
	
	/**
	 * @see xDefaultModule
	 */
	function renderBox($name,$title,$content)
	{
		$output = 
		'<div class="box">
			<div class="title">' . $title . '</div>
			<div class="content">' . $content . '</div>
		</div>
		
		'
		;
		return $output;
	}
	
	
	/**
	 * @see xDefaultModule
	 */
	function renderPage($content,$groups)
	{
		$output = 
		'<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
		<html>
			<head>
				<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
				<title>' . $content->m_title . '</title>
				<meta name="keywords" content="' . $content->m_meta_keywords . '" />
				<meta name="description" content="' . $content->m_meta_description . '" />
				'.xTheme::renderAllCss().'
			</head>
			<body>
				<div id="page">
					<div id="header">
						<div id="logo">
						<img src="themes/xanthin/images/logo.png"/>
						</div>
					</div>
					<div id="links">
						Link1 | Link2
					</div>
					<div id="middle">
						<div id="left-sidebar">'. $groups['left_group']->render() .'</div>
						
						<div id="contents">
						<div id="contents-header">&nbsp;</div>
						<div id="contents-center">
						'; $output = xNotifications::render($output) . $content->render() . '
						</div>
						<div id="contents-footer">&nbsp;</div>
						</div>
						<div class="cleaner">&nbsp;</div>
					</div>
					<div id="footer"> Queries ' . xDB::getDB()->queryGetCount() . ', Execution time ' . xExecutionTime::render() . ' secs</div>
					' . xLogEntry::renderFromScreen() . '
				</div>
			</body>
		</html>';
		
		return $output;
	}
};

xTheme::registerTheme(new xThemeXanthin());



?>
