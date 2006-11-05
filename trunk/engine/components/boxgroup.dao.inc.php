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
		$db =& xDB::getDB();
		$db->startTransaction();
			
		$db->query("INSERT INTO box_group(name,description,render) VALUES('%s','%s',%d)",
			$box_group->m_name,$box_group->m_description,$box_group->m_render);
			
		xBoxGroupDAO::_insertBoxes($box_group);
			
		if(!$db->commitTransaction())
			return false;
		
		return true;
	}
	
	/**
	 * @access private
	 */
	function _insertBoxes($box_group)
	{
		$db =& xDB::getDB();
		foreach($box_group->m_boxes as $box)
		{
			if(! $db->query("INSERT INTO box_to_group(box_group,box_name) VALUES('%s','%s')",
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
		$db =& xDB::getDB();
		$db->startTransaction();
		
		$where[0]["clause"] = "name = '%s'";
		$where[0]["connector"] = "AND";
		$where[0]["value"] = $box_group->m_name;
		
		$record[1]["name"] = 'render';
		$record[1]["type"] = "%d";
		$record[1]["value"] = $box_group->m_render;
		
		$record[0]["name"] = 'description';
		$record[0]["type"] = "'%s'";
		$record[0]["value"] = $box_group->m_description;
		
		$record[1]["name"] = 'render';
		$record[1]["type"] = "%d";
		$record[1]["value"] = $box_group->m_render;
		
		$record[2]["name"] = 'render';
		$record[2]["type"] = "%d";
		$record[2]["value"] = $box_group->m_render;
		
		$db->autoQueryUpdate('box_group',$record,$where);
		$db->query("DELETE FROM box_to_group WHERE box_group = '%s'",$box_group->m_name);
		xBoxGroupDAO::_insertBoxes($box_group);
			
		if(!$db->commitTransaction())
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
		$db =& xDB::getDB();
		return $db->query("DELETE FROM box_group WHERE name = '%s'",$box_group_name);
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
		$db =& xDB::getDB();
		$boxes = array();
		$result = $db->query("SELECT box.name,box.type FROM box,box_to_group WHERE 
			box_to_group.box_group = '%s' AND box.name = box_to_group.box_name",$group_name);
		while($row = $db->fetchObject($result))
		{
			$boxes[] = $row;
		}
		
		return $boxes;
	}
	
	/**
	 * Find box groups.
	 * 
	 * @return
	 * @static
	 */
	function findBoxGroups($name)
	{
		$db =& xDB::getDB();
		$where[0]["clause"] = "box_to_group.box_name = '%s'";
		$where[0]["connector"] = "AND";
		$where[0]["value"] = $name;
	 
		$where[1]["clause"] = "box_group.name = box_to_group.box_group";
		$where[1]["connector"] = "AND";
		
		$result = $db->autoQuerySelect('*','box_group,box_to_group',$where);
		$objs = array();
		while($row = $db->fetchObject($result))
			$objs[] = xBoxGroupDAO::_boxGroupFromRow($row,NULL);
		return $objs;
	}
	
	/**
	 * Find box groups.
	 * 
	 * @return array(xBox) An xBoxGroup array with empty m_boxes member.
	 * @static
	 */
	function load($name)
	{
		$db =& xDB::getDB();
		$where['box_group']['name']['type'] = "'%s'";
		$where['box_group']['name']['connector'] = "AND";
		$where['box_group']['name']['value'] = $name;
		
		$result = $db->autoQuery('SELECT',array(),$where);
		$objs = array();
		if($row = $db->fetchObject($result))
		{
			return xBoxGroupDAO::_boxGroupFromRow($row,NULL);
		}
		return NULL;
	}
	
	
	/**
	 * Find box groups.
	 * 
	 * @return array(xBox) An xBoxGroup array with empty m_boxes member.
	 * @static
	 */
	function find($renderizable)
	{
		$db =& xDB::getDB();
		$where['box_group']['render']['type'] = "%d";
		$where['box_group']['render']['connector'] = "AND";
		$where['box_group']['render']['value'] = $renderizable;
		
		$result = $db->autoQuery('SELECT',array(),$where);
		$objs = array();
		while($row = $db->fetchObject($result))
		{
			$objs[] = xBoxGroupDAO::_boxGroupFromRow($row,NULL);
		}
		return $objs;
	}
};

?>