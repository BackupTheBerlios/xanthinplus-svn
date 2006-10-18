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
		
		xBoxDAO::insert($box);
		
		xDB::getDB()->query("INSERT INTO box_custom(box_name,title,content,content_filter) VALUES('%s','%s','%s','%s')",
			$box->m_name,$box->m_title,$box->m_content,$box->m_content_filter);
		
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
		
		xBoxDAO::update($box);
		
		xDB::getDB()->query("UPDATE box_custom SET title = '%s',content = '%s',content_filter = '%s' 
			WHERE box_name = '%s'",
			$box->m_title,$box->m_content,$box->m_content_filter,$box->m_name);
		
		if(!xDB::getDB()->commitTransaction())
			return false;
		
		return TRUE;
	}
	
	
	/**
	* Delete an existing static box. Based on key.
	*
	* @param xBoxStatic $box
	* @return bool FALSE on error
	* @static 
	*/
	function delete($box)
	{
		return xBoxDAO::delete($box);
	}
	
	/**
	 *
	 */
	function _boxcustomFromRow($row)
	{
		return new xBoxCustom($row->name,$row->type,$row->weight,
			new xShowFilter($row->show_filters_type,$row->show_filters),$row->title,$row->content,$row->content_filter);
	}
	
	/**
	 * Extract specific data for static box and build and return a new xBoxStatic
	 *
	 * @param xBox $box
	 * @return xBoxStatic or NULL no error
	 * @static
	 */
	function load($name)
	{
		$result = xDB::getDB()->query("SELECT * FROM box_custom WHERE box_name = '%s'",$name);
		if($row = xDB::getDB()->fetcObject($result))
		{
			return xBoxCustomDAO::_boxcustomFromRow($row);
		}
		return NULL;
	}
};











?>