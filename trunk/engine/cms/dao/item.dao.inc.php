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
	 * @return int The new id or FALSE on error
	 * @static
	 */
	function insert($item,$transaction = TRUE)
	{
		if($transaction)
			xDB::getDB()->startTransaction();
		
		$id = xUniqueId::generate('item');
		
		if( !xDB::getDB()->query("INSERT INTO item(id,title,type,author,content,content_filter,creation_time) 
			VALUES (%d,'%s','%s','%s','%s','%s',NOW())",
			$id,$item->m_title,$item->m_type,$item->m_author,$item->m_content,$item->m_content_filter))
			return false;
		
		if($transaction)
			xDB::getDB()->commit();
		
		return $id;
	}
	
	/**
	 * Deletes an item and all its replies.
	 * 
	 * @param int $itemid
	 * @static
	 */
	function delete($itemid,$transaction = true)
	{
		if($transaction)
			xDB::getDB()->startTransaction();
		
		if(! xDB::getDB()->query("DELETE FROM item WHERE id = %d",$itemid))
			return false;
		
		if(! xDB::getDB()->query("DELETE FROM item_replies WHERE parentid = %d",$itemid))
			return false;
		
		if($transaction)
			xDB::getDB()->commit();
		
		return true;
	}
	
	/**
	 * Updates an item.
	 *
	 * 
	 * @param xItem $item
	 * @return bool FALSE on error
	 * @static
	 */
	function update($item)
	{
		return xDB::getDB()->query("UPDATE item SET title = '%s',content = '%s',content_filter = '%s',lastedittime = NOW()",
			$item->m_title,$item->m_content,$item->m_content_filter);
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
			$row_object->content,$row_object->content_filter,
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
	 * Insert an item in a list of cathegories
	 *
	 * @param int $itemid
	 * @param array(int) $cathegories_id
	 * @return bool FALSE on error
	 * @static
	 */
	function insertInCathegories($itemid,$cathegories_id,$transaction = TRUE)
	{
		if($transaction)
			xDB::getDB()->startTransaction();
		
		foreach($cathegories_id as $cathegory_id)
		{
			if(! xDB::getDB()->query("INSERT INTO item_to_cathegory (itemid,catid) VALUES (%d,%d)",$itemid,$cathegories_id))
				return false;
		}
		
		if($transaction)
			xDB::getDB()->commit();
		
		return true;
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
	 * @param string $title Like search
	 * @param string $author Exact search
	 * @param string $content Like search
	 * @param int $cathegory Exact search on category id
	 * @param int $nelementpage Number of elements per page
	 * @param int $npage Number of page (starting from 1).
	 * @return array(xItem)
	 * @static
	 */
	function find($title,$parentid,$author,$content,$cathegory,$nelementpage = 0,$npage = 0)
	{
		$items = array();
		
		$query_tables = array("item");
		$values = array();
		$query_where = array();
		$query_where_link = array();
		
		if($parentid !== NULL)
		{
			$query_tables[] = "item_replies";
			$query_where[] = "item_replies.parentid = %d AND item.id = item_replies.childid";
			$query_where_link[] = "AND";
			$values[] = $parentid;
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