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
 * 
 */
class xWidgetGroupDAO
{
	function xWidgetGroupDAO()
	{
		assert(false);	
	}
	
	/**
	 *
	 * @return bool FALSE on error
	 * @static
	 */
	function insert($obj)
	{
		$db =& xDB::getDB();
		$db->startTransaction();
		
		$db->query("INSERT INTO widget_group(name) VALUES ('%s')",$obj->m_name);
		
		xWidgetGroupDAO::_insertChilds($obj);
		
		if(!$db->commitTransaction())
			return false;
		return true;
	}
	
	
	/**
	 * 
	 */
	function _insertChilds($group)
	{
		$db =& xDB::getDB();
		$db->startTransaction();
		
		foreach($group->m_widgets as $wid)
			$db->query("INSERT INTO group_to_widget(group_name,class_name,widget_name) VALUES 
				('%s','%s','%s')",$group->m_name,get_class($wid),$wid->m_name);
		
		if(!$db->commitTransaction())
			return false;
			
		return true;
	}
	
	
	/**
	 * 
	 */
	function update($obj)
	{
		$db =& xDB::getDB();
		$db->startTransaction();
		
		$db->query("DELETE FROM group_to_widget WHERE group_name = '%s'",$obj->m_name);
		xWidgetGroupDAO::_insertWidgets($menu->m_name,$menu->m_items,0);
		
		if(!$db->commitTransaction())
			return false;
		return true;
	}
	
	
	/**
	 * Deletes an item type.
	 *
	 * 
	 * @param string $typename
	 * @return bool FALSE on error
	 * @static
	 */
	function delete($name)
	{
	}
	
	/**
	 * 
	 */
	function _widgetGroupFromRow($row,$widgets)
	{
		return new xWidgetGroup($row->name,$widgets);
	}
	
	
	/**
	 * 
	 */
	function _findChilds($group_name)
	{
		$db =& xDB::getDB();
		$where[0]["clause"] = "group_to_widget.group_name = '%s'";
		$where[0]["connector"] = "AND";
		$where[0]["value"] = $group_name;
	 
		$result = $db->autoQuerySelect('*','group_to_widget',$where);
		$objs = array();
		while($row = $db->fetchObject($result))
		{
			
			$class_name = $row->class_name;
			$objs[] = call_user_func(array($class_name,'load'),$row->widget_name);
		}
		
		return $objs;
	}
	
	
	
	/**
	 * Retrieves all item type.
	 *
	 * @return array(xItemType)
	 * @static
	 */
	function find($name)
	{
		$db =& xDB::getDB();
		$where[0]["clause"] = "widget_group.name = '%s'";
		$where[0]["connector"] = "AND";
		$where[0]["value"] = $name;
	 
		$result = $db->autoQuerySelect('*','widget_group',$where);
		$objs = array();
		while($row = $db->fetchObject($result))
		{
			$childs = xWidgetGroupDAO::_findChilds($row->m_name);
			$objs[] = xWidgetGroupDAO::_widgetGroupFromRow($row,$childs);
		}
		return $objs;
	}
}


?>