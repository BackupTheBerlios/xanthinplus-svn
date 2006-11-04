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
			$row_object->creation_time,$row_object->edit_time);
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

?>