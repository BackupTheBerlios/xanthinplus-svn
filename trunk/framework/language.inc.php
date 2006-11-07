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
 * Represent a language
 */
class xLanguage
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
	var $m_full_name;
	

	/**
	 *
	 */
	function xLanguage($name,$full_name)
	{
		$this->m_name = $name;
		$this->m_full_name = $full_name;
	}
	
	
	/** 
	 * Inserts this into db
	 *
	 * @return bool FALSE on error
	 */
	function insert()
	{
		xLanguageDAO::insert($this);
	}
	
	/** 
	 * Delete this from db
	 *
	 * @return bool FALSE on error
	 */
	function delete()
	{
		return xLanguageDAO::delete($this->m_name);
	}
	
	/**
	 * Retrieve a specific item type from db
	 *
	 * @return xItemType
	 * @static
	 */
	function load($name)
	{
		return xLanguageDAO::load($name);
	}
	
	/**
	 * Retrieves all node types.
	 *
	 * @return array(xNodeType)
	 * @static
	 */
	function findNames()
	{
		return xLanguageDAO::findNames();
	}
	
	/**
	 * Retrieves all node types.
	 *
	 * @return array(xNodeType)
	 * @static
	 */
	function find()
	{
		return xLanguageDAO::find();
	}
};


?>
