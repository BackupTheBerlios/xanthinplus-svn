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


class xCathegoryDAO
{
	function xCathegoryDAO()
	{
		//non instaltiable
		assert(FALSE);
	}
	
	/**
	 * Insert a new cathegory
	 *
	 * @param xCathegory $cathegory
	 * @return int The new id or FALSE on error
	 * @static
	 */
	function insert($cathegory,$transaction = TRUE)
	{
		if($transaction)
			xDB::getDB()->startTransaction();
		
		$id = xUniqueId::generate('cathegory');
		$field_names = "id,name,title,type,description";
		$field_values = "%d,'%s','%s','%s','%s'";
		$values = array($id,$cathegory->m_name,$cathegory->m_title,$cathegory->m_type,$cathegory->m_description);
		
		if(!empty($cathegory->m_parent_cathegory))
		{
			$field_names .= ',parent_cathegory';
			$field_values .= ",%d";
			$values[] = $cathegory->m_parent_cathegory;
		}
		
		if(! xDB::getDB()->query("INSERT INTO cathegory($field_names) VALUES($field_values)",$values))
			return false;
		
		if($transaction)
			xDB::getDB()->commit();
		
		return $id;
	}
	
	/**
	 * Deletes a cathegory
	 *
	 * @param int $catid
	 * @return bool FALSE on error
	 * @static
	 */
	function delete($catid)
	{
		return xDB::getDB()->query("DELETE FROM cathegory WHERE id = %d",$catid);
	}
	
	/**
	 * Updates a cathegory.
	 *
	 * @param xCathegory $cathegory
	 * @return bool FALSE on error
	 * @static
	 */
	function update($cathegory)
	{
		$fields = "name = '%s',title = '%s',description = '%s'";
		$values = array($item_type->m_name,$item_type->m_title,$item_type->m_description);

		
		if(!empty($cathegory->m_parent_cathegory))
		{
			$fields .= ",parent_cathegory = %d";
			$values[] = $cathegory->m_parent_cathegory;
		}
		else
		{
			$fields .= ",parent_cathegory = NULL";
		}
		
		
		$values[] = $cathegory->m_id;
		return xDB::getDB()->query("UPDATE cathegory SET $fields WHERE id = %d",$values);
	}
	
	
	/**
	 *
	 * @return xCathegory
	 * @static
	 * @access private
	 */
	function _cathegoryFromRow($row_object)
	{
		return new xCathegory($row_object->id,$row_object->name,$row_object->title,$row_object->type,
			$row_object->description,$row_object->parent_cathegory);
	}
	
	/**
	 * Load an cathegory from db.
	 *
	 * @return xCathegory
	 * @static
	 */
	function load($catid)
	{
		$result = xDB::getDB()->query("SELECT * FROM cathegory WHERE id = %d",$catid);
		if($row = xDB::getDB()->fetchObject($result))
		{
			return xCathegoryDAO::_cathegoryFromRow($row);
		}
		
		return NULL;
	}
	
	/**
	 * Retrieves all cathegories.
	 *
	 * @return array(xCathegory)
	 * @static
	 */
	function findAll()
	{
		$cats = array();
		$result = xDB::getDB()->query("SELECT * FROM cathegory");
		while($row = xDB::getDB()->fetchObject($result))
		{
			$cats[] = xCathegoryDAO::_cathegoryFromRow($row);
		}
		return $cats;
	}
}

?>