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


class xItemDAO
{
	function xItemDAO()
	{
		//non instaltiable
		assert(FALSE);
	}
	
	/**
	 * Insert a new item
	 *
	 * @param xItem $item
	 * @return int The new id
	 * @static
	 */
	function insert($item)
	{
		xDB::getDB()->query("INSERT INTO item(title,type,author,content,content_filter,published,approved,
			accept_replies,sticky,weight,description,keywords,creation_time) 
			VALUES ('%s','%s','%s','%s','%s',%d,%d,%d,%d,%d,'%s','%s',NOW())",
			$item->m_title,$item->m_type,$item->m_author,$item->m_content,$item->m_content_filter,
			$item->m_published,$item->m_approved,$item->m_accept_replies,$item->m_sticky,
			$item->m_weight,$item->m_description,$item->m_keywords);
	}
	
	/**
	 * Deletes an item and all its replies.
	 * 
	 * @param int $itemid
	 * @static
	 */
	function delete($itemid)
	{
		xDB::getDB()->startTransaction();
		
		xDB::getDB()->query("DELETE FROM item WHERE id = %d",$itemid);
		xDB::getDB()->query("DELETE FROM item_replies WHERE parentid = %d",$itemid);
		
		xDB::getDB()->commit();
	}
	
	/**
	 * Updates an item.
	 *
	 * 
	 * @param xItem $item
	 * @static
	 */
	function update($item)
	{
		xDB::getDB()->query("UPDATE item SET title = '%s',content = '%s',content_filter = '%s',
			published = %d,approved = %d,accept_replies = %d,sticky = %d,weight = %d,
			description = '%s',keywords = '%s',lastedittime = NOW()",
			$item->m_title,$item->m_content,$item->m_content_filter,$item->m_published,
			$item->m_approved,$item->m_accept_replies,$item->m_sticky,$item->m_weight,
			$item->m_description,$item->m_keywords);
	}
	
	
	/**
	 *
	 * @return xItem
	 * @static
	 * @access private
	 */
	function _itemFromRow($row_object)
	{
		return new xItem($row_object->id,$row_object->title,$row_object->type,$row_object->author,
			$row_object->content,$row_object->content_filter,$row_object->published,
			$row_object->approved,$row_object->accept_replies,$row_object->sticky,
			$row_object->weight,$row_object->description,$row_object->keywords,
			$row_object->creation_time,$row_object->lastedit_time);
	}
	
	/**
	 * Retrieve a specific item
	 *
	 * @return xItem
	 * @static
	 */
	function load($id)
	{
		$result = xDB::getDB()->query("SELECT * FROM item WHERE id = %d",$id);
		if($row = xDB::getDB()->fetchObject($result))
		{
			return xItemDAO::_itemFromRow($row);
		}
		
		return NULL;
	}
	
	/**
	 * Retrieves all replies associated with an items.
	 *
	 * @param int $parentid
	 * @param int $nelementpage Number of elements per page
	 * @param int $npage Number of page (starting from 1).
	 * @return array(xItem)
	 * @static
	 */
	function findReplies($parentid,$nelementpage = 0,$npage = 0)
	{
		$items = array();
		$query = "SELECT * FROM item,item_replies WHERE item_replies.parentid = %d AND item.id = item_replies.childid";
		$values = array($parentid);
		
		if($npage != 0)
		{
			$query .= " LIMIT %d,%d";
			$values[] = ($npage - 1) * $nelementpage;
			$values[] = $nelementpage;
		}
		
		$result = xDB::getDB()->query($query,$values);
		while($row = xDB::getDB()->fetchObject($result))
		{
			$items[] = xItemDAO::_itemFromRow($row);
		}
		return $items;
	}
	
	
	/**
	 * Retrieves all items.
	 *
	 * @param string $type Exact search
	 * @param string $title Like search
	 * @param string $author Exact search
	 * @param string $content Like search
	 * @param bool $published
	 * @param bool $approved
	 * @param int $cathegory Exact search on category id
	 * @param int $nelementpage Number of elements per page
	 * @param int $npage Number of page (starting from 1).
	 * @return array(xItem)
	 * @static
	 */
	function find($type,$title,$author,$content,$published,$approved,$cathegory,$nelementpage = 0,$npage = 0)
	{
		$items = array();
		
		$query_tables = array("item");
		$values = array();
		$query_where = array();
		$query_where_link = array();
		
		if($type !== NULL)
		{
			$query_where[] = "item.type = '%s'";
			$query_where_link[] = "AND";
			$values[] = $type;
		}
		
		if($title !== NULL)
		{
			$query_where[] = "item.title LIKE '%s'";
			$query_where_link[] = "AND";
			$values[] = $title;
		}
		
		if($author !== NULL)
		{
			$query_where[] = "item.author = '%s'";
			$query_where_link[] = "AND";
			$values[] = $author;
		}
		
		if($content !== NULL)
		{
			$query_where[] = "item.content LIKE '%s'";
			$query_where_link[] = "AND";
			$values[] = $content;
		}
		
		if($published !== NULL)
		{
			$query_where[] = "item.published = %d";
			$query_where_link[] = "AND";
			$values[] = $published;
		}
		
		if($approved !== NULL)
		{
			$query_where[] = "item.approved = %d";
			$query_where_link[] = "AND";
			$values[] = $approved;
		}
		
		if($cathegory !== NULL)
		{
			$query_tables[] = "item_to_cathegory";
			$query_where[] = "item_to_cathegory.catid = %d AND item.id = item_to_cathegory.itemid";
			$query_where_link[] = "AND";
			$values[] = $cathegory;
		}
		
		if($npage != 0)
		{
			$query_where[] .= "LIMIT %d,%d";
			$query_where_link[] = "";
			$values[] = ($npage - 1) * $nelementpage;
			$values[] = $nelementpage;
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
		while($row = xDB::getDB()->fetchObject($result))
		{
			$items[] = xItemDAO::_itemFromRow($row);
		}
		return $items;
	}
	
	
	
};

?>