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
		$field_names = "name,title,type,weight";
		$field_values = "'%s','%s','%s',%d";
		$values = array($box->m_name,$box->m_title,$box->m_type,$box->m_weight);
		
		if(!empty($box->m_area))
		{
			$field_names .= ',area';
			$field_values .= ",'%s'";
			$values[] = $box->m_area;
		}
		if(!empty($box->m_filterset))
		{
			$field_names .= ',filterset';
			$field_values .= ",%d";
			$values[] = $box->m_filterset;
		}
		
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
		$fields = "title = '%s',weight = %d";
		$values = array($box->m_title,$box->m_weight);
		
		if(!empty($box->m_area))
		{
			$fields .= ",area = '%s'";
			$values[] = $box->m_area;
		}
		else
		{
			$fields .= ",area = NULL";
		}
		
		if(!empty($box->m_filterset))
		{
			$fields .= ",filterset = %d";
			$values[] = $box->m_filterset;
		}
		else
		{
			$fields .= ",filterset = NULL";
		}
		
		$values[] = $box->m_id;
		return xDB::getDB()->query("UPDATE box SET $fields WHERE name = '%s'",$values);
	}
	
	
	/**
	* Delete an existing box. Based on key.
	*
	* @param xBox $box
	* @return bool FALSE on error
	* @static 
	*/
	function delete($box)
	{
		return xDB::getDB()->query("DELETE FROM box WHERE name = '%s'",$box->m_name);
	}
	
	/**
	 * Returns all registered boxes.
	 * 
	 * @return array(xBox)
	 * @static
	 */
	function findAll()
	{
		return xBoxDAO::find();
	}
	
	/**
	 * Returns all boxes in an area.
	 * 
	 * @return array(xBox)
	 * @static
	*/
	function find($area = '')
	{
		$boxes = array();
		if(empty($area))
		{
			$result = xDB::getDB()->query("SELECT * FROM box");
		}
		else
		{
			$result = xDB::getDB()->query("SELECT * FROM box WHERE area = '%s'",$area);
		}
		
		while($row = xDB::getDB()->fetchArray($result))
		{
			$current_box = new xBox($row['name'],$row['title'],$row['type'],$row['weight'],$row['filterset'],$row['area']);
			$boxes[] = $current_box;
		}
		return $boxes;
	}
};











?>