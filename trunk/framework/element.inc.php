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
 * Represents all objects ables to be rendered.
 */
class xRenderable
{
	var $m_path;
	
	/**
	 * 
	 */
	function xRenderable($path)
	{
		$this->m_path = $path;
	}
	
	
	/**
	 * Render the widget.
	 *
	 * @return string A string representing the renderized element.
	 */
	function render()
	{
		$copy = $this->sanitize();
		xModule::invoke('xm_render',$this);
	}
	
	/**
	 * Returns a copy of this element with all members variables sanitized, ready 
	 * to be rendered.
	 * 
	 * @return xElement
	 */
	function sanitize()
	{
		return xanth_clone($this);
	}
	
	/**
	 * Check preconditions for the current element, before render it.
	 * 
	 * @return xResult
	 */
	function checkPreconditions()
	{
		return new xResult(true);
	}
}



/**
 * Tha base class for all visual elements.
 */
class xWidget extends xRenderable
{
	/**
	 * @var string
	 */
	var $m_widget_type;
	
	/**
	 * @var string
	 */
	var $m_widget_subtype;
	
	/**
	 * @var mixed
	 */
	var $m_widget_id;
	
	/**
	 * 
	 */
	function xElement($path,$widget_subtype,$widget_type,$widget_id)
	{
		$this->xRenderable($path);
		$this->m_elem_subtype = $widget_subtype;
		$this->m_elem_type = $widget_type;
		$this->m_elem_id = $widget_id;
	}
	

};



/**
 * 
 */
class xWidgetGroup extends xWidget
{
	var $m_name;
	var $m_elements;
	
	function xElementGroup()
	{
		$this->xElementGroup();	
	}
}

?>
