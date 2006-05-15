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
 * Represent an item in the CMS. An item can be an article, a blog entry, a forum post.
 */
class xItem extends xElement
{
	/**
	 * @var int
	 * @access public
	 */
	var $m_id;
	
	/**
	 * @var string
	 * @access public
	 */
	var $m_title;
	
	/**
	 * @var string
	 * @access public
	 */
	var $m_type;
	
	/**
	 * @var string
	 * @access public
	 */
	var $m_author;
	
	/**
	 * @var string
	 * @access public
	 */
	var $m_content;
	
	/**
	 * @var string
	 * @access public
	 */
	var $m_content_filter;
	
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
	 * @var bool
	 * @access public
	 */
	var $m_accept_replies;
	
	
	/**
	 * @var bool
	 * @access public
	 */
	var $m_sticky;
	
	
	/**
	 * @var int
	 * @access public
	 */
	var $m_weight;
	
	/**
	 * @var string
	 * @access public
	 */
	var $m_description;
			
	/**
	 * @var string
	 * @access public
	 */
	var $m_keywords;
	
	/**
	 * @var timestamp
	 * @access public
	 */
	var $m_creation_time;
			
	/**
	 * @var timestamp Can be NULL
	 * @access public
	 */
	var $m_lastedit_time;
	
	
	/**
	 *
	 */
	function xItem($id,$title,$type,$author,$content,$content_filter,
		$published,$approved,$accept_replies,$sticky,$weight,$description,$keywords,$creation_time,$lastedit_time)
	{
		$this->xElement();
		
		$this->m_id = $id;
		$this->m_title = $title;
		$this->m_type = $type;
		$this->m_author = $author;
		$this->m_content = $content;
		$this->m_content_filter = $content_filter;
		$this->m_published = $published;
		$this->m_approved = $approved;
		$this->m_accept_replies = $accept_replies;
		$this->m_sticky = $sticky;
		$this->m_weight = $weight;
		$this->m_description = $description;
		$this->m_keywords = $keywords;
		$this->m_creation_time = $creation_time;
		$this->m_lastedit_time = $lastedit_time;
	}
	
	
	// DOCS INHERITHED  ========================================================
	function onRender()
	{
		
	}
	
	/** 
	 * Inserts this into db
	 */
	function dbInsert()
	{
		xItemDAO::insert($this);
	}
	
	/** 
	 * Delete this from db
	 */
	function dbDelete()
	{
		xItemDAO::delete($this->m_id);
	}
	
	
	/** 
	 * Delete an item from db using its id
	 *
	 * @param int $catid
	 * @static
	 */
	function dbDeleteById($id)
	{
		xItemDAO::delete($id);
	}
	
	/**
	 * Update this in db
	 */
	function dbUpdate()
	{
		xItemDAO::update($this);
	}
	
	/**
	 * Retrieve a specific item from db
	 *
	 * @return xItem
	 * @static
	 */
	function dbLoad($id)
	{
		return xItemDAO::load($id);
	}
	
	/**
	 * Retrieves all replies associated with an item.
	 *
	 * @param int $parentid
	 * @param int $nelementpage Number of elements per page
	 * @param int $npage Number of page (starting from 1).
	 * @return array(xItem)
	 * @static
	 */
	function findReplies($parentid,$nelementpage = 0,$npage = 0)
	{
		return xItemDAO::findReplies($parentid,$nelementpage,$npage);
	}
	
	
	/**
	 * Retrieves all items.
	 *
	 * @param string $type Exact search
	 * @param string $title Like search
	 * @param string $author Exact search
	 * @param string $content Like search
	 * @param bool $published
	 * @param bool $approved
	 * @param int $cathegory Exact search on category id
	 * @param int $nelementpage Number of elements per page
	 * @param int $npage Number of page (starting from 1).
	 * @return array(xItem)
	 * @static
	 */
	function find($type = NULL,$title = NULL,$author = NULL,$content = NULL,$published = NULL,$approved = NULL,
		$cathegory = NULL,$nelementpage = 0,$npage = 0)
	{
		return xItemDAO::find($type,$title,$author,$content,$published,$approved,$cathegory,$nelementpage,$npage);
	}
	
	
	
	/**
	 * Return a form element for asking for title input
	 *
	 * @param string $var_name The name of the form element
	 * @param string $value
	 * @param bool $mandatory True if this input is manadtory
	 * @return xFormElement
	 * @static
	 */
	function getFormTitleInput($var_name,$value,$mandatory)
	{
		return new xFormElementTextField($var_name,'Title','',$value,$mandatory,new xInputValidatorText(256));
	}
	
	
	/**
	 * Return a form element for asking for body input
	 *
	 * @param string $var_name The name of the form element
	 * @param string $value
	 * @param bool $mandatory True if this input is manadtory
	 * @return xFormElement
	 * @static
	 */
	function getFormBodyInput($var_name,$value,$mandatory)
	{
		return new xFormElementTextArea($var_name,'Body','',$value,$mandatory,new xInputValidatorText(0));
	}
};


?>
