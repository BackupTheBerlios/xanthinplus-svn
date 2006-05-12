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
 * "Menu Static" Data Access Object
 */
class xMenuStaticDAO
{
	/**
	 * Insert a new menu static.
	 *
	 * @param xMenuStatic $menu
	 * @static 
	 */
	function insert($menu)
	{
		xDB::getDB()->startTransaction();
		xBoxDAO::insert($menu);
		
		foreach($menu->m_items as $item)
		{
			xDB::getDB()->query("INSERT INTO menu_static(box_name,text,link) VALUES('%s','%s','%s')",
				$menu->m_name,$item->m_text,$item->m_link);
		}
		
		xDB::getDB()->commit();
	}
	
	
	/**
	 * Update an existing static menu.
	 *
	 * @param xMenuStatic $menu
	 * @static 
	 */
	function update($menu)
	{
		xDB::getDB()->startTransaction();
		xBoxDAO::update($menu);
		
		//clear all menu items
		xDB::getDB()->query("DELETE FROM menu_static WHERE box_name = '%s')",$menu->m_name);
		
		//insert new
		foreach($menu->m_items as $item)
		{
			xDB::getDB()->query("INSERT INTO menu_static(box_name,text,link) VALUES('%s','%s','%s')",
				$menu->m_name,$item->m_text,$item->m_link);
		}
		
		xDB::getDB()->commit();
	}
	
	
	/**
	* Delete an existing static menu. Based on key.
	*
	* @param xMenuStatic $menu
	* @static 
	*/
	function delete($menu)
	{
		xBoxDAO::delete($menu);
	}
	
	/**
	 * Extract specific data for static menu and build and return a new xMenuStatic
	 *
	 * @param xBox $menu
	 * @return xMenuStatic
	 * @static
	 */
	function toSpecificBox($menu)
	{
		$result = xDB::getDB()->query("SELECT * FROM menu_static WHERE box_name = '%s'",$menu->m_name);
		
		$items = array();
		while($row = xDB::getDB()->fetcObject($result))
		{
			$items[] = new xMenuItem($row->text,$row->link);
		}
		
		return new xMenuStatic($box->m_name,$box->m_title,$box->m_type,$items,$box->m_filterset,$box->m_area);
	}
};











?>