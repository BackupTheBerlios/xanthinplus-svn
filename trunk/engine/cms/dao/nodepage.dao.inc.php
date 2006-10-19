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


class xNodePageDAO
{
	function xNodePageDAO()
	{
		//non instaltiable
		assert(FALSE);
	}
	
	/**
	 * Insert a new Node page
	 *
	 * @param xNodePage $item
	 * @return int The new id or FALSE on error
	 * @static
	 */
	function insert($node)
	{
		xDB::getDB()->startTransaction();
		
		$id = xNodeI18NDAO::insert($node);
		
		xDB::getDB()->query("INSERT INTO node_page(nodeid,lang,sticky,accept_replies,published,approved,
			meta_description,meta_keywords) VALUES (%d,'%s',%d,%d,%d,%d,'%s','%s')",
			$id,$node->m_lang,$node->m_published,$node->m_sticky,$node->m_accept_replies,$node->m_published,
			$node->m_approved,$node->m_meta_description,$node->m_meta_keywords);
			
		if(!xDB::getDB()->commitTransaction())
			return false;
		
		return $id;
	}
	
	/**
	 * Insert a new Node page
	 *
	 * @param xNodePage $item
	 * @return int The new id or FALSE on error
	 * @static
	 */
	function insertTranslation($node)
	{
		xDB::getDB()->startTransaction();
		
		xNodeI18NDAO::insertTranslation($node);
		
		xDB::getDB()->query("INSERT INTO node_page(nodeid,lang,sticky,accept_replies,published,approved,
			meta_description,meta_keywords) VALUES (%d,%d,%d,%d,%d,'%s','%s')",
			$node->m_id,$node->m_lang,$node->m_published,$node->m_sticky,$node->m_accept_replies,$node->m_published,
			$node->m_approved,$node->m_meta_description,$node->m_meta_keywords);
			
		if(!xDB::getDB()->commitTransaction())
			return false;
		
		return $id;
	}
	
	/**
	 * Updates an node.
	 *
	 * 
	 * @param xnodePage $node
	 * @return bool FALSE on error
	 * @static
	 */
	function update($node)
	{
		xDB::getDB()->startTransaction();
		
		xNodeI18NDAO::update($node);
		
		xDB::getDB()->query("UPDATE node_page SET published = %d,sticky = %d,accept_replies = %d,published = %d,
			approved = %d,meta_description = '%s',meta_keywords = '%s' WHERE nodeid = %d AND lang = '%s'",
			$node->m_published,$node->m_sticky,$node->m_accept_replies,$node->m_published,
			$node->m_approved,$node->m_meta_description,$node->m_meta_keywords,$node->m_id,$node->m_lang);
			
		if(!xDB::getDB()->commitTransaction())
			return false;
		
		return true;
	}
	
	
	/**
	 *
	 * @return xnodePage
	 * @static
	 * @access private
	 */
	function _nodepageFromRow($row_object,$cathegories)
	{
		return new xNodePage($row_object->id,$row_object->type,
			$row_object->author,$row_object->content_filter,$row_object->title,$row_object->content,$cathegories,
			$row_object->creation_time,$row_object->edit_time,$row_object->published,$row_object->sticky,
			$row_object->accept_replies,
			$row_object->approved,$row_object->meta_description,$row_object->meta_keywords);
	}
	
	/**
	 * Retrieve a specific Node page
	 *
	 * @return xNodePage
	 * @static
	 */
	function load($id,$lang)
	{
		$result = xDB::getDB()->query("SELECT * FROM node,node_i18n,node_page WHERE 
			node.id = %d AND node_i18n.nodeid = node.id AND node_i18n.lang = '%s' AND 
			node_page.nodeid = node_i18n.nodeid",$id,$lang);
		if($row = xDB::getDB()->fetchObject($result))
		{
			return xNodePageDAO::_nodepageFromRow($row,xCathegoryDAO::findNodeCathegories($id));
		}
		
		return NULL;
	}
}

?>