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


class xNodeI18NDAO
{
	function xNodeI18NDAO()
	{
		//non instaltiable
		assert(FALSE);
	}
	
	/**
	 * Insert a new node
	 *
	 * @param xNode $node
	 * @return int The new id or FALSE on error
	 * @static
	 */
	function insert($node)
	{
		xDB::getDB()->startTransaction();
		
		$id = xNodeDAO::insert($node);
		
		xDB::getDB()->query("INSERT INTO node_i18n(nodeid,title,content,lang) VALUES(%d,'%s','%s','%s')",
			$id, $node->m_title, $node->m_content, $node->m_lang);

		if(! xDB::getDB()->commitTransaction())
			return false;
		
		return $id;
	}
	
	/**
	 * Insert a new node
	 *
	 * @param xNode $node
	 * @return int The new id or FALSE on error
	 * @static
	 */
	function insertTranslation($node)
	{
		return xDB::getDB()->query("INSERT INTO node_i18n(nodeid,title,content,lang) VALUES(%d,'%s','%s,'%s')",
			$id, $node->m_title, $node->m_content, $node->m_lang);
	}
	
	/**
	 * Insert a new node
	 *
	 * @param xNode $node
	 * @return int The new id or FALSE on error
	 * @static
	 */
	function deleteTranslation($id,$lang)
	{
		return xDB::getDB()->query("DELETE FROM node_i18n WHERE nodeid = %d AND lang = '%s'",
			$id,$lang);
	}
	
	/**
	 * Updates a node.
	 *
	 * 
	 * @param xNode $node
	 * @return bool FALSE on error
	 * @static
	 */
	function update($node)
	{
		xDB::getDB()->startTransaction();
			
		xNode::update($node);
		
		$fields = "title = '%s',content = NOW()";
		$values = array($node->m_title,$node->m_content);
		
		$values[] = $node->m_id;
		$values[] = $node->m_lang;
		xDB::getDB()->query("UPDATE node_i18n SET $fields WHERE nodeid = %d AND lang = '%s'",$values);
		
		if(!xDB::getDB()->commitTransaction())
			return false;
		
		return true;
	}
	
	/**
	 *
	 * @return xNode
	 * @static
	 * @access private
	 */
	function _nodei18nFromRow($row_object,$cathegories)
	{
		return new xNodeI18N($row_object->id,$row_object->type,$row_object->author,
			$row_object->content_filter,$row_object->title,$row_object->content,$cathegories,
			$row_object->creation_time,$row_object->edit_time);
	}
	
	/**
	 * Retrieve a specific node
	 *
	 * @return xNode
	 * @static
	 */
	function load($id,$lang)
	{
		$result = xDB::getDB()->query("SELECT * FROM node,node_i18n WHERE node.id = %d AND node_i18n.nodeid = node.id AND 
			node_i18n.lang = '%s'",$id,$lang);
		if($row = xDB::getDB()->fetchObject($result))
		{
			return xNodeI18NDAO::_nodei18nFromRow($row,xCathegoryDAO::findNodeCathegories($id));
		}
		return NULL;
	}
	
	
	/**
	 * Retrieve all nodes
	 *
	 * @return xNode
	 * @static
	 */
	function findAll($lang)
	{
		$nodes = array();
		$result = xDB::getDB()->query("SELECT * FROM node,node_i18n WHERE node_i18n.nodeid = node.id AND 
			node_i18n.lang = '%s'");
		while($row = xDB::getDB()->fetchObject($result))
		{
			$nodes[] = xNodeI18NDAO::_nodei18nFromRow($row,xCathegoryDAO::findNodeCathegories($id));
		}
		
		return $nodes;
	}
	
	/**
	 *
	 */
	function getNodeTranslations($nodeid,$exclude_lang)
	{
		$langs = array();
		$result = xDB::getDB()->query("SELECT lang FROM node_i18n WHERE node_i18n.nodeid = %d AND 
			node_i18n.lang <> '%s'",$nodeid,$exclude_lang);
		while($row = xDB::getDB()->fetchObject($result))
		{
			$langs[] = $row->lang;
		}
		
		return $langs;
	}
	
};

?>