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
 * An object that contains function related to html header contents
 */
class xHeader extends xRenderable
{
	function xHeader()
	{
	}
	
	
	/**
	 * @static
	 */
	function process()
	{
		$csses = xModule::invokeAll('fetchStylesheet');
		$csses = $csses->getValidValues(true);
		
		$output = '';
		foreach($csses as $css)
			$output .= '<style type="text/css" media="all">@import "'.$css.'";</style>';
		
		return $output;
	}
	
	
	
	/**
	 * @static
	 */
	function renderStylesheets()
	{
		$csses = xModule::invokeAll('fetchStylesheet');
		$csses = $csses->getValidValues(true);
		
		$output = '';
		foreach($csses as $css)
			$output .= '<style type="text/css" media="all">@import "'.$css.'";</style>';
		
		return $output;
	}
};





?>