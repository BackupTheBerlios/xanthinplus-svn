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
 * Represent the standard node in xanthin+
 */
class xNodePage extends xNode
{
	/**
	 * @var bool
	 * @access public
	 */
	var $m_sticky;
	
	/**
	 * @var bool
	 * @access public
	 */
	var $m_accept_replies;
	
	/**
	 * @var bool
	 * @access public
	 */
	var $m_published;
	
	/**
	 * @var bool
	 * @access public
	 */
	var $m_approved;
	
	/**
	 * @var string
	 * @access public
	 */
	var $m_meta_description;
	
	/**
	 * @var bool
	 * @access public
	 */
	var $m_meta_keywords;
	
	/**
	 * @var bool
	 * @access public
	 */
	var $m_last_edit_time;
	
	/**
	 *
	 */
	function xNodePage($id,$title,$type,$author,$content,$content_filter,$parent_cathegories,$creation_time,
		$edit_time,$published,$sticky,$accept_replies,$approved,$meta_description,$meta_keywords)
	{
		xNode::xNode($id,$title,$type,$author,$content,$content_filter,$parent_cathegories,
			$creation_time,$edit_time);
			
		$this->m_sticky = $sticky;
		$this->m_accept_replies = $accept_replies;
		$this->m_published = $published;
		$this->m_approved = $approved;
		$this->m_meta_description = $meta_description;
		$this->m_meta_keywords = $meta_keywords;
	}
	
	/** 
	 * Inserts this into db
	 *
	 * @return bool FALSE on error
	 */
	function dbInsert()
	{
		$this->m_id = xNodePageDAO::insert($this);
		return $this->m_id;
	}
	
	/**
	 * Update this in db
	 *
	 * @return bool FALSE on error
	 */
	function dbUpdate()
	{
		return xNodePageDAO::update($this);
	}
	
	/**
	 * Retrieve a specific Node page from db
	 *
	 * @return xNodePage
	 * @static
	 */
	function dbLoad($id)
	{
		return xNodePageDAO::load($id);
	}
};

?>
