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


class xItemCathegorizableDAO
{
	function xItemCathegorizableDAO()
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
		
		$id = xItemDAO::insert($item,FALSE);
		if($id == FALSE)
			return false;
		
		if(! xDB::getDB()->query("INSERT INTO item_cathegorizable(itemid,catid) VALUES(%d,%d)",
			$item->m_id,$item->m_cathegory))
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
		return xItemDAO::delete($itemid,$transaction);
	}
	
	/**
	 * Updates an item.
	 *
	 * 
	 * @param xItem $item
	 * @return bool FALSE on error
	 * @static
	 */
	function update($item,$transaction)
	{
		if($transaction)
			xDB::getDB()->startTransaction();
		
		$id = xItemDAO::update($item);
		
		
		if(! xDB::getDB()->query("UPDATE item_cathegorizable SET catid = %d WHERE itemid = %d",
			$item->m_cathegory,$item->m_id))
			return false;
		
		if($transaction)
			xDB::getDB()->commit();
		
		return TRUE;
	}
	
	
	/**
	 *
	 * @return xItem
	 * @static
	 * @access private
	 */
	function _itemFromRow($row_object)
	{
		return new xItemCathegorizable($row_object->id,$row_object->title,$row_object->type,$row_object->author,
			$row_object->content,$row_object->content_filter,$row_object->creation_time,$row_object->cathegory);
	}
	
	/**
	 * Retrieve a specific item
	 *
	 * @return xItem
	 * @static
	 */
	function load($id)
	{
		$result = xDB::getDB()->query("SELECT * FROM item,item_cathegorizable WHERE id = %d 
			AND item_cathegorizable.itemid = item.id",$id);
		if($row = xDB::getDB()->fetchObject($result))
		{
			return xItemCathegorizableDAO::_itemFromRow($row);
		}
		
		return NULL;
	}
};

?>