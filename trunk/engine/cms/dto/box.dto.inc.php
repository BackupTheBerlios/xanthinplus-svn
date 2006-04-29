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
* Box Data Transfer Object
*/
class xBoxDTO
{
	/**
	* @var string
	* @access public
	*/
	var $m_name;
	
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
	* if empty (NULL,FALSE,...) no area ssignation
	*
	* @var string
	* @access public
	*/
	var $m_area;
};







?>