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
 * An item page subtype.
 */
class xItemPageType
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
	var $m_description;
	
	/**
	 * @var array(string)
	 * @access public
	 */
	var $m_allowed_content_filters;
	
	/**
	 * @var bool
	 * @access public
	 */
	var $m_default_published;
	
	/**
	 * @var bool
	 * @access public
	 */
	var $m_default_sticky;
	
	/**
	 * @var bool
	 * @access public
	 */
	var $m_default_accept_replies;
	
	/**
	 * @var bool
	 * @access public
	 */
	var $m_default_approved;

	/**
	 *
	 */
	function xItemPageType($name,$description,$allowed_content_filters,$default_published,$default_sticky,
		$default_accept_replies,$default_approved)
	{
		$this->m_name = $name;
		$this->m_description = $description;
		$this->m_allowed_content_filters = $allowed_content_filters;
		$this->m_default_published = $default_published;
		$this->m_default_sticky = $default_sticky;
		$this->default_accept_replies = $default_accept_replies;
		$this->m_default_approved = $default_approved;
	}
	
	
	/** 
	 * Inserts this into db
	 */
	function dbInsert()
	{
		$this->m_id = xItemPageTypeDAO::insert($this);
	}
	
	/** 
	 * Delete this from db
	 */
	function dbDelete()
	{
		xItemPageTypeDAO::delete($this->m_name);
	}
	
	
	/** 
	 * Delete an item type from db using its name
	 *
	 * @param int $typename
	 * @static
	 */
	function dbDeleteById($typename)
	{
		xItemPageTypeDAO::delete($typename);
	}
	
	/**
	 * Update this in db
	 */
	function dbUpdate()
	{
		xItemPageTypeDAO::update($this);
	}
	
	/**
	 * Retrieve a specific item type from db
	 *
	 * @return xItemPageType
	 * @static
	 */
	function dbLoad($typename)
	{
		return xItemPageTypeDAO::load($typename);
	}
	
	/**
	 * Retrieves all itme types.
	 *
	 * @return array(xItemPageType)
	 * @static
	 */
	function findAll()
	{
		return xItemPageTypeDAO::findAll();
	}
};


?>
