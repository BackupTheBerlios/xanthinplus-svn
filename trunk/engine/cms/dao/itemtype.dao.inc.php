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
	 * @static
	 */
	function insert($item_type)
	{
		$field_names = "name,description,default_content_filter,default_approved,default_published,default_sticky,
			default_weight,default_accept_replies";
		$field_values = "'%s','%s',%d,%d,%d,%d,%d,%d";
		$values = array($item_type->m_name,$item_type->m_description,$item_type->m_default_content_filter,
			$item_type->m_default_approved,$item_type->m_default_published,$item_type->m_default_sticky,
			$item_type->m_default_weight,$item_type->m_default_accept_replies);
		
		if(!empty($item_type->m_accessfiltersetid))
		{
			$field_names .= ',accessfiltersetid';
			$field_values .= ",%d";
			$values[] = $item_type->m_accessfiltersetid;
		}
		
		xDB::getDB()->query("INSERT INTO item_type($field_names) VALUES($field_values)",$values);
	}
	
	/**
	 * Deletes an item type.
	 *
	 * 
	 * @param string $itemtypename
	 * @static
	 */
	function delete($itemtypename)
	{
		xDB::getDB()->query("DELETE FROM item_type WHERE name = '%s'",$itemtypename);
	}
	
	/**
	 * Updates an item type.
	 *
	 * 
	 * @param xItemType $item_type
	 * @static
	 */
	function update($item_type)
	{
		$fields = "description = '%s',default_content_filter = %d,default_approved = %d,default_published = %d,
			default_sticky = %d,default_weight = %d,default_accept_replies = %d";
		$values = array($item_type->m_description,$item_type->m_default_content_filter,
			$item_type->m_default_approved,$item_type->m_default_published,$item_type->m_default_sticky,
			$item_type->m_default_weight,$item_type->m_default_accept_replies);
		
		if(!empty($item_type->m_accessfiltersetid))
		{
			$fields .= ",accessfiltersetid = %d";
			$values[] = $item_type->m_accessfiltersetid;
		}
		else
		{
			$fields .= ",accessfiltersetid = NULL";
		}
		
		$values[] = $item_type->m_name;
		xDB::getDB()->query("UPDATE item_type SET $fields WHERE name = '%s'",$values);
	}
	
	/**
	 *
	 * @return xItemType
	 * @static
	 * @access private
	 */
	function _itemtypeFromRow($row_object)
	{
		return new xItemType($row_object->name,$row_object->description,$row_object->default_content_filter,
			$row_object->default_approved,$row_object->default_published,$row_object->default_sticky,
			$row_object->default_weight,$row_object->default_accept_replies,$row_object->accessfiltersetid);
	}
	
	
	/**
	 * Load an Item type from db.
	 *
	 * @return xItemType
	 * @static
	 */
	function load($itemtypename)
	{
		$result = xDB::getDB()->query("SELECT * FROM item_type WHERE name = '%s'",$itemtypename);
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