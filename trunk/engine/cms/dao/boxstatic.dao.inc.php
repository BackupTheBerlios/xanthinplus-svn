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
 * "Box Static" Data Access Object
 */
class xBoxStaticDAO
{
	/**
	 * Insert a new static box.
	 *
	 * @param xBoxStatic $box
	 * @return bool FALSE on error
	 * @static 
	 */
	function insert($box,$transaction = true)
	{
		if($transaction)
			xDB::getDB()->startTransaction();
		
		if(! xBoxDAO::insert($box,false))
			return FALSE;
		
		if(! xDB::getDB()->query("INSERT INTO box_static(box_name,content,content_filter) VALUES('%s','%s','%s')",
			$box->m_name,$box->m_content,$box->m_content_filter))
			return FALSE;
		
		if($transaction)
			xDB::getDB()->commit();
		
		return TRUE;
	}
	
	
	/**
	 * Update an existing static box.
	 *
	 * @param xBoxStatic $box
	 * @return bool FALSE on error
	 * @static 
	 */
	function update($box,$transaction = true)
	{
		if($transaction)
			xDB::getDB()->startTransaction();
		
		if(! xBoxDAO::update($box,false))
			return FALSE;
		
		if(! xDB::getDB()->query("UPDATE box_static SET content,content_filter WHERE box_name = '%s'",
			$box->m_content,$box->m_content_filter,$box->m_name))
			return FALSE;
		
		if($transaction)
			xDB::getDB()->commit();
		
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
	 * Extract specific data for static box and build and return a new xBoxStatic
	 *
	 * @param xBox $box
	 * @return xBoxStatic or NULL no error
	 * @static
	 */
	function toSpecificBox($box)
	{
		$result = xDB::getDB()->query("SELECT * FROM box_static");
		
		if($row = xDB::getDB()->fetcObject($result))
		{
			return new xBoxStatic($box->m_name,$box->m_title,$box->m_type,$box->m_weight,
				$row->content,$row->content_filter,$box->m_filterset,$box->m_area);
		}
		return NULL;
	}
};











?>