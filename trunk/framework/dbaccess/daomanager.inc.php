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



class xDAOManager extends xObject
{
	/**
	 * 
	 */
	var $m_daos = array();
	
	/**
	 * 
	 */
	var $m_type;
	
	function __construct($type)
	{
		$this->m_type = $type;
	}
	
	/**
	 * 
	 */
	function setDAO($name,$dao)
	{
		$this->m_daos[$name] = $dao;
	}
	
	/**
	 * 
	 */
	function &getDAO($name)
	{
		return $this->m_daos[$name];
	}
}

?>