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
* Represent  box visual element.
*/
class xBox extends xElement
{
	//! (xBoxDao)
	var $m_boxdao;
	
	/**
	* Contructor
	*
	* @param $boxdao (xBoxDao) 
	*/
	function xBox($boxdao = NULL)
	{
		$this->xElement();
		$m_boxdao = $boxdao;
	}
	
	// DOCS INHERITHED  ========================================================
	function render()
	{
		//must override
		assert(FALSE);
	}
	
	
	/**
	*
	*
	* @static
	*/
	function getBoxesForArea($this->m_name)
	{
	
	
	}
};




?>
