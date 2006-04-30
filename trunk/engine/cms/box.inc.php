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
* Represent box visual element. The box id is a string.
*/
class xBox extends xElement
{
	/**
	* @var string
	* @access public
	*/
	var $m_title;
	
	/**
	* if dynamic, content and content format will be ignored. Contents will be generated dymanically by some module.
	*
	* @var bool
	* @access public
	*/ 
	var $m_is_dynamic;
	
	/**
	* @var string
	* @access public
	*/
	var $m_content;
	
	/**
	* @var string
	* @access public
	*/
	var $m_content_format;
	
	/**
	* if empty (NULL,FALSE,...) no area assignation
	*
	* @var string
	* @access public
	*/
	var $m_area;
	
	/**
	* Contructor
	*
	* @param string $id
	* @param string $title
	* @param bool $is_dynamic
	* @param string $content
	* @param string $content_format
	* @param string $area
	*/
	function xBox($id,$title,$is_dynamic,$content,$content_format,$area = NULL)
	{
		$this->xElement($id);
		
		$this->m_title = $title;
		$this->m_is_dynamic = $is_dynamic;
		$this->m_content = $content;
		$this->content_format = $content_format;
		$this->m_area = $area;
	}
	
	// DOCS INHERITHED  ========================================================
	function render()
	{
		//must override
		assert(FALSE);
	}
	
	/**
	* Insert a this box element into database.
	*
	* 
	*/
	function dbInsert()
	{
		xBoxDAO::insert($this);
	}
	
	/**
	*
	*
	* @static
	*/
	function getBoxesForArea($name)
	{
		//return xBoxDAO::find($this->m_name);
		return array();
	}
};




?>
