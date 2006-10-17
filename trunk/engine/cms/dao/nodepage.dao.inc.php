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
	function insert($node,$transaction = TRUE)
	{
		if($transaction)
			xDB::getDB()->startTransaction();
		
		$id = xNodeDAO::insert($node,FALSE);
		if($id == FALSE)
			return false;
		
		if(! xDB::getDB()->query("INSERT INTO Node_page(nodeid,sticky,accept_replies,published,approved,
			meta_description,meta_keywords) VALUES (%d,%d,%d,%d,%d,'%s','%s')",
			$id,$node->m_published,$node->m_sticky,$node->m_accept_replies,$node->m_published,
			$node->m_approved,$node->m_meta_description,$node->m_meta_keywords))
			return false;
			
		
		if($transaction)
			xDB::getDB()->commit();
		
		return $id;
	}
	
	/**
	 * Deletes an node and all its replies.
	 * 
	 * @param int $nodeid
	 * @return bool FALSE on error
	 * @static
	 */
	function delete($nodeid,$transaction = true)
	{
		return xNodeDAO::delete($nodeid,$transaction);
	}
	
	/**
	 * Updates an node.
	 *
	 * 
	 * @param xnodePage $node
	 * @return bool FALSE on error
	 * @static
	 */
	function update($node,$transaction = true)
	{
		if($transaction)
			xDB::getDB()->startTransaction();
		
		if(! xNodeDAO::update($node,false))
			return false;
		
		if(! xDB::getDB()->query("UPDATE node_page SET published = %d,sticky = %d,accept_replies = %d,published = %d,
			approved = %d,meta_description = '%s',meta_keywords = '%s' WHERE nodeid = %d",
			$node->m_published,$node->m_sticky,$node->m_accept_replies,$node->m_published,
			$node->m_approved,$node->m_meta_description,$node->m_meta_keywords,$node->m_id))
			return false;
			
		if($transaction)
			xDB::getDB()->commit();
		
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
		return new xNodePage($row_object->id,$row_object->title,$row_object->type,
			$row_object->author,$row_object->content,$row_object->content_filter,$cathegories,
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
	function load($id)
	{
		$result = xDB::getDB()->query("SELECT * FROM node,node_page WHERE 
			node.id = %d AND node_page.nodeid = node.id",$id);
		if($row = xDB::getDB()->fetchObject($result))
		{
			return xNodePageDAO::_nodepageFromRow($row,xCathegoryDAO::findNodeCathegories($id));
		}
		
		return NULL;
	}
	
	
	/**
	 *
	 * @static
	 */
	function find($title = NULL,$author = NULL,
		$parent_cathegory = NULL,$creation_time = NULL, $edit_time = NULL, $published, 
		$sticky = NULL, $accept_replies = NULL, $approved = NULL, $meta_description = NULL,$meta_keywords = NULL, 
		$inf_limit = 0,$sup_limit = 0,$order_by = 0)
	{
		assert($inf_limit >= 0 && $sup_limit >= 0 && $inf_limit <= $sup_limit);
		
		$query_tables = array("node","node_page");
		$query_where = array('node.id = node_page.nodeid');
		$query_where_link = array('AND');
		$order = '';
		$values = array();
		
		if($title !== NULL)
		{
			$query_where[] = "node.title LIKE '%s'";
			$query_where_link[] = "AND";
			$values[] = $title;
		}
		
		if($author !== NULL)
		{
			$query_where[] = "node.author = '%s'";
			$query_where_link[] = "AND";
			$values[] = $author;
		}
		
		if($parent_cathegory !== NULL)
		{
			$query_tables[] = 'node_to_cathegory';
			$query_where[] = "node_to_cathegory.nodeid = node.id AND node_to_cathegory.catid = %d";
			$query_where_link[] = "AND";
			$values[] = $parent_cathegory;
		}
		
		if($parent_cathegory !== NULL)
		{
			$query_where[] = "cathegory.parent_cathegory = '%d'";
			$query_where_link[] = "AND";
			$values[] = $parent_cathegory;
		}
		
		if($inf_limit != 0 || $sup_limit != 0)
		{
			$query_where[] .= "LIMIT %d,%d";
			$query_where_link[] = "";
			$values[] = $inf_limit;
			$values[] = $sup_limit;
		}
		
		//now construct the query
		$query = "SELECT * FROM ";
		$i = 0;
		foreach($query_tables as $query_table)
		{
			if($i === 0) //not adding link string
			{
				$query .= $query_table;
			}
			else
			{
				$query .= "," . $query_table;
			}
			$i++;
		}
		
		$query .= " ";
		for($i = 0;$i < count($query_where);$i++)
		{
			if($i === 0) //not adding link string
			{
				$query .= "WHERE ";
				$query .= $query_where[$i];
			}
			else
			{
				$query .= " " . $query_where_link[$i] . " ";
				$query .= $query_where[$i];
			}
		}
		$result = xDB::getDB()->query($query,$values);
		
		$cats = array();
		while($row = xDB::getDB()->fetchObject($result))
		{
			$cats[] = xCathegoryDAO::_cathegoryFromRow($row);
		}
		return $cats;
	}
	
}

?>