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
	function insert($cathegory)
	{
		$db =& xDB::getDB();
		$db->startTransaction();
		
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
		
		$db->query("INSERT INTO cathegory($field_names) VALUES($field_values)",$values);
		
		if(! $db->commitTransaction())
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
		$db =& xDB::getDB();
		return $db->query("DELETE FROM cathegory WHERE id = %d",$catid);
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
		$db =& xDB::getDB();
		$cats = array();
		$result = $db->query("SELECT * FROM cathegory,node_to_cathegory WHERE node_to_cathegory.nodeid = %d 
			AND cathegory.id = node_to_cathegory.catid",$id);
		while($row = $db->fetchObject($result))
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
		$db =& xDB::getDB();
		$result = $db->query("SELECT * FROM cathegory WHERE id = %d",$catid);
		if($row = $db->fetchObject($result))
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
		$db =& xDB::getDB();
		$where['cathegory']['type']['type'] = "'%s'";
		$where['cathegory']['type']['connector'] = "AND";
		$where['cathegory']['type']['value'] = $type;
		
		$where['cathegory']['parent_cathegory']['type'] = "%d";
		$where['cathegory']['parent_cathegory']['connector'] = "AND";
		$where['cathegory']['parent_cathegory']['value'] = $parent_cathegory;
		
		$result = $db->autoQuery('SELECT',array(),$where);
		$cats = array();
		while($row = $db->fetchObject($result))
		{
			$cats[] = xCathegoryDAO::_cathegoryFromRow($row);
		}
		return $cats;
	}
	
}




class xCathegoryI18NDAO
{
	function xCathegoryI18NDAO()
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
		$db =& xDB::getDB();
		$db->startTransaction();
		
		$id = xCathegoryDAO::insert($cathegory);
		
		$db->query("INSERT INTO cathegory_i18n(catid,name,title,description,lang) VALUES(%d,'%s','%s','%s','%s')",
			$id,$cathegory->m_name,$cathegory->m_title,$cathegory->m_description,$cathegory->m_lang);
		
		if(! $db->commitTransaction())
			return false;
		
		return $id;
	}
	
	/**
	 * Insert a new cathegory
	 *
	 * @param xCathegory $cathegory
	 * @return bool FALSE on error
	 * @static
	 */
	function insertTranslation($cathegory)
	{
		$db =& xDB::getDB();
		return $db->query("INSERT INTO cathegory_i18n(catid,name,title,description,lang) VALUES(%d,'%s','%s','%s','%s')",
			$cathegory->m_id,$cathegory->m_name,$cathegory->m_title,$cathegory->m_description,$cathegory->m_lang);
	}
	
	
	/**
	 * Insert a new cathegory
	 *
	 * @param xCathegory $cathegory
	 * @return int The new id or FALSE on error
	 * @static
	 */
	function deleteTranslation($id,$lang)
	{
		$db =& xDB::getDB();
		$db->query("DELETE FROM cathegory_i18n WHERE catid = %d AND lang = '%s'",$id,$lang);
	}
	
	/**
	 *
	 * @return xCathegory
	 * @static
	 * @access private
	 */
	function _cathegoryi18nFromRow($row_object)
	{
		return new xCathegoryI18N($row_object->id,$row_object->type,$row_object->parent_cathegory,
			$row_object->name,$row_object->title,$row_object->description,$row_object->lang);
	}
	
	/**
	 * Load an cathegory from db.
	 *
	 * @return xCathegoryI18N
	 * @static
	 */
	function load($catid,$lang)
	{
		$db =& xDB::getDB();
		$result = $db->query("SELECT * FROM cathegory,cathegory_i18n WHERE cathegory.id = %d AND 
			cathegory_i18n.catid = cathegory.id AND cathegory_i18n.lang = '%s'",$catid,$lang);
		if($row = $db->fetchObject($result))
		{
			return xCathegoryI18NDAO::_cathegoryi18nFromRow($row);
		}
		
		return NULL;
	}
	
	/**
	 * Retrieves cathegories by search parameters
	 *
	 * @return array(xCathegory)
	 * @static
	 */
	function find($type = NULL,$parent_cathegory = NULL,$name = NULL,$lang = NULL)
	{
		$db =& xDB::getDB();
		$where['cathegory']['id']['join'][] = "cathegory_i18n.catid";
		$where['cathegory']['id']['connector'] = "AND";
		
		$where['cathegory']['type']['type'] = "'%s'";
		$where['cathegory']['type']['connector'] = "AND";
		$where['cathegory']['type']['value'] = $type;
		
		$where['cathegory']['parent_cathegory']['type'] = "%d";
		$where['cathegory']['parent_cathegory']['connector'] = "AND";
		$where['cathegory']['parent_cathegory']['value'] = $parent_cathegory;
		
		$where['cathegory_i18n']['name']['type'] = "'%s'";
		$where['cathegory_i18n']['name']['connector'] = "AND";
		$where['cathegory_i18n']['name']['value'] = $name;
		
		$where['cathegory_i18n']['lang']['type'] = "'%s'";
		$where['cathegory_i18n']['lang']['connector'] = "AND";
		$where['cathegory_i18n']['lang']['value'] = $lang;
		
		$result = $db->autoQuery('SELECT',array(),$where);
		$cats = array();
		while($row = $db->fetchObject($result))
		{
			$cats[] = xCathegoryI18NDAO::_cathegoryi18nFromRow($row);
		}
		return $cats;
	}
	
}



?>