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
	var $m_boxes;
	
	/**
	 * Contructor
	 * 
	 * @param string $name
	 */
	function xArea($id)
	{
		$this->xElement($id);
		
		//retrieve boxes for area
		$this->m_boxes = xBox::getBoxesForArea($this->m_id);
	}
	
	/**
	* Return an array of all page areas.
	*
	* @return array(xArea)
	* @static
	*/
	function getAreas()
	{
		$area_strings = xTheme::getActive()->declareAreas();
		$areas = array();
		foreach($area_strings as $area_string)
		{
			$areas[] = new xArea($area_string);
		}
		
		return $areas;
	}
	
	// DOCS INHERITHED  ========================================================
	function render()
	{
		return xTheme::getActive()->renderArea($this->m_id,$this->m_boxes);
	}
};


?>
