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


class xItemTypeDAO
{
	function xItemTypeDAO()
	{
		//non instaltiable
		assert(FALSE);
	}
	
	/**
	 * Insert a new item type
	 *
	 * @param xItemType $item_type
	 * @return bool FALSE on error
	 * @static
	 */
	function insert($item_type)
	{
		return xDB::getDB()->query("INSERT INTO item_type (name,description) 
			VALUES ('%s','%s')",$item_type->m_name,$item_type->m_description);
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
		return xDB::getDB()->query("DELETE FROM item_type WHERE name = '%s'",$typename);
	}
	
	/**
	 * Updates an item type.
	 *
	 * 
	 * @param xItemType $item_type
	 * @return bool FALSE on error
	 * @static
	 */
	function update($item_type)
	{
		return xDB::getDB()->query("UPDATE item_type SET description = '%s' WHERE name = '%s'",
			$item_type->m_description,$item_type->m_name);
	}
	
	/**
	 *
	 * @return xItemType
	 * @static
	 * @access private
	 */
	function _itemtypeFromRow($row_object)
	{
		return new xItemType($row_object->name,$row_object->description);
	}
	
	
	/**
	 * Load an Item type from db.
	 *
	 * @return xItemType
	 * @static
	 */
	function load($typename)
	{
		$result = xDB::getDB()->query("SELECT * FROM item_type WHERE name = '%s'",$typename);
		if($row = xDB::getDB()->fetchObject($result))
		{
			return xItemTypeDAO::_itemtypeFromRow($row);
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
		$types = array();
		$result = xDB::getDB()->query("SELECT * FROM item_type");
		while($row = xDB::getDB()->fetchObject($result))
		{
			$types[] = xItemTypeDAO::_itemtypeFromRow($row);
		}
		
		return $types;
	}
}

?>