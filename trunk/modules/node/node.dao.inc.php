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


class xNodeDAO
{
	function xNodeDAO()
	{
		//non instaltiable
		assert(FALSE);
	}
	
	
	/**
	 * @access private
	 *
	 */
	function _linkToCathegories($nodeid,$cathegories)
	{
		$db =& xDB::getDB();
		foreach($cathegories as $cathegory)
		{
			if(! $db->query("INSERT INTO node_to_cathegory(nodeid,catid) VALUES(%d,%d)",
					$nodeid,$cathegory->m_id))
			{
				return false;
			}
		}
		
		return true;
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
		
		$id = xUniqueId::generate('node');
		
		$field_names = "id,type,author,content_filter,creation_time";
		$field_values = "%d,'%s','%s','%s',NOW()";
		$values = array($id,$node->m_type,$node->m_author,$node->m_content_filter);
		
		$db->query("INSERT INTO node($field_names) VALUES($field_values)",$values);
		
		//now cathegory link
		xNodeDAO::_linkToCathegories($id,$node->m_parent_cathegories);

		if(! $db->commitTransaction())
		{
			return false;
		}
		
		return $id;
	}
	
	/**
	 * Deletes a node.
	 * 
	 * @param int $nodeid
	 * @static
	 */
	function delete($nodeid)
	{
		$db =& xDB::getDB();
		if(! $db->query("DELETE FROM node WHERE id = %d",$nodeid))
			return false;
		
		//automatic node_to_cathegory deletion
		
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
			
		$fields = "content_filter = '%s',edit_time = NOW()";
		$values = array($node->m_content_filter,$node->m_cathegory);
		
		$values[] = $node->m_id;
		$db->query("UPDATE node SET $fields WHERE id = %d",$values);
		
		//now cathegories
		$db->query("DELETE FROM node_to_cathegory WHERE nodeid = %d",$node->m_id);
		
		xNodeDAO::_linkToCathegories($node->m_id,$node->m_cathegories);
		
		if(!$db->commitTransaction())
			return false;
		
		return true;
	}
	
	/**
	 * @return string NULL on error
	 */
	function getNodeTypeById($id)
	{
		$db =& xDB::getDB();
		$result = $db->query("SELECT type FROM node WHERE id = %d",$id);
		if($row = $db->fetchObject($result))
		{
			return $row->type;
		}
		return NULL;
	}
	
	/**
	 *
	 * @return xNode
	 * @static
	 * @access private
	 */
	function _nodeFromRow($row_object,$cathegories)
	{
		return new xNode($row_object->id,$row_object->type,$row_object->author,
			$row_object->content_filter,$cathegories,
			$db->decodeTimestamp($row_object->creation_time),$db->decodeTimestamp($row_object->edit_time));
	}
	
	
	/**
	 * Retrieve all nodes
	 *
	 * @return xNode
	 * @static
	 */
	function find($order,$limit,$id,$type,$author,$parent_cat)
	{
		$db =& xDB::getDB();
		$where[0]["clause"] = "node.id = %d";
		$where[0]["connector"] = "AND";
		$where[0]["value"] = $id;
	 
		$where[1]["clause"] = "node.type = '%s'";
		$where[1]["connector"] = "AND";
		$where[1]["value"] = $type;
		
		$where[2]["clause"] = "node.author = '%s'";
		$where[2]["connector"] = "AND";
		$where[2]["value"] = $author;
		
		$where[3]["clause"] = "node_to_cathegory.catid = %d";
		$where[3]["connector"] = "AND";
		$where[3]["value"] = $parent_cat;
		
		$where[4]["clause"] = "node.id = node_to_cathegory.nodeid";
		$where[4]["connector"] = "AND";
		if($parent_cat === NULL)
			$where[4]["value"] = NULL;
		
		$result = $db->autoQuerySelect('node.*','node,node_to_cathegory',$where,$order,$limit);
		$objs = array();
		while($row = $db->fetchObject($result))
			$objs[] = xNodeDAO::_nodeFromRow($row,NULL);
		return $objs;
	}
};





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
			$db->decodeTimestamp($row_object->creation_time),$db->decodeTimestamp($row_object->edit_time));
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
		$db =& xDB::getDB();
		
		return new xNodePage($row_object->id,$row_object->type,
			$row_object->author,$row_object->content_filter,$row_object->title,$row_object->content,
			$row_object->lang,$row_object->translator,$cathegories,
			$db->decodeTimestamp($row_object->creation_time),$db->decodeTimestamp($row_object->edit_time),
			$row_object->published,$row_object->sticky,$row_object->accept_replies,$row_object->approved,
			$row_object->meta_description,$row_object->meta_keywords);
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
			
			$result = $db->autoQuerySelect('node.*,node_i18n.*,node_page.*',
				'node,node_i18n,node_to_cathegory,node_page',$where,$order,$limit);
			$objs = array();
			while($row = $db->fetchObject($result))
				$objs[] = xNodePageDAO::_nodepageFromRow($row,NULL);
		
			return $objs;
		}
	}
}



class xNodeTypeDAO
{
	function xNodeTypeDAO()
	{
		//non instaltiable
		assert(FALSE);
	}
	
	/**
	 * Insert a new node type
	 *
	 * @param xNodeType $node_type
	 * @return bool FALSE on error
	 * @static
	 */
	function insert($node_type)
	{
		$db =& xDB::getDB();
		return $db->query("INSERT INTO node_and_cathegory_type (name,description) 
			VALUES ('%s','%s')",$node_type->m_name,$node_type->m_description);
	}
	
	/**
	 * Deletes an item type.
	 *
	 * 
	 * @param string $typename
	 * @return bool FALSE on error
	 * @static
	 */
	function delete($typename)
	{
		$db =& xDB::getDB();
		return $db->query("DELETE FROM node_and_cathegory_type WHERE name = '%s'",$typename);
	}
	
	/**
	 * Updates an item type.
	 *
	 * 
	 * @param xNodeType $node_type
	 * @return bool FALSE on error
	 * @static
	 */
	function update($node_type)
	{
		$db =& xDB::getDB();
		return $db->query("UPDATE node_and_cathegory_type SET description = '%s' WHERE name = '%s'",
			$node_type->m_description,$node_type->m_name);
	}
	
	/**
	 *
	 * @return xNodeType
	 * @static
	 * @access private
	 */
	function _itemtypeFromRow($row_object)
	{
		return new xNodeType($row_object->name,$row_object->description);
	}
	
	
	/**
	 * Load an Item type from db.
	 *
	 * @return xNodeType
	 * @static
	 */
	function load($typename)
	{
		$db =& xDB::getDB();
		$result = $db->query("SELECT * FROM node_and_cathegory_type WHERE name = '%s'",$typename);
		if($row = $db->fetchObject($result))
		{
			return xNodeTypeDAO::_itemtypeFromRow($row);
		}
		
		return NULL;
	}
	
	/**
	 * Retrieves all item type.
	 *
	 * @return array(xItemType)
	 * @static
	 */
	function findAll()
	{
		$db =& xDB::getDB();
		$types = array();
		$result = $db->query("SELECT * FROM node_and_cathegory_type");
		while($row = $db->fetchObject($result))
		{
			$types[] = xNodeTypeDAO::_itemtypeFromRow($row);
		}
		
		return $types;
	}
}

?>