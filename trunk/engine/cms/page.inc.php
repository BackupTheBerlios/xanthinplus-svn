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
	* @var array(xArea)
	* @access public
	*/
	var $m_areas;
	
	/**
	* @var xContent
	* @access public
	*/
	var $m_content;
	
	/**
	*
	*/
	function xPage()
	{
		$this->xElement('');
		
		//broadcast onPageCreation event
		$modules = xModule::getModules();
		foreach($modules as $module)
		{
			if(method_exists($module,'onPageCreation'))
			{
				$module->onPageCreation();
			}
		}
		
		//ask for content
		$this->m_content = xContent::getContent();
		
		//ask for areas
		$this->m_areas = xArea::getAreas();
	}
	
	
	// DOCS INHERITHED  ========================================================
	function render()
	{
		return xTheme::getActive()->renderPage($this->m_id,$this->m_content,$this->m_areas);
	}
};

?>
