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
 * Box type Data Access Object
 */
class xBoxTypeDAO
{
	/**
	* Insert a new box type.
	*
	* @param xBoxType $box_type
	* @return bool FALSE on error
	* @static 
	*/
	function insert($box_type)
	{
		$field_names = "name,description,user_editable";
		$field_values = "'%s','%s',%d";
		$values = array($box_type->m_name,$box_type->m_description,$box_type->m_user_editable);
		
		return xDB::getDB()->query("INSERT INTO box_type($field_names) VALUES($field_values)",$values);
	}
	
	/**
	* Delete an existing box type. Based on key.
	*
	* @param string $box_type_name
	* @return bool FALSE on error
	* @static 
	*/
	function delete($box_type_name)
	{
		return xDB::getDB()->query("DELETE FROM box_type WHERE name = '%s'",$box_type_name);
	}
	
	
	/**
	 * @access private
	 */ 
	function _boxTypeFromRow($row)
	{
		return new xBoxType($row->name,$row->description,$row->user_editable);
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
		$result = xDB::getDB()->query("SELECT * FROM box_type");
		while($row = xDB::getDB()->fetchObject($result))
		{
			$boxes[] = xBoxDAO::_boxTypeFromRow($row);
		}
		
		return $boxes;
	}
};

?>