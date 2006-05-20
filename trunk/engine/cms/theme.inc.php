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


$g_xanth_builtin_themes = array();
$g_xanth_themes = array();

/**
* An object that contains methods for render xElement objects.
* @see xDummyTheme For all implementable methods
*/
class xTheme
{
	function xTheme()
	{
	}
	
	//----------------STATIC FUNCTIONS----------------------------------------------
	//----------------STATIC FUNCTIONS----------------------------------------------
	//----------------STATIC FUNCTIONS----------------------------------------------
	
	/**
	* Register a theme.
	*
	* @param xTheme $theme The module to register.
	* @internal
	* @static
	*/
	function registerDefaultTheme($theme)
	{
		global $g_xanth_builtin_themes;
		$g_xanth_builtin_themes[] = $theme;
	}
	
	
	/**
	* Retrieve all registered themes as an array.
	*
	* @return array(xTheme)
	* @internal
	* @static
	*/
	function getDefaultThemes()
	{
		global $g_xanth_builtin_themes;
		return $g_xanth_builtin_themes;
	}
	
	/**
	* Register a theme.
	*
	* @param xTheme $theme
	* @static
	*/
	function registerTheme($theme)
	{
		global $g_xanth_themes;
		$g_xanth_themes[] = $theme;
	}
	
	
	/**
	* Retrieve all registered themes as an array.
	*
	* @return array(xTheme)
	* @static
	*/
	function getThemes()
	{
		global $g_xanth_themes;
		return $g_xanth_themes;
	}
	
	/**
	 * Make a method call to all themes and return the first result !== NULL (0 argument version).
	 *
	 * @param string $function
	 * @return string The renderung output
	 */
	function render0($function)
	{
		//first to user modules then default
		$all_themes = array(xTheme::getThemes(),xTheme::getDefaultThemes());
		
		foreach($all_themes as $themes)
		{
			foreach($themes as $theme)
			{
				if(method_exists($theme,$function))
				{
					$result = $theme->$function();
					if($result !== NULL)
					{
						return $result;
					}
				}
			}
		}
		
		return NULL;
	}
	
	
	/**
	 * Make a method call to all themes and return the first result !== NULL (1 argument version).
	 *
	 * @param string $function
	 * @return string The renderung output
	 */
	function render1($function,&$arg1)
	{
		//first to user modules then default
		$all_themes = array(xTheme::getThemes(),xTheme::getDefaultThemes());
		
		foreach($all_themes as $themes)
		{
			foreach($themes as $theme)
			{
				if(method_exists($theme,$function))
				{
					$result = $theme->$function($arg1);
					if($result !== NULL)
					{
						return $result;
					}
				}
			}
		}
		
		return NULL;
	}
	
	/**
	 * Make a method call to all themes and return the first result !== NULL (2 argument version).
	 *
	 * @param string $function
	 * @return string The renderung output
	 */
	function render2($function,&$arg1,&$arg2)
	{
		//first to user modules then default
		$all_themes = array(xTheme::getThemes(),xTheme::getDefaultThemes());
		
		foreach($all_themes as $themes)
		{
			foreach($themes as $theme)
			{
				if(method_exists($theme,$function))
				{
					$result = $theme->$function($arg1,$arg2);
					if($result !== NULL)
					{
						return $result;
					}
				}
			}
		}
		
		return NULL;
	}
	
	
	/**
	 * Make a method call to all themes and return the first result !== NULL (3 argument version).
	 *
	 * @param string $function
	 * @return string The renderung output
	 */
	function render3($function,&$arg1,&$arg2,&$arg3)
	{
		//first to user modules then default
		$all_themes = array(xTheme::getThemes(),xTheme::getDefaultThemes());
		
		foreach($all_themes as $themes)
		{
			foreach($themes as $theme)
			{
				if(method_exists($theme,$function))
				{
					$result = $theme->$function($arg1,$arg2,$arg3);
					if($result !== NULL)
					{
						return $result;
					}
				}
			}
		}
		
		return NULL;
	}
	
	
	/**
	 * Make a method call to all themes and return the first result !== NULL (4 argument version).
	 *
	 * @param string $function
	 * @return string The renderung output
	 */
	function render4($function,&$arg1,&$arg2,&$arg3,&$arg4)
	{
		//first to user modules then default
		$all_themes = array(xTheme::getThemes(),xTheme::getDefaultThemes());
		
		foreach($all_themes as $themes)
		{
			foreach($themes as $theme)
			{
				if(method_exists($theme,$function))
				{
					$result = $theme->$function($arg1,$arg2,$arg3,$arg4);
					if($result !== NULL)
					{
						return $result;
					}
				}
			}
		}
		
		return NULL;
	}
};

/**
 * A dummy theme class for documentation purpose only
 */
class xDummyTheme extends xTheme
{
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
	}
	
	
	/**
	* Should return an array of strings representing the names of the areas in the page.
	*
	* @return array(string) Area names
	*/
	function declareAreas()
	{
	}
	
	/**
	* Render the whole page.
	* 
	* @param array(xArea) $areas
	* @return string the renderized element.
	*/
	function renderPage($content,$areas)
	{
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
	}
	
	
	/**
	 * Render a list of menu items
	 * 
	 * @param array(xMenuItem) $items
	 * @return string the renderized element.
	 */
	function renderMenuItems($items)
	{
	}
	
	/**
	 * Render an item page
	 *
	 * @param string $subtype
	 * @param string $title
	 * @param string $content
	 * @return string the renderized element.
	 */
	function renderItemPage($subtype,$title,$content)
	{
	}
	
	/**
	 * Render an array of notifications
	 *
	 * @param array(array("severity" => int,"message" => string)) $notifications
	 * @return string the renderized element.
	 */
	function renderNotifications($notifications)
	{}
}


/**
 * The default theme.
 */
class xDefaultTheme extends xTheme
{
	
	function xDefaultTheme()
	{}
	
	/**
	 * @see xDummyModule
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
	 * @see xDummyModule
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
	 * @see xDummyModule
	 */
	function declareAreas()
	{
		return array('leftArea');
	}
	
	/**
	 * @see xDummyModule
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
	function renderItemPage($subtype,$title,$content)
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
		
	}
};


xTheme::registerDefaultTheme(new xDefaultTheme());



?>
