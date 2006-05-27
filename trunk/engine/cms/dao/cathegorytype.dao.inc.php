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
	 * @return bool FALSE on error
	 * @static
	 */
	function insert($cathegory_type,$transaction = TRUE)
	{
		if($transaction)
			xDB::getDB()->startTransaction();
			
		if(! xDB::getDB()->query("INSERT INTO cathegory_type (name,description) 
			VALUES ('%s','%s')",$cathegory_type->m_name,$cathegory_type->m_description))
			return FALSE;
		
		if(! xCathegoryTypeDAO::_insertItemTypes($cathegory_type->m_name,$cathegory_type->m_item_types))
			return FALSE;
		
		if($transaction)
			xDB::getDB()->commit();
			
		return TRUE;
	}
	
	/**
	 * @access private
	 */
	function _insertItemTypes($cattype,$types)
	{
		foreach($types as $type)
		{
			if(! xDB::getDB()->query("INSERT INTO cathegory_type_to_item_types (cattype,itemtype) 
				VALUES ('%s','%s')",$cattype,$type))
				return FALSE;
		}
		
		return TRUE;
	}
	
	/**
	 * Deletes a cathegory type.
	 *
	 * @param string $typename
	 * @return bool FALSE on error
	 * @static
	 */
	function delete($typename)
	{
		return xDB::getDB()->query("DELETE FROM cathegory_type WHERE name = '%s'",$typename);
	}
	
	/**
	 * Updates a cathegory type.
	 *
	 * @param xCathegoryType $item_type
	 * @return bool FALSE on error
	 * @static
	 */
	function update($cathegory_type,$transaction = TRUE)
	{
		if($transaction)
			xDB::getDB()->startTransaction();
			
		//clear all cattype to itemtype
		if(! xDB::getDB()->query("DELETE FROM cathegory_type_to_item_types WHERE cattype = '%s'",
			$cathegory_type->m_name))
			return FALSE;
			
		//reinsert
		if(! xCathegoryTypeDAO::_insertItemTypes($cathegory_type->m_name,$cathegory_type->m_item_types))
			return FALSE;
		
		//update cattype
		if(! xDB::getDB()->query("UPDATE cathegory_type SET description = '%s' WHERE name = '%s'",
			$cathegory_type->m_description,$cathegory_type->m_name))
			return FALSE;
			
		if($transaction)
			xDB::getDB()->commit();
			
		return TRUE;
	}
	
	
	/**
	 *
	 * @return array(string)
	 * @static
	 * @access private
	 */
	function _loadItemTypes($cattype)
	{
		$types = array();
		$result = xDB::getDB()->query("SELECT * FROM cathegory_type_to_item_types WHERE cattype = '%s'",$cattype);
		while($row = xDB::getDB()->fetchObject($result))
		{
			$types[] = $row->itemtype;
		}
		
		return $types;
	}
	
	
	/**
	 *
	 * @return xCathegoryType
	 * @static
	 * @access private
	 */
	function _cathegorytypeFromRow($row_object)
	{
		return new xCathegoryType($row_object->name,$row_object->description,
			xCathegoryTypeDAO::_loadItemTypes($row_object->name));
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