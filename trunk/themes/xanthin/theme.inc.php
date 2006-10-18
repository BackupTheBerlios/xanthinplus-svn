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
	 * @see xDummyModule
	 */
	function getCss()
	{
		return "themes/xanthin/default.css";
	}
	
	
	/**
	 * @see xDummyModule
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
	 * @see xDummyModule
	 */
	function renderBoxGroup($group_name,$rendered_boxes)
	{
		$output = '';
		
		foreach($rendered_boxes as $box)
		{
			$output .= $box;
		}
		
		return $output;
	}
	
	/**
	 * @see xDummyModule
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
				<div id="header">
					<div id="logo">
					</div>
				</div>
				<div id="page">
					<div id="left-sidebar">'. $groups['left_group']->render() .'</div>
					<div id="contents">
					'; $output = xNotifications::render($output) . $content->render() . '
					</div>
				</div>
				<div align="center"> Queries ' . xDB::getDB()->queryGetCount() . ', Execution time ' . xExecutionTime::render() . ' secs</div>
				' . xLogEntry::renderFromScreen() . '
			</body>
		</html>';
		
		return $output;
	}
	
	/**
	 * @see xDummyModule
	 */
	function renderMenuItem($label,$link,$subitems)
	{
		return '<li><a href="' . $link . '">' . $label . '</a>' . $subitems . '</li>';
	}
	
	
	/**
	 * @see xDummyModule
	 */
	function renderMenuItems($items)
	{
		$output = '';
		if(!empty($items))
		{
			$output .= "<ul>\n";
			foreach($items as $item)
			{
				$output .= $item->render();
			}
			$output .= "</ul>\n";
		}
		
		return $output;
	}
	
	/**
	 * @see xDummyModule::renderItemPage()
	 */
	function renderItem($type,$title,$content)
	{
		$output = '<div class="item-title">' . $title . '</div>
		<div class="item-content">' . $content . '</div>';
		
		return $output;
	}
	
	
	/**
	 * @see xDummyModule::renderNotifications()
	 */
	function renderNotifications($notifications)
	{
		$output = '';
		foreach($notifications as $notification)
		{
			$output .= '<div class="notification"><div class="' . $notification['severity'] . '">' .
				$notification['message'] . '</div></div>';
		}
		
		return $output;
	}
};

xTheme::registerTheme(new xThemeXanthin());



?>
