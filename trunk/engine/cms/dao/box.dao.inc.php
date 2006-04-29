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
	* @param $boxdto (xBoxDTO) the data to insert
	* @static 
	*/
	function insert($boxdto)
	{
		$field_names = "name,title,is_dynamic,content,content_format";
		$field_values = "'%s','%s',%d,'%s','%s'";
		$values = array($this->m_name,$this->m_title,$this->m_is_dynamic,$this->m_content,$this->m_content_format);
		
		if(!empty($this->m_area))
		{
			$field_names .= ',area';
			$field_values .= ",'%s'";
			$values[] = $this->m_area;
		}
		
		xDB::getDB()->query("INSERT INTO box($field_names) VALUES($field_values)",$values);
	}
	
	
	/**
	* Update an existing box.
	*/
	function update()
	{
		$fields = "content_format = '%s',title = '%s',content = '%s'";
		$values = array($this->m_content_format,$this->m_title,$this->m_content);
		
		if(!empty($this->m_area))
		{
			$fields .= ",area = '%s'";
			$values[] = $this->m_area;
		}
		
		$values[] = $this->m_name;
		xDB::getDB()->query("UPDATE box SET $fields WHERE name = '%s'",$values);
	}
	
	
	/**
	* Delete an existing box. Based on key.
	*/
	function delete()
	{
		xDB::getDB()->query("DELETE FROM box WHERE name = '%s'",$this->m_name);
	}
	
	/**
	 * Returns all registered boxes.
	 * 
	 * @return (array(xBoxDAO)) 
	 * @static
	 */
	function findAll()
	{
		return xBoxDAO::find();
	}
	
	/**
	 * Returns all boxs in an area.
	 * @return (array(xBoxDAO)) 
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
			$current_box = new xBoxDAO($row['name'],$row['title'],$row['content'],$row['content_format'],$row['is_dynamic'],$row['area']);
			$boxes[] = $current_box;
		}
		return $boxes;
	}
};











?>