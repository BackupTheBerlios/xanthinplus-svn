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
	 * @return int The new id
	 * @static
	 */
	function insert($cathegory)
	{
		$field_names = "name,description";
		$field_values = "'%s','%s'";
		$values = array($cathegory->m_name,$cathegory->m_description);
		
		if(!empty($cathegory->m_parent_cathegory))
		{
			$field_names .= ',parent_cathegory';
			$field_values .= ",%d";
			$values[] = $cathegory->m_parent_cathegory;
		}
		
		
		if(!empty($cathegory->m_accessfiltersetid))
		{
			$field_names .= ',accessfiltersetid';
			$field_values .= ",%d";
			$values[] = $cathegory->m_accessfiltersetid;
		}
		
		if(!empty($cathegory->m_items_type))
		{
			$field_names .= ',items_type';
			$field_values .= ",'%s'";
			$values[] = $cathegory->m_items_type;
		}
		
		xDB::getDB()->query("INSERT INTO cathegory($field_names) VALUES($field_values)",$values);
		return xDB::getDB()->getLastId();
	}
	
	/**
	 * Deletes a cathegory
	 *
	 * 
	 * @param int $catid
	 * @static
	 */
	function delete($catid)
	{
		xDB::getDB()->query("DELETE FROM cathegory WHERE id = %d",$catid);
	}
	
	/**
	 * Updates a cathegory.
	 *
	 * 
	 * @param xCathegory $cathegory
	 * @static
	 */
	function update($cathegory)
	{
		$fields = "name = '%s',description = '%s'";
		$values = array($item_type->m_name,$item_type->m_description);
		
		if(!empty($cathegory->m_accessfiltersetid))
		{
			$fields .= ",accessfiltersetid = %d";
			$values[] = $cathegory->m_accessfiltersetid;
		}
		else
		{
			$fields .= ",accessfiltersetid = NULL";
		}
		
		if(!empty($cathegory->m_parent_cathegory))
		{
			$fields .= ",parent_cathegory = %d";
			$values[] = $cathegory->m_parent_cathegory;
		}
		else
		{
			$fields .= ",parent_cathegory = NULL";
		}
		
		if(!empty($cathegory->m_items_type))
		{
			$fields .= ",items_type = '%s'";
			$values[] = $cathegory->m_items_type;
		}
		else
		{
			$fields .= ",items_type = NULL";
		}
		
		
		$values[] = $cathegory->m_id;
		xDB::getDB()->query("UPDATE cathegory SET $fields WHERE id = %d",$values);
	}
	
	/**
	 *
	 * @return xCathegory
	 * @static
	 * @access private
	 */
	function _cathegoryFromRow($row_object)
	{
		return new xCathegory($row_object->id,$row_object->name,$row_object->description,$row_object->parent_cathegory,
			$row_object->items_type,$row_object->accessfiltersetid);
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