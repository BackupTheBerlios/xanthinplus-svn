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
 * Represent a node in the CMS.
 */
class xNode extends xElement
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
	var $m_alias;
	
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
	 * @var timestamp
	 * @access public
	 */
	var $m_creation_time;

	/**
	 * @var timestamp
	 * @access public
	 */
	var $m_edit_time;
	
	/**
	 * @var array(xCathegory)
	 * @access public
	 */
	var $m_parent_cathegories;

	/**
	 *
	 */
	function xItem($id,$title,$alias,$type,$author,$content,$content_filter,$parent_cathegories = array(),
		$creation_time = NULL,$edit_time = NULL)
	{
		$this->xElement();
		
		$this->m_id = $id;
		$this->m_title = $title;
		$this->m_alias = $alias;
		$this->m_type = $type;
		$this->m_author = $author;
		$this->m_content = $content;
		$this->m_content_filter = $content_filter;
		$this->m_parent_cathegories = $parent_cathegories;
		$this->m_creation_time = $creation_time;
		$this->m_edit_time = $edit_time;
	}
	
	
	// DOCS INHERITHED  ========================================================
	function render()
	{
		$error = '';
		$content = xContentFilterController::applyFilter($this->m_content_filter,$this->m_content,$error);
		$title = xContentFilterController::applyFilter('notags',$this->m_title,$error);
		return xTheme::render3('renderItem',$this->m_type,$title,$content);
	}
	
	/**
	 * Render the node in a compact form with at least a title and optionally a sumary of the content
	 *
	 * @return string
	 */
	function renderSummary()
	{
		//todo
		assert(false);
	}

	
	/** 
	 * Delete a node from db using its id
	 *
	 * @param int $id
	 * @return bool FALSE on error
	 * @static
	 */
	function dbDeleteById($id)
	{
		return xNodeDAO::delete($id);
	}
	
	/**
	 * @return string NULL on error
	 */
	function getNodeTypeById($id)
	{
		return xNodeDAO::getNodeTypeById($id);
	}
	
	/**
	 * Retrieve a node from db.
	 *
	 * @return xItem
	 * @static
	 */
	function dbLoad($id)
	{
		return xNodeDAO::load($id);
	}
	
	
	/**
	 * Check a permission for an action relative to a node. In Xanthin+ permissions on nodes are
	 * relative to parent cathegories and type/cathegory type.
	 */
	function checkCurrentUserNodePermissionRecursively($nodeid,$action)
	{
	
	}
};


?>
