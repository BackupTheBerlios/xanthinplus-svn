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
		foreach($cathegories as $cathegory)
		{
			if(! xDB::getDB()->query("INSERT INTO node_to_cathegory(nodeid,catid) VALUES(%d,%d)",
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
	function insert($node,$transaction = TRUE)
	{
		if($transaction)
			xDB::getDB()->startTransaction();
		
		$id = xUniqueId::generate('node');

		$field_names = "id,title,type,author,content,content_filter,creation_time";
		$field_values = "%d,'%s','%s','%s','%s','%s',NOW()";
		$values = array($id,$node->m_title,$node->m_type,$node->m_author,$node->m_content,
			$node->m_content_filter);
		
		
		if(! xDB::getDB()->query("INSERT INTO node($field_names) VALUES($field_values)",$values))
			return false;
		
		//now cathegory link
		if(! xNodeDAO::_linkToCathegories($id,$node->m_parent_cathegories))
			return false;

		if($transaction)
			xDB::getDB()->commit();
		
		return $id;
	}
	
	/**
	 * Deletes a node.
	 * 
	 * @param int $nodeid
	 * @static
	 */
	function delete($nodeid,$transaction = true)
	{
		if($transaction)
			xDB::getDB()->startTransaction();
		
		if(! xDB::getDB()->query("DELETE FROM node WHERE id = %d",$nodeid))
			return false;
		
		//automatic node_to_cathegory deletion
		
		if($transaction)
			xDB::getDB()->commit();
		
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
	function update($node,$transaction = true)
	{
		if($transaction)
			xDB::getDB()->startTransaction();
			
		$fields = "title = '%s',content = '%s',content_filter = '%s',edit_time = NOW()";
		$values = array($node->m_title,$node->m_content,$node->m_content_filter,
			$node->m_cathegory);
		
		$values[] = $node->m_id;
		if(! xDB::getDB()->query("UPDATE node SET $fields WHERE id = %d",$values))
			return false;
		
		//now cathegories
		if(! xDB::getDB()->query("DELETE FROM node_to_cathegory WHERE nodeid = %d",$node->m_id))
			return false;
		if(! xNodeDAO::_linkToCathegories($node->m_id,$node->m_cathegories))
			return false;
		
		if($transaction)
			xDB::getDB()->commit();
		
		return true;
	}
	
	/**
	 * @return string NULL on error
	 */
	function getNodeTypeById($id)
	{
		$result = xDB::getDB()->query("SELECT type FROM node WHERE id = %d",$id);
		if($row = xDB::getDB()->fetchObject($result))
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
		return new xNode($row_object->id,$row_object->title,$row_object->type,$row_object->author,
			$row_object->content,$row_object->content_filter,$cathegories,
			$row_object->creation_time,$row_object->edit_time);
	}
	
	/**
	 * Retrieve a specific item
	 *
	 * @return xNode
	 * @static
	 */
	function load($id)
	{
		$result = xDB::getDB()->query("SELECT * FROM node WHERE id = %d",$id);
		if($row = xDB::getDB()->fetchObject($result))
		{
			return xNodeDAO::_nodeFromRow($row,xCathegoryDAO::findNodeCathegories($id));
		}
		return NULL;
	}
	
};

?>