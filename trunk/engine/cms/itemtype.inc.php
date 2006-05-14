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
 * An item type.
 */
class xItemType
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
	 * @var string
	 * @access public
	 */
	var $m_default_content_filter;
	
	/**
	 * @var bool
	 * @access public
	 */
	var $m_default_approved;
	
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
	 * @var int
	 * @access public
	 */
	var $m_default_weight;
	
	
	/**
	 * @var int
	 * @access public
	 */
	var $m_accessfiltersetid;
	
	
	/**
	 * @var bool
	 * @access public
	 */
	var $m_default_accept_replies;
	
	
	/**
	 *
	 */
	function xItemType($name,$description,$default_content_filter,$default_approved,$default_published,$default_sticky,$default_weight,
		$default_accept_replies,$accessfiltersetid)
	{
		$this->xElement();
		
		$this->m_name = $name;
		$this->m_description = $description;
		$this->m_default_content_filter = $default_content_filter;
		$this->m_default_approved = $default_approved;
		$this->m_default_published = $default_published;
		$this->m_default_sticky = $default_sticky;
		$this->m_default_weight = $default_weight;
		$this->m_accessfiltersetid = $accessfiltersetid;
		$this->m_default_accept_replies = $default_accept_replies;
	}
	
	
	/** 
	 * Inserts this into db
	 */
	function dbInsert()
	{
		xItemTypeDAO::insert($this);
	}
	
	/** 
	 * Delete this from db
	 */
	function dbDelete()
	{
		xItemTypeDAO::delete($this->m_name);
	}
	
	
	/** 
	 * Delete an item type from db using its name
	 *
	 * @param string $typename
	 * @static
	 */
	function dbDeleteById($typename)
	{
		xItemTypeDAO::delete($typename);
	}
	
	/**
	 * Update this in db
	 */
	function dbUpdate()
	{
		xItemTypeDAO::update($this);
	}
	
	/**
	 * Retrieve a specific item type from db
	 *
	 * @return xItemType
	 * @static
	 */
	function dbLoad($name)
	{
		return xItemTypeDAO::load($name);
	}
	
	/**
	 * Retrieves all itme types.
	 *
	 * @return array(xItemType)
	 * @static
	 */
	function findAll()
	{
		return xItemTypeDAO::findAll();
	}
};


?>
