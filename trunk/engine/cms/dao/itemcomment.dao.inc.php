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


class xItemCommentDAO
{
	function xItemCommentDAO()
	{
		//non instaltiable
		assert(FALSE);
	}
	
	/**
	 * Insert a new item Comment
	 *
	 * @param xItemComment $item
	 * @return int The new id or FALSE on error
	 * @static
	 */
	function insert($item,$transaction = true)
	{
		return xItemDAO::insert($item,$transaction);
	}
	
	/**
	 * Deletes an item and all its replies.
	 * 
	 * @param int $itemid
	 * @return bool FALSE on error
	 * @static
	 */
	function delete($itemid,$transaction = true)
	{
		return xItemDAO::delete($itemid,$transaction);
	}
	
	/**
	 * Updates an item.
	 *
	 * 
	 * @param xItemComment $item
	 * @return bool FALSE on error
	 * @static
	 */
	function update($item)
	{
		return xItemDAO::update($item);
	}
	
	
	/**
	 *
	 * @return xItemComment
	 * @static
	 * @access private
	 */
	function _itemcommentFromRow($row_object)
	{
		return new xItemComment($row_object->id,$row_object->title,$row_object->type,$row_object->author,
			$row_object->content,$row_object->content_filter,$row_object->creation_time,$row_object->lastedit_time);
	}
	
	/**
	 *
	 */
	function toSpecificItem($item)
	{
		return new xItemComment($item->m_id,$item->m_title,$item->m_type,$item->m_author,
			$item->m_content,$item->m_ontent_filter,$item->m_creation_time,$item->m_lastedit_time);
	}
	
	/**
	 * Retrieve a specific item page
	 *
	 * @return xItemComment
	 * @static
	 */
	function load($id)
	{
		$result = xDB::getDB()->query("SELECT * FROM item WHERE item.id = %d AND item.type = '%s' AND 
			item_page.itemid = item.id",$id,'comment');
		if($row = xDB::getDB()->fetchObject($result))
		{
			return xItemPageDAO::_itemcommentFromRow($row);
		}
		
		return NULL;
	}
	
	
	/**
	 * Retrieves all item comments.
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
	function find($parentid,$title,$author,$content,$cathegory,$nelementpage = 0,$npage = 0)
	{
		$items = array();
		
		$query_tables = array("item,item_replies");
		$values = array();
		$query_where = array();
		$query_where_link = array();
		
		$query_where[] .= "item.type = 'comment'";
		$query_where_link[] = "AND";
		
		if($parentid !== NULL)
		{
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
			$items[] = xItemCommentDAO::_itemcommentFromRow($row);
		}
		return $items;
	}
	
	
	
};

?>