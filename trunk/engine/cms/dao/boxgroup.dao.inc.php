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
 * Box Group Data Access Object
 */
class xBoxGroupDAO
{
	/**
	* Insert a new box.
	*
	* @param xBoxGroup $box_group
	* @return bool FALSE on error
	* @static 
	*/
	function insert($box_group)
	{
		xDB::getDB()->startTransaction();
			
		xDB::getDB()->query("INSERT INTO box_group(name,render) VALUES('%s',%d)",
			$box_group->m_name,$box_group->m_render);
			
		xBoxGroupDAO::_insertBoxes($box_group);
			
		if(!xDB::getDB()->commitTransaction())
			return false;
		
		return true;
	}
	
	/**
	 * @access private
	 */
	function _insertBoxes($box_group)
	{
		foreach($box_group->m_boxes as $box)
		{
			if(! xDB::getDB()->query("INSERT INTO box_to_group(box_group,box_name) VALUES('%s','%s')",
				$box_group->m_name,$box->m_name))
				return false;
		}
		
		return true;
	}
	
	
	/**
	 * Update an existing box group.
	 *
	 * @param xBox $box
	 * @return bool FALSE on error
	 * @static 
	 */
	function update($box_group)
	{
		xDB::getDB()->startTransaction();
		
		xDB::getDB()->query("UPDATE box_group SET render = %d WHERE box_group_name = '%s'",
			$box_group->m_name,$box_group->m_render);
			
		xDB::getDB()->query("DELETE FROM box_group WHERE box_group_name = '%s'",$box_group->m_name);
			
		xBoxGroupDAO::_insertBoxes($box_group);
			
		if(!xDB::getDB()->commitTransaction())
			return false;
		
		return true;
	}
	
	
	/**
	* Delete an existing box group. Based on key.
	*
	* @return bool FALSE on error
	* @static 
	*/
	function delete($box_group_name)
	{
		return xDB::getDB()->query("DELETE FROM box_group WHERE name = '%s'",$box_group_name);
	}
	
	
	/**
	 * @access private
	 */ 
	function _boxGroupFromRow($row,$boxes)
	{
		return new xBoxGroup($row->name,$row->render,$boxes);
	}
	
	/**
	 *
	 */
	function _findBoxesByGroup($group_name,$lang)
	{
		$boxes = array();
		$result = xDB::getDB()->query("SELECT box.name as boxname,type  FROM box,box_to_group WHERE 
			box_to_group.box_group = '%s' AND box.name = box_to_group.box_name",$group_name);
		while($row = xDB::getDB()->fetchObject($result))
		{
			$box = xBox::fetchBox($row->boxname,$row->type,$lang);
			if($box != NULL)
				$boxes[] = $box;
		}
		
		return $boxes;
	}
	
	/**
	 * Find box groups.
	 * 
	 * @return array(xBox)
	 * @static
	 */
	function find($renderizable,$lang)
	{
		$groups = array();
		$result = xDB::getDB()->query("SELECT * FROM box_group WHERE render = %d",$renderizable);
		while($row = xDB::getDB()->fetchObject($result))
		{
			$boxes = xBoxGroupDAO::_findBoxesByGroup($row->name,$lang);
			$groups[] = xBoxGroupDAO::_boxGroupFromRow($row,$boxes);
		}
		
		return $groups;
	}
};

?>