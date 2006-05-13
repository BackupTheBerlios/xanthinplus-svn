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
*/
class xTheme
{
	function xTheme()
	{
	}
	
	/**
	* Get current active theme.
	*
	* @return xTheme
	* @static
	*/
	function getActive()
	{
		return new xTheme();
	}
	
	/**
	* Render the box element.
	* 
	* @param string $id
	* @param string $title
	* @param string $content
	* @return string the renderized element.
	*/
	function renderBox($id,$title,$content)
	{
		$output = 
		'<div class="box"><div class="title">' . $title . '</div>
		<div>' . $content . '</div></div>
		'
		;
		return $output;
	}
	
	/**
	* Render the area element.
	* 
	* @param string $id
	* @param array(xBox) $boxes
	* @param xContent $content
	* @return string the renderized element.
	*/
	function renderArea($id,$boxes)
	{
		$output = '';
		
		switch($id)
		{
		case 'leftArea':
			foreach($boxes as $box)
			{
				$output .= $box->render();
			}
			break;
			
		default:
			//area not declared
			assert(FALSE);
		}
		
		return $output;
	}
	
	
	/**
	* Should return an array of strings representing the names of the areas in the page.
	*
	* @return array(string) Area names
	*/
	function declareAreas()
	{
		return array('leftArea');
	}
	
	/**
	* Render the whole page.
	* 
	* @param array(xArea) $areas
	* @return string the renderized element.
	*/
	function renderPage($content,$areas)
	{
		//first render areas for later use
		$left_area_out = '';
		foreach($areas as $area)
		{
			switch($area->m_name)
			{
			case 'leftArea':
				$left_area_out .= $area->render();
				break;
				
			default:
				//area not declared
				assert(FALSE);
			}
		}
		
		$output = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">'."\n";
		$output .= "<html>\n";
		$output .= "<head>\n";
		$output .= "<title>" . $content->m_title . "</title>". "\n";
		$output .= '<meta name="keywords" content="' . $content->m_keywords . '" />' . "\n";
		$output .= '<meta name="description" content="' . $content->m_description . '" />'. "\n";
		$output .= '<style type="text/css" media="all">@import "engine/cms/default.css";</style>' . "\n";
		$output .= "</head>";
		$output .= "<body>\n";
		$output .= '<table id="page-table"><tr>' . "\n";
		$output .= '<td id="left-sidebar">' . $left_area_out . '</td>';
		$output .= '<td id="content">' . $content->render() . '</td>';
		$output .= "</tr></table>\n";
		$output .= '<div align="center"> Queries ' . xDB::getDB()->queryGetCount() . ', Execution time ' . xExecutionTime::onRender() . ' secs</div>';
		$output .= xLogEntry::renderFromScreen();
		$output .= " </body>\n";
		$output .= "</html>\n";
		
		return $output;
	}
	
	/**
	 * Render a list of menu items
	 * 
	 * @param string $label
	 * @param string $link
	 * @param string subitems
	 * @return string the renderized element.
	 */
	function renderMenuItem($label,$link,$subitems)
	{
		return '<li><a href="' . $link . '">' . $label . '</a>' . $subitems . '</li>';
	}
	
	
	/**
	 * Render a list of menu items
	 * 
	 * @param array(xMenuItem)
	 * @return string the renderized element.
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
	
};


?>
