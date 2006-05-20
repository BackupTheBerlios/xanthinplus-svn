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


class xItemPageDAO
{
	function xItemPageDAO()
	{
		//non instaltiable
		assert(FALSE);
	}
	
	/**
	 * Insert a new item page
	 *
	 * @param xItemPage $item
	 * @return int The new id
	 * @static
	 */
	function insert($item,$transaction = TRUE)
	{
		if($transaction)
		{
			xDB::getDB()->startTransaction();
		}
		
		$id = xItemDAO::insert($item,FALSE);
		
		xDB::getDB()->query("INSERT INTO item_page(itemid,subtype,sticky,accept_replies,published,approved,
			meta_description,meta_keywords) VALUES (%d,'%s',%d,%d,%d,%d,'%s','%s')",
			$id,$item->m_subtype,$item->m_published,$item->m_sticky,$item->m_accept_replies,$item->m_published,
			$item->m_approved,$item->m_meta_description,$item->m_meta_keywords);
			
		
		if($transaction)
		{
			xDB::getDB()->commit();
		}
		
		
		return $id;
	}
	
	/**
	 * Deletes an item and all its replies.
	 * 
	 * @param int $itemid
	 * @static
	 */
	function delete($itemid)
	{
		xItemDAO::delete($itemid);
	}
	
	/**
	 * Updates an item.
	 *
	 * 
	 * @param xItemPage $item
	 * @static
	 */
	function update($item)
	{
		xDB::getDB()->startTransaction();
		
		xItemDAO::update($item);
		
		xDB::getDB()->query("UPDATE item_page SET published = %d,sticky = %d,accept_replies = %d,published = %d,
			approved = %d,meta_description = '%s',meta_keywords = '%s' WHERE itemid = %d",
			$item->m_published,$item->m_sticky,$item->m_accept_replies,$item->m_published,
			$item->m_approved,$item->m_meta_description,$item->m_meta_keywords,$item->m_id);
			
		xDB::getDB()->commit();
	}
	
	
	/**
	 *
	 * @return xItemPage
	 * @static
	 * @access private
	 */
	function _itempageFromRow($row_object)
	{
		return new xItem($row_object->id,$row_object->title,$row_object->type,$row_object->author,
			$row_object->content,$row_object->content_filter,$row_object->creation_time,$row_object->lastedit_time,
			$row_object->subtype,$row_object->published,$row_object->sticky,$row_object->accept_replies,
			$row_object->published,$row_object->approved,$row_object->meta_description,$row_object->meta_keywords);
	}
	
	/**
	 *
	 */
	function toSpecificItem($item)
	{
		$result = xDB::getDB()->query("SELECT * FROM item_page WHERE itemid = %d",$item->m_id);
		if($row = xDB::getDB()->fetchObject($result))
		{
			return new xItem($item->m_id,$item->m_title,$item->m_type,$item->m_author,
				$item->m_content,$item->m_content_filter,$item->m_creation_time,$item->m_lastedit_time,
				$row->subtype,$row->published,$row->sticky,$row->accept_replies,
				$row->published,$row->approved,$row->meta_description,$row->meta_keywords);
		}
		
		return NULL;
	}
	
	/**
	 * Retrieve a specific item page
	 *
	 * @return xItemPage
	 * @static
	 */
	function load($id)
	{
		$result = xDB::getDB()->query("SELECT * FROM item,item_page WHERE item.id = %d AND item_page.itemid = item.id",$id);
		if($row = xDB::getDB()->fetchObject($result))
		{
			return xItemPageDAO::_itempageFromRow($row);
		}
		
		return NULL;
	}
	
	
	/**
	 * Retrieves all items.
	 *
	 * @param string $subtype Exact search
	 * @param string $title Like search
	 * @param string $author Exact search
	 * @param string $content Like search
	 * @param int $cathegory Exact search on category id
	 * @param int $nelementpage Number of elements per page
	 * @param int $npage Number of page (starting from 1).
	 * @return array(xItem)
	 * @static
	 */
	function find($subtype,$title,$author,$content,$cathegory,$nelementpage = 0,$npage = 0)
	{
		$items = array();
		
		$query_tables = array("item,item_page");
		$values = array();
		$query_where = array();
		$query_where_link = array();
		
		if($subtype !== NULL)
		{
			$query_where[] = "item_page.subtype = '%s'";
			$query_where_link[] = "AND";
			$values[] = $subtype;
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
		
		$query_where[] .= "item_page.itemid = item.id";
		$query_where_link[] = "AND";
			
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
			$items[] = xItemDAO::_itempageFromRow($row);
		}
		return $items;
	}
	
	
	
};

?>