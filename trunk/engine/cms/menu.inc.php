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
 * Represent a menu item.
 */
class xMenuItem extends xElement
{
	/**
	 * @var string
	 * @access public
	 */
	var $m_label;
	
	/**
	 * @var string
	 * @access public
	 */
	var $m_link;
	
	/**
	 * @var int
	 * @access public
	 */
	var $m_weight;
	
	/**
	 * @var int
	 * @access public
	 */
	var $m_accessfilterset;
	
	/**
	 * @var array(xMenuItem)
	 * @access public
	 */
	var $m_subitems;
	
	/**
	 * Contructor
	 *
	 */
	function xMenuItem($label,$link,$weight,$subitems = array(),$accessfilterset = NULL)
	{
		$this->m_label = $label;
		$this->m_link = $link;
		$this->m_weight = $weight;
		$this->m_accessfilterset = $accessfilterset;
		$this->m_subitems = $subitems;
	}
	
	// DOCS INHERITHED  ========================================================
	function render()
	{
		//here we will provide a check for access filter.
		if(! xAccessFilterSet::checkAccessByFilterSetId($this->m_accessfilterset))
		{
			return NULL;
		}
		
		return $this->onRender();
	}
	
	
	// DOCS INHERITHED  ========================================================
	function onRender()
	{
		usort($this->m_subitems, "_objWeightCompare");
		
		$subitems = xTheme::render1('renderMenuItems',$this->m_subitems);

		return xTheme::render3('renderMenuItem',$this->m_label,$this->m_link,$subitems);
	}
};


/**
 * Represent a simple link menu.
 */
class xMenu extends xBox
{
	/**
	 * @var array(xMenuItem)
	 */
	var $m_items;
	
	/**
	* Contructor
	*
	* @param string $name
	* @param string $title
	* @param string $type
	* @param string $area
	*/
	function xMenu($name,$title,$type,$weight,$items = array(),$filterset,$area = NULL)
	{
		$this->xBox($name,$title,$type,$weight,$filterset,$area);
		$this->m_items = $items;
	}
	
	// DOCS INHERITHED  ========================================================
	function onRender()
	{
		usort($this->m_items, "_objWeightCompare");
		$content = xTheme::render1('renderMenuItems',$this->m_items);
		
		return xTheme::render3('renderBox',$this->m_name,$this->m_title,$content);
	}
	
	/**
	 * Insert this object into db
	 */
	function dbInsert()
	{
		xMenuDAO::insert($this);
	}
	
	/**
	 * Build and return a xMenuStatic object derived from a simple xBox 
	 *
	 * @return xMenuStatic
	 * @static
	 */
	function toSpecificBox($box)
	{
		//extract items from db
		return xMenuDAO::toSpecificBox($box);
	}
};


?>
