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
 * Represent a comment
 */
class xItemComment extends xItem
{
	/**
	 *
	 */
	function xItemComment($id,$title,$type,$author,$content,$content_filter,$creation_time,$lastedit_time)
	{
		$this->xItem($id,$title,$type,$author,$content,$content_filter,$creation_time,$lastedit_time);
	}
	
	
	// DOCS INHERITHED  ========================================================
	function onRender()
	{
		return xTheme::render3('renderItemPage',$this->m_subtype,$this->m_title,$this->m_content);
	}
	
	/** 
	 * Inserts this into db
	 */
	function dbInsert()
	{
		xItemPageDAO::insert($this);
	}
	
	/** 
	 * Delete this from db
	 */
	function dbDelete()
	{
		xItemPageDAO::delete($this->m_id);
	}
	
	/**
	 * Update this in db
	 */
	function dbUpdate()
	{
		xItemPageDAO::update($this);
	}
	
	/**
	 * Retrieve a specific item page from db
	 *
	 * @return xItemPage
	 * @static
	 */
	function dbLoad($id)
	{
		return xItemPageDAO::load($id);
	}
	
	/**
	 * Retrieves all items.
	 *
	 * @param string $type Exact search
	 * @param string $title Like search
	 * @param string $author Exact search
	 * @param string $content Like search
	 * @param int $cathegory Exact search on category id
	 * @param int $nelementpage Number of elements per page
	 * @param int $npage Number of page (starting from 1).
	 * @return array(xItem)
	 * @static
	 */
	function find($subtype = NULL,$title = NULL,$author = NULL,$content = NULL,$cathegory = NULL,$nelementpage = 0,$npage = 0)
	{
		return xItemPageDAO::find($subtype,$title,$author,$content,$cathegory,$nelementpage,$npage);
	}
};


?>
