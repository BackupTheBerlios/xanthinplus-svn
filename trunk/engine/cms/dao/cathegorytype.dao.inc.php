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


class xCathegoryTypeDAO
{
	function xCathegoryTypeDAO()
	{
		//non instaltiable
		assert(FALSE);
	}
	
	/**
	 * Insert a new cathegory type
	 *
	 * @param xCathegoryType $cathegory_type
	 * @static
	 */
	function insert($cathegory_type)
	{
		xDB::getDB()->query("INSERT INTO cathegory_type (name,description) 
			VALUES ('%s','%s')",$cathegory_type->m_name,$cathegory_type->m_description);
	}
	
	/**
	 * Deletes a cathegory type.
	 *
	 * 
	 * @param string $typename
	 * @static
	 */
	function delete($typename)
	{
		xDB::getDB()->query("DELETE FROM cathegory_type WHERE name = '%s'",$typename);
	}
	
	/**
	 * Updates a cathegory type.
	 *
	 * 
	 * @param xCathegoryType $item_type
	 * @static
	 */
	function update($cathegory_type)
	{
		xDB::getDB()->query("UPDATE cathegory_type SET description = '%s' WHERE name = '%s'",
			$cathegory_type->m_description,$cathegory_type->m_name);
	}
	
	/**
	 *
	 * @return xCathegoryType
	 * @static
	 * @access private
	 */
	function _cathegorytypeFromRow($row_object)
	{
		return new xCathegoryType($row_object->name,$row_object->description);
	}
	
	
	/**
	 * Load a cathegory type from db.
	 *
	 * @return xCathegoryType
	 * @static
	 */
	function load($typename)
	{
		$result = xDB::getDB()->query("SELECT * FROM cathegory_type WHERE name = '%s'",$typename);
		if($row = xDB::getDB()->fetchObject($result))
		{
			return xCathegoryTypeDAO::_cathegorytypeFromRow($row);
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
		$result = xDB::getDB()->query("SELECT * FROM cathegory_type");
		while($row = xDB::getDB()->fetchObject($result))
		{
			$types[] = xCathegoryTypeDAO::_cathegorytypeFromRow($row);
		}
		
		return $types;
	}
}

?>