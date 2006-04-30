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
	* @static 
	*/
	function insert($box)
	{
		$field_names = "name,title,is_dynamic,content,content_format";
		$field_values = "'%s','%s',%d,'%s','%s'";
		$values = array($box->m_id,$box->m_title,$box->m_is_dynamic,$box->m_content,$box->m_content_format);
		
		if(!empty($box->m_area))
		{
			$field_names .= ',area';
			$field_values .= ",'%s'";
			$values[] = $box->m_area;
		}
		
		xDB::getDB()->query("INSERT INTO box($field_names) VALUES($field_values)",$values);
	}
	
	
	/**
	* Update an existing box.
	*
	* @param xBox $box
	* @static 
	*/
	function update($box)
	{
		$fields = "content_format = '%s',title = '%s',content = '%s'";
		$values = array($box->m_content_format,$box->m_title,$box->m_content);
		
		if(!empty($box->m_area))
		{
			$fields .= ",area = '%s'";
			$values[] = $box->m_area;
		}
		
		$values[] = $box->m_id;
		xDB::getDB()->query("UPDATE box SET $fields WHERE name = '%s'",$values);
	}
	
	
	/**
	* Delete an existing box. Based on key.
	*
	* @param xBox $box
	* @static 
	*/
	function delete($box)
	{
		xDB::getDB()->query("DELETE FROM box WHERE name = '%s'",$box->m_id);
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
			$current_box = new xBox($row['name'],$row['title'],$row['content'],$row['content_format'],$row['is_dynamic'],$row['area']);
			$boxes[] = $current_box;
		}
		return $boxes;
	}
};











?>