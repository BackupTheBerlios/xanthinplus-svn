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



/**
 * "Box Custom" Data Access Object
 */
class xBoxCustomDAO
{
	/**
	 * Insert a new Custom box.
	 *
	 * @param xBoxCustom $box
	 * @return bool FALSE on error
	 * @static 
	 */
	function insert($box)
	{
		xDB::getDB()->startTransaction();
		
		xBoxI18NDAO::insert($box);
		
		xDB::getDB()->query("INSERT INTO box_custom(box_name,lang,content,content_filter) VALUES('%s','%s','%s','%s')",
			$box->m_name,$box->m_lang,$box->m_content,$box->m_content_filter);
		
		if(!xDB::getDB()->commitTransaction())
			return false;
		
		return TRUE;
	}
	
	/**
	* Insert a new xBoxCustom translation
	*
	* @param xBoxI18 $box
	* @return bool FALSE on error
	* @static 
	*/
	function insertTranslation($box)
	{
		xDB::getDB()->startTransaction();
		
		xBoxI18NDAO::insertTranslation($box);
		
		xDB::getDB()->query("INSERT INTO box_custom(box_name,lang,content,content_filter) VALUES('%s','%s','%s','%s')",
			$box->m_name,$box->m_lang,$box->m_content,$box->m_content_filter);
		
		if(!xDB::getDB()->commitTransaction())
			return false;
		
		return TRUE;
	}
	
	
	/**
	 * Update an existing custom box.
	 *
	 * @param xBoxCustom $box
	 * @return bool FALSE on error
	 * @static 
	 */
	function update($box)
	{
		xDB::getDB()->startTransaction();
		
		xBoxI18NDAO::update($box);
		
		xDB::getDB()->query("UPDATE box_custom SET content = '%s',content_filter = '%s' 
			WHERE box_name = '%s' AND lang = '%s'",
			$box->m_content,$box->m_content_filter,$box->m_name,$box->m_lang);
		
		if(!xDB::getDB()->commitTransaction())
			return false;
		
		return TRUE;
	}
	
	/**
	 *
	 */
	function _boxcustomFromRow($row)
	{
		return new xBoxCustom($row->name,$row->type,$row->weight,
			new xShowFilter($row->show_filters_type,$row->show_filters),$row->title,$row->lang,
			$row->content,$row->content_filter);
	}
	
	/**
	 * Extract specific data for static box and build and return a new xBoxStatic
	 *
	 * @param xBox $box
	 * @return xBoxCustom or NULL no error
	 * @static
	 */
	function load($name)
	{
		$result = xDB::getDB()->query("SELECT * FROM box,box_i18n,box_custom WHERE box.name = '%s' 
			AND box_i18N.box_name = box.name AND box_custom.box_name = box_i18n.box_name",$name);
		if($row = xDB::getDB()->fetchObject($result))
		{
			return xBoxCustomDAO::_boxcustomFromRow($row);
		}
		return NULL;
	}
};











?>