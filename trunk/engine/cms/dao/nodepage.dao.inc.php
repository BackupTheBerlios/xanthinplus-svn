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
		$db =& xDB::getDB();
		$db->startTransaction();
		
		$id = xNodeI18NDAO::insert($node);
		
		$db->query("INSERT INTO node_page(nodeid,lang,sticky,accept_replies,published,approved,
			meta_description,meta_keywords) VALUES (%d,'%s',%d,%d,%d,%d,'%s','%s')",
			$id,$node->m_lang,$node->m_sticky,$node->m_accept_replies,$node->m_published,
			$node->m_approved,$node->m_meta_description,$node->m_meta_keywords);
			
		if(!$db->commitTransaction())
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
		$db =& xDB::getDB();
		$db->startTransaction();
		
		xNodeI18NDAO::insertTranslation($node);
		
		$db->query("INSERT INTO node_page(nodeid,lang,sticky,accept_replies,published,approved,
			meta_description,meta_keywords) VALUES (%d,'%s',%d,%d,%d,%d,'%s','%s')",
			$node->m_id,$node->m_lang,$node->m_published,$node->m_sticky,$node->m_accept_replies,$node->m_published,
			$node->m_approved,$node->m_meta_description,$node->m_meta_keywords);
			
		if(!$db->commitTransaction())
			return false;
		
		return true;
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
		$db =& xDB::getDB();
		$db->startTransaction();
		
		xNodeI18NDAO::update($node);
		
		$db->query("UPDATE node_page SET published = %d,sticky = %d,accept_replies = %d,published = %d,
			approved = %d,meta_description = '%s',meta_keywords = '%s' WHERE nodeid = %d AND lang = '%s'",
			$node->m_published,$node->m_sticky,$node->m_accept_replies,$node->m_published,
			$node->m_approved,$node->m_meta_description,$node->m_meta_keywords,$node->m_id,$node->m_lang);
			
		if(!$db->commitTransaction())
			return false;
		
		return true;
	}
	
	/**
	 * 
	 *
	 * @param xNode $node
	 * @return The new id or FALSE on error
	 * @static
	 */
	function updateTranslation($node)
	{
		$db =& xDB::getDB();
		$db->startTransaction();
		
		xNodeI18NDAO::updateTranslation($node);
		
		$db->query("UPDATE node_page SET meta_description = '%s',meta_keywords = '%s' 
			WHERE nodeid = %d AND lang = '%s'",
			$node->m_meta_description, $node->m_meta_keywords,$node->m_id, $node->m_lang);
			
		if(!$db->commitTransaction())
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
			$row_object->author,$row_object->content_filter,$row_object->title,$row_object->content,$row_object->lang,
			$row_object->translator,$cathegories,
			$row_object->creation_time,$row_object->edit_time,$row_object->published,$row_object->sticky,
			$row_object->accept_replies,
			$row_object->approved,$row_object->meta_description,$row_object->meta_keywords);
	}
	
	/**
	 * @static
	 */
	function isNodePage($id)
	{
		$db =& xDB::getDB();
		$result = $db->query("SELECT nodeid FROM node_page WHERE 
			nodeid = %d",$id);
		if($row = $db->fetchObject($result))
		{
			return true;
		}
		
		return false;
	}
	
	/**
	 * 
	 */
	function find($order,$limit,$id,$type,$author,$parent_cat,$lang,$flexible_lang,$translator)
	{
		$db =& xDB::getDB();
		if($flexible_lang && $lang !== NULL)
		{
			//now extract all nodes with all languages
			$objs = xNodePageDAO::find($order,$limit,$id,$type,$author,$parent_cat,NULL,FALSE,$translator);
			return xNodeI18NDAO::_selectFlexiLang($objs,$lang);
		}
		else
		{
			$i = 0;
			$where[$i]["clause"] = "node_page.nodeid = %d";
			$where[$i]["connector"] = "AND";
			$where[$i]["value"] = $id;
			
			$i++;
			$where[$i]["clause"] = "node_page.lang = '%s'";
			$where[$i]["connector"] = "AND";
			$where[$i]["value"] = $lang;
			
			$i++;
			$where[$i]["clause"] = "node_i18n.nodeid = node_page.nodeid";
			$where[$i]["connector"] = "AND";
			
			$i++;
			$where[$i]["clause"] = "node_i18n.lang = node_page.lang";
			$where[$i]["connector"] = "AND";
			
			$i++;
			$where[$i]["clause"] = "node_i18n.translator = '%s'";
			$where[$i]["connector"] = "AND";
			$where[$i]["value"] = $translator;
		 
		 	$i++;
		 	$where[$i]["clause"] = "node.id = node_i18n.nodeid";
			$where[$i]["connector"] = "AND";
		 	
		 	$i++;
			$where[$i]["clause"] = "node.type = '%s'";
			$where[$i]["connector"] = "AND";
			$where[$i]["value"] = $type;
			
			$i++;
			$where[$i]["clause"] = "node.author = '%s'";
			$where[$i]["connector"] = "AND";
			$where[$i]["value"] = $author;
			
			$i++;
			$where[$i]["clause"] = "node_to_cathegory.catid = %d";
			$where[$i]["connector"] = "AND";
			$where[$i]["value"] = $parent_cat;
			
			$i++;
			$where[$i]["clause"] = "node.id = node_to_cathegory.nodeid";
			$where[$i]["connector"] = "AND";
			if($parent_cat === NULL)
				$where[$i]["value"] = NULL;
			
			$result = $db->autoQuerySelect('node.*,node_i18n.*,node_page.*',
				'node,node_i18n,node_to_cathegory,node_page',$where,$order,$limit);
			$objs = array();
			while($row = $db->fetchObject($result))
				$objs[] = xNodePageDAO::_nodepageFromRow($row,NULL);
		
			return $objs;
		}
	}
}

?>