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
		$published,$sticky,$weight,$description,$keywords,$creation_time,$lastedit_time)
	{
		$this->xElement();
		
		$this->m_id = $id;
		$this->m_title = $title;
		$this->m_type = $type;
		$this->m_author = $author;
		$this->m_content = $content;
		$this->m_content_filter = $content_filter;
		$this->m_published = $published;
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
};


?>
