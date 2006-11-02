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


class xNodeTypeDAO
{
	function xNodeTypeDAO()
	{
		//non instaltiable
		assert(FALSE);
	}
	
	/**
	 * Insert a new node type
	 *
	 * @param xNodeType $node_type
	 * @return bool FALSE on error
	 * @static
	 */
	function insert($node_type)
	{
		$db =& xDB::getDB();
		return $db->query("INSERT INTO node_and_cathegory_type (name,description) 
			VALUES ('%s','%s')",$node_type->m_name,$node_type->m_description);
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
		$db =& xDB::getDB();
		return $db->query("DELETE FROM node_and_cathegory_type WHERE name = '%s'",$typename);
	}
	
	/**
	 * Updates an item type.
	 *
	 * 
	 * @param xNodeType $node_type
	 * @return bool FALSE on error
	 * @static
	 */
	function update($node_type)
	{
		$db =& xDB::getDB();
		return $db->query("UPDATE node_and_cathegory_type SET description = '%s' WHERE name = '%s'",
			$node_type->m_description,$node_type->m_name);
	}
	
	/**
	 *
	 * @return xNodeType
	 * @static
	 * @access private
	 */
	function _itemtypeFromRow($row_object)
	{
		return new xNodeType($row_object->name,$row_object->description);
	}
	
	
	/**
	 * Load an Item type from db.
	 *
	 * @return xNodeType
	 * @static
	 */
	function load($typename)
	{
		$db =& xDB::getDB();
		$result = $db->query("SELECT * FROM node_and_cathegory_type WHERE name = '%s'",$typename);
		if($row = $db->fetchObject($result))
		{
			return xNodeTypeDAO::_itemtypeFromRow($row);
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
		$db =& xDB::getDB();
		$types = array();
		$result = $db->query("SELECT * FROM node_and_cathegory_type");
		while($row = $db->fetchObject($result))
		{
			$types[] = xNodeTypeDAO::_itemtypeFromRow($row);
		}
		
		return $types;
	}
}

?>