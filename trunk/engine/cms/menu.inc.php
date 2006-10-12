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
	 * @var int
	 */
	var $m_id;
	
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
	 * @var array(xMenuItem)
	 * @access public
	 */
	var $m_subitems;
	
	/**
	 * Contructor
	 *
	 */
	function xMenuItem($id,$label,$link,$weight,$subitems = array())
	{
		$this->m_id = $id;
		$this->m_label = $label;
		$this->m_link = $link;
		$this->m_weight = $weight;
		$this->m_subitems = $subitems;
	}
	
	// DOCS INHERITHED  ========================================================
	function render()
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
	function xMenu($name,$title,$type,$weight,$show_filter,$items = array())
	{
		$this->xBox($name,$title,$type,$weight,$show_filter);
		$this->m_items = $items;
	}
	
	// DOCS INHERITHED  ========================================================
	function render()
	{
		usort($this->m_items, "_objWeightCompare");
		$content = xTheme::render1('renderMenuItems',$this->m_items);
		
		return xTheme::render3('renderBox',$this->m_name,$this->m_title,$content);
	}
	
	/**
	 * Insert this object into db
	 *
	 * @return bool FALSE on error
	 */
	function dbInsert()
	{
		return xMenuDAO::insert($this);
	}
	
	/**
	 *
	 *
	 * @return bool FALSE on error
	 */
	function dbLoad($name)
	{
		return xMenuDAO::load($name);
	}
};


?>
