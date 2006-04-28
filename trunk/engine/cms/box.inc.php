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
* An object that can output to the user the content of a page .
*/
class xViewBox
{
	function xViewBox()
	{}
	
	/**
	* Render the page element. Override this method in your implementation.
	* 
	* @param $page_element(xPageElement) The page element to render.
	* @return (string) the renderized element.
	*/
	function render($page_element)
	{
		//Virtual method.
		assert(FALSE);
	}
};


/**
* Tha base class for all page elements.
*/
class xPageElement
{
	//! @private
	$m_view;
	
	function xPageElement()
	{
		$m_view = NULL;
	}
	
	/**
	* Set the current object view.
	*
	* @param $view (xView)
	* @return Nothing
	*/
	function setView($view)
	{
		$this->m_view = $view;
	}
	
	/**
	* Get the current object view
	*
	* @return (xView) The current object view.
	*/
	function getView()
	{
		return $this->m_view;
	}
	
	/**
	* Render the page element using the object current view view.
	* 
	* @return (string) XHTML code representing the renderized element.
	*/
	function render()
	{
		return $this->m_view->render($this);
	}
};


?>
