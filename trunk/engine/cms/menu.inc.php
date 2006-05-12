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
	var $m_text; 
	
	/**
	 * @var string
	 * @access public
	 */
	var $m_link;
	
	
	/**
	 * Contructor
	 *
	 * @param string $text
	 * @param string $link
	 */
	function xMenuItem($text,$link)
	{
		$this->m_text = $text;
		$this->m_link = $link;
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
	function xMenu($name,$title,$type,$items = array(),$filterset,$area = NULL)
	{
		$this->xBox($name,$title,$type,$filterset,$area);
		$this->m_items = $items;
	}
	
	// DOCS INHERITHED  ========================================================
	function onRender()
	{
		$content = xTheme::getActive()->renderMenuItems($this->m_items);
		return xTheme::getActive()->renderBox($this->m_name,$this->m_title,$content);
	}
};

/**
 * Represent a simple static link menu.
 */
class xMenuStatic extends xMenu
{
	/**
	* Contructor
	*
	* @param string $name
	* @param string $title
	* @param string $type
	* @param string $area
	*/
	function xMenuStatic($name,$title,$type,$items = array(),$filterset,$area = NULL)
	{
		$this->xMenu($name,$title,$type,$items,$filterset,$area);
	}
	
	/**
	 * Insert this object into db
	 */
	function dbInsert()
	{
		xMenuStaticDAO::insert($this);
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
		return xMenuStaticDAO::toSpecificBox($box);
	}
};


/**
 * Represent a simple dinamic link menu.
 */
class xMenuDynamic extends xMenu
{	
	/**
	* Contructor
	*
	* @param string $name
	* @param string $title
	* @param string $type
	* @param string $area
	*/
	function xMenuDynamic($name,$title,$type,$items = array(),$filterset,$area = NULL)
	{
		$this->xMenu($name,$title,$type,$items,$filterset,$area);
	}
	
	/**
	 * Build and return a xMenuStatic object derived from a simple xBox 
	 *
	 * @return xMenuDynamic
	 * @static
	 */
	function toSpecificBox($box)
	{
		//ask modules for items
		$items = xModule::callWithArrayResult1('getMenuItem',$box->m_name);
		
		return new xMenuDynamic($box->m_name,$box->m_title,$box->m_type,$items,$box->m_filterset,$box->m_area);
	}
};





?>
