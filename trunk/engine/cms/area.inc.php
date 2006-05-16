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
* An area in the page. Tha page id is a string.
*/
class xArea extends xElement
{
	/**
	 * @var array(xBox)
	 * @access public
	 */
	var $m_boxes;
	
	/**
	 * @var string
	 * @access public
	 */
	var $m_name;
	
	
	/**
	 * Contructor
	 * 
	 * @param string $name
	 */
	function xArea($name)
	{
		$this->xElement();
		
		$this->m_name = $name;
		
		//retrieve boxes for area
		$this->m_boxes = xBox::getBoxesForArea($this->m_name);
	}
	
	/**
	* Return an array of all page areas.
	*
	* @return array(xArea)
	* @static
	*/
	function getAreas()
	{
		$area_strings = xTheme::render0('declareAreas');
		$areas = array();
		foreach($area_strings as $area_string)
		{
			$areas[] = new xArea($area_string);
		}
		
		return $areas;
	}
	
	// DOCS INHERITHED  ========================================================
	function onRender()
	{
		return xTheme::render2('renderArea',$this->m_name,$this->m_boxes);
	}
};


?>
