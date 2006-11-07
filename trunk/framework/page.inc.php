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
	 * @var xContent
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
		return xTheme::render('renderPage',array($this->m_content,$this->m_box_groups));
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
			//todo check for errors
			xModule::invokeAll('xm_onPageCreation',array($path));
		
			//ask for content
			$content = xContent::fetchContent($path);
		}
		else
		{
			$content = new xContentSimple('Error','ERROR: Invalid path','','',$path);
		}
		
		//ask for areas
		$box_groups = xBoxGroup::find(TRUE);
		$groups = array();
		foreach($box_groups as $group)
		{
			$group->loadBoxes($path->m_lang);
			usort($group->m_boxes,'_objWeightCompare');
			$groups[$group->m_name] = $group;
		}
		
		return new xPage($path,$content,$groups);
	}
};

?>