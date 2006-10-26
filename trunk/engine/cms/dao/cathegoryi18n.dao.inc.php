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
		xDB::getDB()->startTransaction();
		
		$id = xCathegoryDAO::insert($cathegory);
		
		xDB::getDB()->query("INSERT INTO cathegory_i18n(catid,name,title,description,lang) VALUES(%d,'%s','%s','%s','%s')",
			$id,$cathegory->m_name,$cathegory->m_title,$cathegory->m_description,$cathegory->m_lang);
		
		if(! xDB::getDB()->commitTransaction())
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
		return xDB::getDB()->query("INSERT INTO cathegory_i18n(catid,name,title,description,lang) VALUES(%d,'%s','%s','%s','%s')",
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
		xDB::getDB()->query("DELETE FROM cathegory_i18n WHERE catid = %d AND lang = '%s'",$id,$lang);
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
		$result = xDB::getDB()->query("SELECT * FROM cathegory,cathegory_i18n WHERE cathegory.id = %d AND 
			cathegory_i18n.catid = cathegory.id AND cathegory_i18n.lang = '%s'",$catid,$lang);
		if($row = xDB::getDB()->fetchObject($result))
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
		
		$result = xDB::getDB()->autoQuery('SELECT',array(),$where);
		$cats = array();
		while($row = xDB::getDB()->fetchObject($result))
		{
			$cats[] = xCathegoryI18NDAO::_cathegoryi18nFromRow($row);
		}
		return $cats;
	}
	
}

?>