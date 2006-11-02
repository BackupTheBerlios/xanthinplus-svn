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
	 * Load the specificed theme
	 */
	function load($name)
	{
		if(empty($name))
			return;
		
		if($handle = opendir('themes/'.$name.'/')) 
		{
		    while(false !== ($file = readdir($handle))) 
			{
		        if($file != "." && $file != ".." && is_file('themes/'.$name.'/'.$file)) 
				{
					$pieces = explode('.',$file);
					if(array_pop($pieces) == 'php')
					{
						include_once('themes/'.$name.'/'.$file);
					}
		        }
		    }
		    closedir($handle);
		}
		else
		{
			xNotifications::add(NOTIFICATION_ERROR,'Selected theme does not exists');
		}
	}
	
	
	/**
	 * 
	 * @static
	 */
	function renderAllCss()
	{
		$csses = xTheme::callWithArrayResult0('getCss');
		
		$output = '';
		foreach($csses as $css)
		{
			$output .= '<style type="text/css" media="all">@import "'.$css.'";</style>';
		}
		
		return $output;
	}
	
	
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
	 * Make a method call to all themes and return an array that is the union
	 * of all results != NULL returned. (0 argument version).
	 *
	 * @param string $function The method to call
	 * @return array(mixed)
	 */
	function callWithArrayResult0($function)
	{
		$array_result = array();
		$themes = array_merge(xTheme::getThemes(),xTheme::getDefaultThemes());
		foreach($themes as $theme)
		{
			if(method_exists($theme,$function))
			{
				$result = $theme->$function();
				if($result !== NULL)
				{
					if(is_array($result))
					{
						foreach($result as $one_result)
						{
							$array_result[] = $one_result;
						}
					}
					else
					{
						$array_result[] = $result;
					}
				}
			}
		}
		
		return $array_result;
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
 * The default theme.
 */
class xDefaultTheme extends xTheme
{
	function xDefaultTheme()
	{}
	
	
	/**
	 * Return the path to theme css file or an array of it.
	 *
	 * @return mixed
	 */
	function getCss()
	{
		return "engine/cms/default.css";
	}
	
	
	/**
	 * Render the box element.
	 * 
	 * @param string $name
	 * @param string $title
	 * @param string $content
	 * @return string the renderized element.
	 */
	function renderBox($name,$title,$content)
	{
		$output = 
		'<div class="box"><div class="title">' . $title . '</div>
		<div>' . $content . '</div></div>
		'
		;
		return $output;
	}
	
	/**
	 * Render the boxgroup element.
	 * 
 	 * @param string $group_name
	 * @param array(string) $rendered_boxes
	 * @return string the renderized element.
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
	 * Render the whole page.
	 * 
	 * @param xPageContent $content
	 * @param array(xBoxGroup) $groups
	 * @return string the renderized element.
	 */
	function renderPage($content,$groups)
	{
		$output = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
		<html>
		<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
		<title>' . $content->m_title . '</title>
		<meta name="keywords" content="' . $content->m_meta_keywords . '" />
		<meta name="description" content="' . $content->m_meta_description . '" />
		'. xTheme::renderAllCss() .'
		</head>
		<body>
		<table id="page-table"><tr>
		<td id="left-sidebar">'. $groups['left_group']->render() .'</td>
		<td id="content">
		' . xNotifications::render($output) . $content->render() . '
		</td></tr></table>
		<div align="center"> Queries ' . $db->queryGetCount() . ', Execution time ' . xExecutionTime::render() . ' secs</div>
		' . xLogEntry::renderFromScreen() . '
		</body>
		</html>';
		
		return $output;
	}
	
	/**
	 * Render a menu item
	 * 
	 * @param string $label
	 * @param string $link
	 * @param string $subitems
	 * @return string the renderized element.
	 */
	function renderMenuItem($label,$link,$subitems)
	{
		return '<li><a href="' . $link . '">' . $label . '</a>' . $subitems . '</li>';
	}
	
	
	/**
	 * Render cathegory
	 * 
	 * @param string $title
	 * @param string $descrition
	 * @param array(xNode) $nodes
	 * @return string the renderized element.
	 */
	function renderCathegory($title,$description,$nodes,$operations)
	{
		$out = 
		'<div class="cathegory">
			'.$operations.'
			<div class = "title">
			'.$title.'
			</div>
			<div class="node-list">';
			
		foreach($nodes as $node)
		{
			$out .= $node->renderBrief();
		}
		
		$out .= '</div></div>';
		
		return $out;
	}
	
	/**
	 * Render node operations
	 * 
	 * @param array() $operations An array so structured:
	 * 		array(name => array(link => string, description => string))
	 * @return string the renderized element.
	 */
	function renderNodeOperations($operations)
	{
		$out = '<div class="operations">';
		foreach($operations as $name => $params)
		{
			$out .= '<div class="operation"> <a href="'.$params['link'].'">'.$name.'</a> </div>';
		}
		$out .= '</div>';
		
		return $out;
	}
		
	/**
	 * Render cathegory operations
	 * 
	 * @param array() $operations An array so structured:
	 * 		array(name => array(link => string, description => string))
	 * @return string the renderized element.
	 */
	function renderCathegoryOperations($operations)
	{
		$out = '<div class="operations">';
		foreach($operations as $name => $params)
		{
			$out .= '<div class="operation"> <a href="'.$params['link'].'">'.$name.'</a> </div>';
		}
		$out .= '</div>';
		
		return $out;
	}
	
	
	/**
	 * Render a list of menu items
	 * 
	 * @param array(xMenuItem) $items
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
	
	/**
	 * Render a node.
	 *
	 *
	 * @param string $type
	 * @param string $title
	 * @param string $content
	 * @param string $operations
	 */
	function renderNode($type,$title,$content,$operations)
	{
		$output = '<div class="node">' . $operations . '<div class="node-title">' . $title . '</div>
		<div class="node-content">' . $content . '</div></div>';
		
		return $output;
	}
	
	
	/**
	 * Render a node in a brief version.
	 *
	 *
	 * @param string $type
	 * @param string $title
	 * @param string $content
	 * @param string $operations
	 */
	function renderBriefNode($type,$title,$content,$operations)
	{
		$output = '<div class="node-brief">' . $operations . '<div class="node-title">' . $title . '</div>
		<div class="node-content">' . $content . '</div></div>';
		
		return $output;
	}
	
	
	/**
	 * Render an array of notifications
	 *
	 * @param array(xNotification) $notifications
	 * @return string the renderized element.
	 */
	function renderNotifications($notifications)
	{
		$output = '';
		foreach($notifications as $notification)
		{
			$output .= '<div class="notification"><div class="' . $notification->m_severity . '">' .
				$notification->m_message . '</div></div>';
		}
		
		return $output;
	}
};
xTheme::registerDefaultTheme(new xDefaultTheme());



?>
