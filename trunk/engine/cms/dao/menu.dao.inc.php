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
 * "Menu" Data Access Object
 */
class xMenuDAO
{
	function xMenuDAO()
	{
		assert(FALSE);
	}
	
	/**
	 * Insert a new menu static.
	 *
	 * @param xMenuStatic $menu
	 * @return bool FALSE on error
	 * @static 
	 */
	function insert($menu,$transaction = TRUE)
	{
		if($transaction)
			xDB::getDB()->startTransaction();
		
		if(! xBoxDAO::insert($menu))
			return false;
		
		if(! xMenuDAO::_insertItems($menu->m_name,$menu->m_items,0))
			return false;
		
		if($transaction)
			xDB::getDB()->commit();
		
		return true;
	}
	
	/**
	 * @access private
	 * @return bool FALSE on error
	 * @static
	 */
	function _insertItems($menuname,$items,$parentid)
	{
		if(!empty($items))
		{
			foreach($items as $item)
			{
				$id = xUniqueId::generate('menu_items');
				$field_names = "id,box_name,label,link,weight";
				$field_values = "%d,'%s','%s','%s',%d";
				$values = array($id,$menuname,$item->m_label,$item->m_link,$item->m_weight);
				
				if(!empty($parentid))
				{
					$field_names .= ',parent';
					$field_values .= ",%d";
					$values[] = $parentid;
				}
				
				
				if(! xDB::getDB()->query("INSERT INTO menu_items($field_names) VALUES($field_values)",$values))
					return false;
				
				if(! xMenuDAO::_insertItems($menuname,$item->m_subitems,$id))
					return false;
			}
		}
		
		return true;
	}
	
	/**
	 * Update an existing static menu.
	 *
	 * @param xMenuStatic $menu
	 * @return bool FALSE on error
	 * @static 
	 */
	function update($menu,$transaction = TRUE)
	{
		if($transaction)
			xDB::getDB()->startTransaction();
		
		if(!xBoxDAO::update($menu))
			return false;
		
		//clear all menu items
		if(! xDB::getDB()->query("DELETE FROM menu_items WHERE box_name = '%s')",$menu->m_name))
			return false;
		
		//insert new
		if(! xMenuDAO::_insertItems($menu->m_name,$menu->m_items,0))
			return false;
		
		if($transaction)
			xDB::getDB()->commit();
		
		return true;
	}
	
	
	/**
	* Delete an existing static menu. Based on key.
	*
	* @param xMenuStatic $menu
	* @return bool FALSE on error
	* @static 
	*/
	function delete($menu)
	{
		return xBoxDAO::delete($menu);
	}
	
	
	/**
	 *
	 * @return xMenuItem
	 * @static
	 * @access private
	 */
	function _menuitemFromRow($row_object)
	{
		return new xMenuItem($row_object->label,$row_object->link,$row_object->weight);
	}
	
	/**
	 *
	 * @return array(xMenuItems)
	 * @access private
	 * @static
	 */
	function _getMenuItems($menuname,$parent)
	{
		$items = array();
		if($parent === 0)
		{
			$result = xDB::getDB()->query("SELECT * FROM menu_items WHERE menu_items.box_name = '%s' AND 
				menu_items.parent IS NULL",$menuname);
		
			while($row = xDB::getDB()->fetchObject($result))
			{
				$newitem = xMenuDAO::_menuitemFromRow($row);
				$newitem->m_subitems = xMenuDAO::_getMenuItems($menuname,$row->id);
				
				$items[] = $newitem;
			}
		}
		else
		{
			$result = xDB::getDB()->query("SELECT * FROM menu_items WHERE menu_items.box_name = '%s' AND 
				menu_items.parent = %d",$menuname,$parent);
		
			while($row = xDB::getDB()->fetchObject($result))
			{
				$newitem = xMenuDAO::_menuitemFromRow($row);
				$newitem->m_subitems = xMenuDAO::_getMenuItems($menuname,$row->id);
				
				$items[] = $newitem;
			}
		}
		
		return $items;
	}
	
	/**
	 * Extract specific data for static menu and build and return a new xMenuStatic
	 *
	 * @param xBox $box
	 * @return xMenuStatic
	 * @static
	 */
	function toSpecificBox($box)
	{
		$items = xMenuDAO::_getMenuItems($box->m_name,0);
		
		return new xMenu($box->m_name,$box->m_title,$box->m_type,$box->m_weight,$items,
			$box->m_filterset,$box->m_area);
	}
};











?>