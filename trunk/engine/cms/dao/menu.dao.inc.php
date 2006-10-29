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
	 * @param xMenu $menu
	 * @return bool FALSE on error
	 * @static 
	 */
	function insert($menu)
	{
		xDB::getDB()->startTransaction();
		
		xBoxI18NDAO::insert($menu);
		
		xMenuDAO::_insertItems($menu->m_name,$menu->m_items,0);
		
		if(!xDB::getDB()->commitTransaction())
			return false;
		
		return true;
	}
	
	
	/**
	* Insert a new menu translation
	*
	* @param xBoxI18 $box
	* @return bool FALSE on error
	* @static 
	*/
	function insertTranslation($box)
	{
		xDB::getDB()->startTransaction();
		
		xBoxI18NDAO::insertTranslation($menu);
		
		xMenuDAO::_insertItems($menu->m_name,$menu->m_items,0);
		
		if(!xDB::getDB()->commitTransaction())
			return false;
		
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
				$id = xUniqueId::generate('menu_item');
				$field_names = "id,box_name,label,link,weight,lang";
				$field_values = "%d,'%s','%s','%s',%d,'%s'";
				$values = array($id,$menuname,$item->m_label,$item->m_link,$item->m_weight,$item->m_lang);
				
				if(!empty($parentid))
				{
					$field_names .= ',parent';
					$field_values .= ",%d";
					$values[] = $parentid;
				}
				
				
				if(! xDB::getDB()->query("INSERT INTO menu_item($field_names) VALUES($field_values)",$values))
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
	function update($menu)
	{
		xDB::getDB()->startTransaction();
		
		xBoxI18NDAO::update($menu);
		
		//clear all menu items
		xDB::getDB()->query("DELETE FROM menu_item WHERE box_name = '%s' AND lang = '%s'",$menu->m_name,$menu->m_lang);
		
		//insert new
		xMenuDAO::_insertItems($menu->m_name,$menu->m_items,0);
		
		if(!xDB::getDB()->commitTransaction())
			return false;
		
		return true;
	}
	
	
	/**
	 *
	 * @return xMenuItem
	 * @static
	 * @access private
	 */
	function _menuitemFromRow($row_object)
	{
		return new xMenuItem($row_object->id,$row_object->label,$row_object->link,$row_object->weight,$row_object->lang);
	}
	
	/**
	 *
	 * @return array(xMenuItems)
	 * @access private
	 * @static
	 */
	function _getMenuItems($parent,$menuname,$lang)
	{
		$where[0]["clause"] = "menu_item.box_name = '%s'";
		$where[0]["connector"] = "AND";
		$where[0]["value"] = $menuname;
	 
		if($parent === NULL)
			$where[1]["clause"] = "menu_item.parent IS NULL";
		else
		{
			$where[1]["clause"] = "menu_item.parent = '%s'";
			$where[1]["value"] = $parent;
		}
		$where[1]["connector"] = "AND";
		
		$where[2]["clause"] = "menu_item.lang = '%s'";
		$where[2]["connector"] = "AND";
		$where[2]["value"] = $lang;
		
		$result = xDB::getDB()->autoQuerySelect('*','menu_item',$where);
		$objs = array();
		while($row = xDB::getDB()->fetchObject($result))
		{
			$newitem = xMenuDAO::_menuitemFromRow($row);
			$newitem->m_subitems = xMenuDAO::_getMenuItems($row->id,$menuname,$lang);
			$objs[] = $newitem;
		}
		return $objs;
	}
	
	
	/**
	 *
	 * @return xMenuItem
	 * @static
	 * @access private
	 */
	function _menuFromRow($row,$items)
	{
		return new xMenu($row->name,$row->type,$row->weight,
			new xShowFilter($row->show_filters_type,$row->show_filters),$row->title,$row->lang,$items);
	}
	
	/**
	 * @access private
	 */
	function _find($name,$type,$lang,$flexible_lang)
	{
		if($flexible_lang && $lang !== NULL)
		{
			//now extract all menus with specified lang
			$menus = xMenuDAO::_find($name,$type,NULL,false);
			
			//now group by name and lang
			$grouped = array();
			foreach($menus as $menu)
				$grouped[$menu->m_name][$menu->m_lang] = $menu;
				
			$ret = array();
			//extract menus
			foreach($grouped as $name => $ignore)
			{
				if(isset($grouped[$name][$lang])) //specific lang
					$ret[] = $grouped[$name][$lang];
				elseif(isset($grouped[$name][xSettings::get('default_lang')])) //default lang
					$ret[] = $grouped[$name][xSettings::get('default_lang')];
				else	//first found lang
					$ret[] = reset($grouped[$name]);
			}
			
			return $ret;
		}
		else
		{
			$where[0]["clause"] = "box_i18n.box_name = '%s'";
			$where[0]["connector"] = "AND";
			$where[0]["value"] = $name;
		 
			$where[1]["clause"] = "box_i18n.lang = '%s'";
			$where[1]["connector"] = "AND";
			$where[1]["value"] = $lang;
			
			$where[2]["clause"] = "box.name = box_i18n.box_name";
			$where[2]["connector"] = "AND";
			
			$where[3]["clause"] = "box.type = '%s'";
			$where[3]["connector"] = "AND";
			$where[3]["value"] = $type;
			
			$result = xDB::getDB()->autoQuerySelect('*','box,box_i18n',$where);
			$objs = array();
			while($row = xDB::getDB()->fetchObject($result))
				$objs[] = xMenuDAO::_menuFromRow($row,NULL);
			return $objs;
		}
	}
	
	
	/**
	 * If flexible lang, first select given lang, then default lang, then first found lang.
	 */
	function find($name,$type,$lang,$flexible_lang)
	{
		$menus = xMenuDAO::_find($name,$type,$lang,$flexible_lang);
		foreach($menus as $menu)
			$menu->m_items = xMenuDAO::_getMenuItems(NULL,$menu->m_name,$menu->m_lang);
		
		return $menus;
	}
};


?>