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


class xItemPageTypeDAO
{
	function xItemPageTypeDAO()
	{
		//non instaltiable
		assert(FALSE);
	}
	
	/**
	 * Insert a new item type
	 *
	 * @param xItemPageType $item_type
	 * @static
	 */
	function insert($item_type)
	{
		xDB::getDB()->query("INSERT INTO item_page_subtype (name,description,allowed_content_filters,default_published,
			default_sticky,default_accept_replies,default_approved) VALUES ('%s','%s','%s',%d,%d,%d,%d)",
			$item_type->m_name,$item_type->m_description,$item_type->m_allowed_content_filters,
			$item_type->m_default_published,$item_type->m_default_sticky,$item_type->m_default_accept_replies,
			$item_type->m_default_approved);
	}
	
	/**
	 * Deletes an item type.
	 *
	 * 
	 * @param string $typename
	 * @static
	 */
	function delete($typename)
	{
		xDB::getDB()->query("DELETE FROM item_page_subtype WHERE name = '%s'",$typename);
	}
	
	/**
	 * Updates an item type.
	 *
	 * 
	 * @param xItemPageType $item_type
	 * @static
	 */
	function update($item_type)
	{
		xDB::getDB()->query("UPDATE item_page_subtype SET description = '%s', allowed_content_filters = '%s',
			default_published = %d,default_sticky = %d,default_accept_replies = %d,default_approved = %d
			WHERE name = '%s'",$item_type->m_description,$item_type->m_name,$item_type->m_allowed_content_filters,
			$item_type->m_default_published,$item_type->m_default_sticky,$item_type->m_default_accept_replies,
			$item_type->m_default_approved);
	}
	
	/**
	 *
	 * @return xItemPageType
	 * @static
	 * @access private
	 */
	function _itempagetypeFromRow($row_object)
	{
		return new xItemPageType($row_object->name,$row_object->description,$row_object->allowed_content_filters,
		$row_object->default_published,$row_object->default_sticky,$row_object->default_accept_replies,
		$row_object->default_approved);
	}
	
	
	/**
	 * Load an Item type from db.
	 *
	 * @return xItemPageType
	 * @static
	 */
	function load($typename)
	{
		$result = xDB::getDB()->query("SELECT * FROM item_page_subtype WHERE name = '%s'",$typename);
		if($row = xDB::getDB()->fetchObject($result))
		{
			return xItemPageTypeDAO::_itempagetypeFromRow($row);
		}
		
		return NULL;
	}
	
	/**
	 * Retrieves all item type.
	 *
	 * @return array(xItemPageType)
	 * @static
	 */
	function findAll()
	{
		$types = array();
		$result = xDB::getDB()->query("SELECT * FROM item_page_subtype");
		while($row = xDB::getDB()->fetchObject($result))
		{
			$types[] = xItemPageTypeDAO::_itempagetypeFromRow($row);
		}
		
		return $types;
	}
}

?>