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
		return new xBoxGroup($row->name,$row->description,$row->render,$boxes);
	}
	
	/**
	 *
	 */
	function findBoxNamesAndTypesByGroup($group_name)
	{
		$boxes = array();
		$result = xDB::getDB()->query("SELECT box.name,box.type FROM box,box_to_group WHERE 
			box_to_group.box_group = '%s' AND box.name = box_to_group.box_name",$group_name);
		while($row = xDB::getDB()->fetchObject($result))
		{
			$boxes[] = $row;
		}
		
		return $boxes;
	}
	
	/**
	 * Find box groups.
	 * 
	 * @return array(xBox) An xBoxGroup array with empty m_boxes member.
	 * @static
	 */
	function findBoxGroups($name)
	{
		$where['box_to_group']['box_name']['type'] = "'%s'";
		$where['box_to_group']['box_name']['connector'] = "AND";
		$where['box_to_group']['box_name']['value'] = $name;
		
		$where['box_group']['name']['join'][] = "box_to_group.box_group";
		$where['box_group']['name']['connector'] = "AND";
		
		$result = xDB::getDB()->autoQuery('SELECT',array(),$where);
		$objs = array();
		while($row = xDB::getDB()->fetchObject($result))
		{
			$objs[] = xBoxGroupDAO::_boxGroupFromRow($row,NULL);
		}
		return $objs;
	}
	
	
	/**
	 * Find box groups.
	 * 
	 * @return array(xBox) An xBoxGroup array with empty m_boxes member.
	 * @static
	 */
	function find($renderizable)
	{
		$where['box_group']['render']['type'] = "%d";
		$where['box_group']['render']['connector'] = "AND";
		$where['box_group']['render']['value'] = $renderizable;
		
		$result = xDB::getDB()->autoQuery('SELECT',array(),$where);
		$objs = array();
		while($row = xDB::getDB()->fetchObject($result))
		{
			$objs[] = xBoxGroupDAO::_boxGroupFromRow($row,NULL);
		}
		return $objs;
	}
};

?>