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
* Tha base class for all page elements.
*/
class xElement
{
	/**
	 * @var xAccessManager
	 * @access public
	 */
	var $m_access_manager;
	
	/**
	 * Create a new Element.
	 */
	function xElement($access_manager = xAccessManager::getNullAccessManager())
	{
		$this->m_access_manager = $access_manager();
	}
	
	/**
	* Check the element access manager and render the page element.
	* 
	* @return string XHTML code representing the renderized element.
	*/
	function render()
	{
		if($this->m_access_manager->checkAccess())
		{
			return $this->onRender();
		}
		else
		{
			return '';
		}
	}
	
	/**
	 * Render the page element using the object current view view.
	 *
	 * @return string XHTML code representing the renderized element.
	 * @abstract
	 * @access protected
	 */
	function onRender()
	{
		//virtual method
		assert(FALSE);
	}
};


?>
