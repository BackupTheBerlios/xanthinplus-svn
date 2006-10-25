﻿<?php
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
	function insert($cathegory)
	{
		xDB::getDB()->startTransaction();
		
		$id = xUniqueId::generate('cathegory');
		
		$field_names = "id,type";
		$field_values = "%d,'%s'";
		$values = array($id,$cathegory->m_type);
		
		if(!empty($cathegory->m_parent_cathegory))
		{
			$field_names .= ',parent_cathegory';
			$field_values .= ",%d";
			$values[] = $cathegory->m_parent_cathegory;
		}
		
		xDB::getDB()->query("INSERT INTO cathegory($field_names) VALUES($field_values)",$values);
		
		if(! xDB::getDB()->commitTransaction())
			return false;
		
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
	 *
	 * @return xCathegory
	 * @static
	 * @access private
	 */
	function _cathegoryFromRow($row_object)
	{
		return new xCathegory($row_object->id,$row_object->type,$row_object->parent_cathegory);
	}
	
	/**
	 * @return array(xCathegory)
	 */
	function findNodeCathegories($id)
	{
		$cats = array();
		$result = xDB::getDB()->query("SELECT * FROM cathegory,node_to_cathegory WHERE node_to_cathegory.nodeid = %d 
			AND cathegory.id = node_to_cathegory.catid",$id);
		while($row = xDB::getDB()->fetchObject($result))
		{
			$cats[] = xCathegoryDAO::_cathegoryFromRow($row);
		}
		
		return $cats;
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
	 * Retrieves cathegories by search parameters
	 *
	 * @return array(xCathegory)
	 * @static
	 */
	function find($type = NULL,$parent_cathegory = NULL)
	{
		$where['cathegory']['type']['type'] = "'%s'";
		$where['cathegory']['type']['connector'] = "AND";
		$where['cathegory']['type']['value'] = $type;
		
		$where['cathegory']['parent_cathegory']['type'] = "%d";
		$where['cathegory']['parent_cathegory']['connector'] = "AND";
		$where['cathegory']['parent_cathegory']['value'] = $parent_cathegory;
		
		$result = xDB::getDB()->autoQuery('SELECT',array(),$where);
		$cats = array();
		while($row = xDB::getDB()->fetchObject($result))
		{
			$cats[] = xCathegoryDAO::_cathegoryFromRow($row);
		}
		return $cats;
	}
	
}

?>