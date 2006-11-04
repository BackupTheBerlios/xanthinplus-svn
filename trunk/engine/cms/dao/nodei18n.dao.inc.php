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
		$db =& xDB::getDB();
		$db->startTransaction();
		
		$id = xNodeDAO::insert($node);
		
		$db->query("INSERT INTO node_i18n(nodeid,title,content,lang,translator) 
			VALUES(%d,'%s','%s','%s','%s')",
			$id, $node->m_title, $node->m_content, $node->m_lang,$node->m_translator);

		if(! $db->commitTransaction())
			return false;
		
		return $id;
	}
	
	/**
	 * Insert a new node
	 *
	 * @param xNode $node
	 * @return The new id or FALSE on error
	 * @static
	 */
	function insertTranslation($node)
	{
		$db =& xDB::getDB();
		return $db->query("INSERT INTO node_i18n(nodeid,title,content,lang,translator) 
			VALUES(%d,'%s','%s','%s','%s')",
			$node->m_id, $node->m_title, $node->m_content, $node->m_lang, $node->m_translator);
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
		$db =& xDB::getDB();
		$db->startTransaction();
		
		$db->query("DELETE FROM node_i18n WHERE nodeid = %d AND lang = '%s'",
			$id,$lang);
			
		$result = $db->query("SELECT nodeid FROM node_i18n WHERE node_i18n.nodeid = %d",$id);
		if(! $db->fetchObject($result))
		{
			xNodeDAO::delete($id);
		}
		
		if(! $db->commitTransaction())
			return false;
		
		return true;
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
		$db =& xDB::getDB();
		$db->startTransaction();
			
		xNode::update($node);
		
		$fields = "title = '%s',content = NOW()";
		$values = array($node->m_title,$node->m_content);
		
		$values[] = $node->m_id;
		$values[] = $node->m_lang;
		$db->query("UPDATE node_i18n SET $fields WHERE nodeid = %d AND lang = '%s'",$values);
		
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
		return $db->query("UPDATE node_i18n SET title = '%s',content = '%s' WHERE nodeid = %d AND lang = '%s'",
			$node->m_title, $node->m_content,$node->m_id, $node->m_lang);
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
			$row_object->content_filter,$row_object->title,$row_object->content,$row_object->m_lang,
			$row_object->translator,$cathegories,
			$row_object->creation_time,$row_object->edit_time);
	}
	
	
	/**
	 * @acces protected
	 */
	function _selectFlexiLang($nodes,$lang)
	{
		$db =& xDB::getDB();
		//now group by name and lang
		$grouped = array();
		foreach($nodes as $node)
			$grouped[$node->m_id][$node->m_lang] = $node;
			
		$ret = array();
		
		//extract nodes
		foreach($grouped as $id => $ignore)
		{
			if(isset($grouped[$id][$lang])) //specific lang
				$ret[] = $grouped[$id][$lang];
			elseif(isset($grouped[$id][xSettings::get('default_lang')])) //default lang
				$ret[] = $grouped[$id][xSettings::get('default_lang')];
			else	//first found lang
				$ret[] = reset($grouped[$id]);
		}
		
		return $ret;
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
			$objs = xNodeI18NDAO::find($order,$limit,$id,$type,$author,$parent_cat,NULL,FALSE,$translator);
			return xNodeI18NDAO::_selectFlexiLang($objs,$lang);
		}
		else
		{
			$i = 0;
			$where[$i]["clause"] = "node_i18n.nodeid = %d";
			$where[$i]["connector"] = "AND";
			$where[$i]["value"] = $id;
			
			$i++;
			$where[$i]["clause"] = "node_i18n.lang = '%s'";
			$where[$i]["connector"] = "AND";
			$where[$i]["value"] = $lang;
			
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
			
			$result = $db->autoQuerySelect('node.*,node_i18n.*','node,node_i18n,node_to_cathegory',
				$where,$order,$limit);
			$objs = array();
			while($row = $db->fetchObject($result))
				$objs[] = xNodeI18NDAO::_nodei18nFromRow($row,NULL);
			return $objs;
		}
	}
	
	
	/**
	 *
	 */
	function getNodeTranslations($nodeid)
	{
		$db =& xDB::getDB();
		$langs = array();
		$result = $db->query("SELECT language.name,language.full_name FROM node_i18n,language WHERE 
			node_i18n.nodeid = %d AND language.name = node_i18n.lang",$nodeid);
		while($row = $db->fetchObject($result))
		{
			$langs[] = xLanguageDAO::_languageFromRow($row);
		}
		
		return $langs;
	}
	
	
	/**
	 *
	 */
	function isTranslatable($nodeid)
	{
		$db =& xDB::getDB();
		$result = $db->query("SELECT lang FROM node_i18n WHERE node_i18n.nodeid = %d",$nodeid);
		if($row = $db->fetchObject($result))
			return true;
		
		return false;
	}
	
	
	/**
	 *
	 */
	function existsTranslation($nodeid,$lang)
	{
		$db =& xDB::getDB();
		$result = $db->query("SELECT lang FROM node_i18n WHERE node_i18n.nodeid = %d AND 
			node_i18n.lang = '%s'",$nodeid,$lang);
		if($row = $db->fetchObject($result))
			return true;
		
		return false;
	}

	
};

?>