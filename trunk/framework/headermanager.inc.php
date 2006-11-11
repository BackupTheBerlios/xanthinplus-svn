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


$g_xanth_header_manager = array('stylesheets' => array(),'description' => '',
	'keywords' => array(),'title' => '','language' => '');

/**
 * An object that contains function related to html header contents
 */
class xHeaderManager
{
	function xHeaderManager()
	{
		assert(false);
	}
	
	/**
	 * @static
	 * @return &array
	 */
	function &getStylesheets()
	{
		global $g_xanth_header_manager;
		return $g_xanth_header_manager['stylesheets'];
	}
	
	/**
	 * @static
	 * @return &string
	 */
	function &getTitle()
	{
		global $g_xanth_header_manager;
		return $g_xanth_header_manager['title'];
	}
	
	
	/**
	 * @static
	 * @return &string
	 */
	function &getLanguage()
	{
		global $g_xanth_header_manager;
		return $g_xanth_header_manager['language'];
	}
	
	/**
	 * @static
	 * @return &string
	 */
	function &getDescription()
	{
		global $g_xanth_header_manager;
		return $g_xanth_header_manager['description'];
	}
	
	/**
	 * @static
	 * @return &array
	 */
	function &getKeywords()
	{
		global $g_xanth_header_manager;
		return $g_xanth_header_manager['keywords'];
	}
	
	
	/**
	 * @static
	 */
	function renderDescription()
	{
		global $g_xanth_header_manager;
		return '<META name="description" content="' . $g_xanth_header_manager['description'] . '">';
	}
	
	
	/**
	 * @static
	 */
	function renderKeywords()
	{
		global $g_xanth_header_manager;
		return '<META name="keywords" content="' . implode(' , ',$g_xanth_header_manager['keywords']) . '">';
	}
	
	/**
	 * @static
	 */
	function renderTitle()
	{
		global $g_xanth_header_manager;
		return '<title>' . $g_xanth_header_manager['title'] . '<title>';
	}
	
	/**
	 * @static
	 */
	function renderContentLanguage()
	{
		global $g_xanth_header_manager;
		return '<META name="Content-language" content="' . $g_xanth_header_manager['language'] . '">';
	}
	
	/**
	 * @static
	 */
	function renderContentType()
	{
		return '<meta HTTP-EQUIV="content-type" CONTENT="text/html; charset=UTF-8">';
	}
	
	
	/**
	 * @static
	 */
	function renderStylesheets()
	{
		$csses = xModuleManager::invokeAll('fetchStylesheet');
		$csses = $csses->getValidValues(true);
		
		$output = '';
		foreach($csses as $css)
			$output .= '<style type="text/css" media="all">@import "'.$css.'";</style>';
		
		return $output;
	}
};




?>