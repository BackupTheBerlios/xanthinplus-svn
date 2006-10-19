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
 * Box I18N Data Access Object
 */
class xBoxI18NDAO
{
	/**
	* Insert a new xBoxI18.
	*
	* @param xBoxI18 $box
	* @return bool FALSE on error
	* @static 
	*/
	function insert($box)
	{
		xDB::getDB()->startTransaction();
		
		xBoxDAO::insert($box);
		
		xDB::getDB()->query("INSERT INTO box_i18n(box_name,title,lang) VALUES('%s','%s','%s')",
			$box->m_name,$box->m_title,$box->m_lang);
		
		if(!xDB::getDB()->endTransaction())
			return false;
			
		return true;
	}
	
	/**
	* Insert a new xBoxI18 translation
	*
	* @param xBoxI18 $box
	* @return bool FALSE on error
	* @static 
	*/
	function insertTranslation($box)
	{
		return xDB::getDB()->query("INSERT INTO box_i18n(box_name,title,lang) VALUES('%s','%s','%s')",
			$box->m_name,$box->m_title,$box->m_lang);
	}
	
	/**
	* Delete an existing box translation.
	*
	* @param string $box_name
	* @return bool FALSE on error
	* @static 
	*/
	function deleteTranslation($box_name,$lang)
	{
		return xDB::getDB()->query("DELETE FROM box_i18n WHERE box_name = '%s', lang ='%s'",$box_name,$lang);
	}
	
	
	/**
	 * Update an existing box.
	 *
	 * @param xBox $box
	 * @return bool FALSE on error
	 * @static 
	 */
	function update($box)
	{
		xDB::getDB()->startTransaction();
		
		xBoxDAO::update($box);
		
		xDB::getDB()->query("UPDATE box_i18n SET title = '%s' WHERE box_name = '%s' AND lang = '%s'",
			$box->m_title,$box->m_name,$box->m_lang);
		
		if(!xDB::getDB()->endTransaction())
			return false;
			
		return true;
	}
	
	/**
	 * @access private
	 */ 
	function _boxI18nFromRow($row)
	{
		return new xBoxI18N($row->name,$row->type,$row->weight,
			new xShowFilter($row->show_filters_type,$row->show_filters,$row->title,$row->lang));
	}
	
	/**
	 * Returns all registered boxes.
	 * 
	 * @return array(xBox)
	 * @static
	 */
	function findAll($lang)
	{
		$boxes = array();
		$result = xDB::getDB()->query("SELECT * FROM box,box_i18n WHERE lang = '%s' AND box.name = box_i18n.box_name",
			$lang);
		while($row = xDB::getDB()->fetchObject($result))
		{
			$boxes[] = xBoxI18NDAO::_boxI18nFromRow($row);
		}
		
		return $boxes;
	}
};

?>