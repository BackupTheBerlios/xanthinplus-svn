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
	function xTheme
	{
	}
	
	/**
	* Get current active theme.
	*
	* @return (xTheme)
	* @static
	*/
	function getCurrent()
	{
		
	}
	
	/**
	* Render the box element.
	* 
	* @param $box(xBoxElement) The element to render.
	* @return (string) the renderized element.
	*/
	function renderBox($box)
	{
	}
	
	/**
	* Should return an array of strings representing the names of the areas in the page.
	*
	* @return (array(string)) Area names
	*/
	function declareAreas()
	{
		return array('leftArea','centerArea');
	}
	
	/**
	* Render the whole page.
	* 
	* @param $page(xPage) The page element to render.
	* @return (string) the renderized element.
	*/
	function renderPage($page)
	{
		//first render areas for later use
		$left_area_out = '';
		$center_area_out = '';
		foreach($page->areas as $area)
		{
			switch($area->m_name)
			{
			case 'leftArea':
				$left_area_out .= $area->render();
				break;
				
			case 'centerArea':
				$center_area_out .= $area->render();
				break;
				
			default:
				//area not declared
				assert(FALSE);
			}
		}
		
		$output = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">'."\n";
		$output .= "<html>\n";
		$output .= "<head>\n";
		$output .= "<title>" . $page->m_title . "</title>". "\n";
		$output .= '<meta name="keywords" content="' . $page->m_keywords . '" />' . "\n";
		$output .= '<meta name="description" content="' . $page->m_description . '" />'. "\n";
		$output .= "<style type=\"text/css\" media=\"all\">@import \"themes/default_theme/style.css\";</style>" . "\n";
		$output .= "</head>";
		$output .= "<body>\n";
		$output .= '<table id="page-table"><tr>' . "\n";
		$output .= '<td id="left-sidebar">' . $left_area_out . '</td>';
		$output .= '<td id="content">'. $center_area_out .'</td>';
		$output .= "</tr></table>\n";
		$output .= " </body>\n";
		$output .= "</html>\n";
		
		return $output;
	}
	
	/**
	* Render the page element.
	* 
	* @param $page(xNode) The element to render.
	* @return (string) the renderized element.
	*/
	function renderNode($page)
	{
		
	}
	
	/**
	* Render the page element.
	* 
	* @param $page(xStatic) The element to render.
	* @return (string) the renderized element.
	*/
	function renderContent($page)
	{
		
	}
};


?>
