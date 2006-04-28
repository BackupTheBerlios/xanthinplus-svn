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
	var $m_areas;
	var $m_title;
	var $m_keywords;
	var $m_description;
	
	function xPage()
	{
		$this->xElement();
		$this->m_areas = array();
		$this->m_title = '';
		$this->m_keywords = '';
		$this->m_description = '';
		
		//ask theme for areas
		xTheme::getActive()->
	}
	
	// DOCS INHERITHED  ========================================================
	function render()
	{
		return xTheme::getActive()->renderPage($this);
	}
};

?>
