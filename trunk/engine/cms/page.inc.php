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
* Represent the entire web page.
*/
class xPage extends xElement
{
	/**
	 * @var xPath
	 * @access public
	 */
	var $m_path;
	
	
	/**
	 * @var array(xBoxGroup)
	 * @access public
	 */
	var $m_box_groups;
	
	/**
	 * @var xPageContent
	 * @access public
	 */
	var $m_content;
	
	/**
	 *
	 */
	function xPage($path,$content,$box_groups)
	{
		$this->xElement();
		
		$this->m_path = $path;
		$this->m_box_groups = $box_groups;
		$this->m_content = $content;
	}
	
	
	// DOCS INHERITHED  ========================================================
	function render()
	{
		return xTheme::render2('renderPage',$this->m_content,$this->m_box_groups);
	}
	
	/**
	 * Retrieve the page that correnspond to a path.
	 *
	 * @param xPath $path
	 * @static
	 */
	function fetchPage($path)
	{
		if($path !== NULL)
		{
			//broadcast onPageCreation event
			xModule::callWithNoResult1('xm_onPageCreation',$path);
		
			//ask for content
			$content = xPageContent::fetchContent($path);
		}
		else
		{
			$content = new xPageContentSimple('Error','ERROR: Invalid path','','',$path);
		}
		
		//ask for areas
		$box_groups = NULL;
		
		return new xPage($path,$content,$box_groups);
	}
};

?>
