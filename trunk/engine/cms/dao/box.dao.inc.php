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



/**
 * Box Data Access Object
 */
class xBoxDAO
{
	/**
	* Insert a new box.
	*
	* @param xBox $box
	* @return bool FALSE on error
	* @static 
	*/
	function insert($box)
	{
		$field_names = "name,title,type,weight,show_filters_type,show_filters";
		$field_values = "'%s','%s','%s',%d,'%s','%s'";
		$values = array($box->m_name,$box->m_title,$box->m_type,$box->m_weight,
			$box->m_show_filter->m_type,$box->m_show_filter->m_filters);
		
		return xDB::getDB()->query("INSERT INTO box($field_names) VALUES($field_values)",$values);
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
		$fields = "weight = %d, title = '%s', show_filters_type = '%s',show_filter = '%s'";
		$values = array($box->m_weight,$box->m_title,$box->m_show_filter->m_type,$box->m_show_filter->m_filters);
		
		$values[] = $box->m_name;
		return xDB::getDB()->query("UPDATE box SET $fields WHERE name = '%s'",$values);
	}
	
	
	/**
	* Delete an existing box. Based on key.
	*
	* @param string $box_name
	* @return bool FALSE on error
	* @static 
	*/
	function delete($box_name)
	{
		return xDB::getDB()->query("DELETE FROM box WHERE name = '%s'",$box_name);
	}
	
	
	/**
	 * @access private
	 */ 
	function _boxFromRow($row)
	{
		return new xBox($row->name,$row->title,$row->type,$row->weight,
			new xShowFilter($row->show_filters_type,$row->show_filters));
	}
	
	/**
	 * Returns all registered boxes.
	 * 
	 * @return array(xBox)
	 * @static
	 */
	function findAll()
	{
		$boxes = array();
		$result = xDB::getDB()->query("SELECT * FROM box");
		while($row = xDB::getDB()->fetchObject($result))
		{
			$boxes[] = xBoxDAO::_boxFromRow($row);
		}
		
		return $boxes;
	}
};

?>