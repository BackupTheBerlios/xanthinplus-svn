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
	* @return bool FALSE on error
	* @static 
	*/
	function insert($box)
	{
		$db =& xDB::getDB();
		$field_names = "name,type,weight,show_filters_type,show_filters";
		$field_values = "'%s','%s',%d,'%s','%s'";
		$values = array($box->m_name,$box->m_type,$box->m_weight,
			$box->m_show_filter->m_type,$box->m_show_filter->m_filters);
		
		return $db->query("INSERT INTO box($field_names) VALUES($field_values)",$values);
	}
	
	
	/**
	 * Update an existing box.
	 *
	 * @param xBox $box
	 * @return bool FALSE on error
	 * @static 
	 */
	function update($box)
	{
		$db =& xDB::getDB();
		$fields = "weight = %d, show_filters_type = %d,show_filters = '%s'";
		$values = array($box->m_weight,$box->m_show_filter->m_type,$box->m_show_filter->m_filters);
		
		$values[] = $box->m_name;
		return $db->query("UPDATE box SET $fields WHERE name = '%s'",$values);
	}
	
	
	/**
	* Delete an existing box. Based on key.
	*
	* @param string $box_name
	* @return bool FALSE on error
	* @static 
	*/
	function delete($box_name)
	{
		$db =& xDB::getDB();
		return $db->query("DELETE FROM box WHERE name = '%s'",$box_name);
	}
	
	
	/**
	 * @access private
	 */ 
	function _boxFromRow($row)
	{
		$db =& xDB::getDB();
		return new xBox($row->name,$row->type,$row->weight,
			new xShowFilter($row->show_filters_type,$row->show_filters));
	}
	
	/**
	 * Returns all registered boxes.
	 * 
	 * @return array(xBox)
	 * @static
	 */
	function find($name,$type)
	{
		$db =& xDB::getDB();
		$where[0]["clause"] = "box.name = '%s'";
		$where[0]["connector"] = "AND";
		$where[0]["value"] = $name;
	 
		$where[1]["clause"] = "box.type = '%s'";
		$where[1]["connector"] = "AND";
		$where[1]["value"] = $type;
		
		$result = $db->autoQuerySelect('*','box',$where);
		$objs = array();
		while($row = $db->fetchObject($result))
			$objs[] = xBoxDAO::_boxFromRow($row,NULL);
		return $objs;
	}
};



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
		$db =& xDB::getDB();
		$field_names = "name,description";
		$field_values = "'%s','%s'";
		$values = array($box_type->m_name,$box_type->m_description);
		
		return $db->query("INSERT INTO box_type($field_names) VALUES($field_values)",$values);
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
		$db =& xDB::getDB();
		return $db->query("DELETE FROM box_type WHERE name = '%s'",$box_type_name);
	}
	
	
	/**
	 * @access private
	 */ 
	function _boxTypeFromRow($row)
	{
		$db =& xDB::getDB();
		return new xBoxType($row->name,$row->description);
	}
	
	
	/**
	 * Returns all registered boxes.
	 * 
	 * @return array(xBox)
	 * @static
	 */
	function findAll()
	{
		$db =& xDB::getDB();
		$boxes = array();
		$result = $db->query("SELECT * FROM box_type");
		while($row = $db->fetchObject($result))
		{
			$boxes[] = xBoxDAO::_boxTypeFromRow($row);
		}
		
		return $boxes;
	}
};





/**
 * Box I18N Data Access Object
 */
class xBoxI18NDAO
{
	/**
	* Insert a new xBoxI18.
	*
	* @param xBoxI18 $box
	* @return bool FALSE on error
	* @static 
	*/
	function insert($box)
	{
		$db =& xDB::getDB();
		$db->startTransaction();
		
		xBoxDAO::insert($box);
		
		$db->query("INSERT INTO box_i18n(box_name,title,lang) VALUES('%s','%s','%s')",
			$box->m_name,$box->m_title,$box->m_lang);
		
		if(!$db->commitTransaction())
			return false;
			
		return true;
	}
	
	/**
	* Insert a new xBoxI18 translation
	*
	* @param xBoxI18 $box
	* @return bool FALSE on error
	* @static 
	*/
	function insertTranslation($box)
	{
		$db =& xDB::getDB();
		return $db->query("INSERT INTO box_i18n(box_name,title,lang) VALUES('%s','%s','%s')",
			$box->m_name,$box->m_title,$box->m_lang);
	}
	
	/**
	* Delete an existing box translation.
	*
	* @param string $box_name
	* @return bool FALSE on error
	* @static 
	*/
	function deleteTranslation($box_name,$lang)
	{
		$db =& xDB::getDB();
		return $db->query("DELETE FROM box_i18n WHERE box_name = '%s', lang ='%s'",$box_name,$lang);
	}
	
	
	/**
	 * Update an existing box.
	 *
	 * @param xBox $box
	 * @return bool FALSE on error
	 * @static 
	 */
	function update($box)
	{
		$db =& xDB::getDB();
		$db->startTransaction();
		
		xBoxDAO::update($box);
		
		$db->query("UPDATE box_i18n SET title = '%s' WHERE box_name = '%s' AND lang = '%s'",
			$box->m_title,$box->m_name,$box->m_lang);
		
		if(!$db->commitTransaction())
			return false;
			
		return true;
	}
	
	/**
	 * @access private
	 */ 
	function _boxI18nFromRow($row)
	{
		return new xBoxI18N($row->name,$row->type,$row->weight,
			new xShowFilter($row->show_filters_type,$row->show_filters),$row->title,$row->lang);
	}
	
	
	/**
	 * @acces protected
	 */
	function _selectFlexiLang($boxes,$lang)
	{
		$db =& xDB::getDB();
		//now group by name and lang
		$grouped = array();
		foreach($boxes as $box)
			$grouped[$box->m_name][$box->m_lang] = $box;
			
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
	
	/**
	 * If flexible lang, first select given lang, then default lang, then first found lang.
	 */
	function find($name,$type,$lang,$flexible_lang)
	{
		$db =& xDB::getDB();
		if($flexible_lang && $lang !== NULL)
		{
			//now extract all menus with specified lang
			$objs = xBoxI18NDAO::find($name,$type,NULL,false);
			return xBoxI18NDAO::_selectFlexiLang($objs,$lang);
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
			
			$result = $db->autoQuerySelect('*','box,box_i18n',$where);
			$objs = array();
			while($row = $db->fetchObject($result))
				$objs[] = xBoxI18NDAO::_boxi18nFromRow($row);
			return $objs;
		}
	}
	
	
	/**
	 *
	 */
	function existsTranslation($name,$lang)
	{
		$db =& xDB::getDB();
		$result = $db->query("SELECT lang FROM box_i18n WHERE box_i18n.box_name = '%s' AND 
			box_i18n.lang = '%s'",$name,$lang);
		if($row = $db->fetchObject($result))
			return true;
		
		return false;
	}
	
};



/**
 * "Box Custom" Data Access Object
 */
class xBoxCustomDAO
{
	/**
	 * Insert a new Custom box.
	 *
	 * @param xBoxCustom $box
	 * @return bool FALSE on error
	 * @static 
	 */
	function insert($box)
	{
		$db =& xDB::getDB();
		$db->startTransaction();
		
		xBoxI18NDAO::insert($box);
		
		$db->query("INSERT INTO box_custom(box_name,lang,content,content_filter) VALUES('%s','%s','%s','%s')",
			$box->m_name,$box->m_lang,$box->m_content,$box->m_content_filter);
		
		if(!$db->commitTransaction())
			return false;
		
		return TRUE;
	}
	
	/**
	* Insert a new xBoxCustom translation
	*
	* @param xBoxI18 $box
	* @return bool FALSE on error
	* @static 
	*/
	function insertTranslation($box)
	{
		$db =& xDB::getDB();
		$db->startTransaction();
		
		xBoxI18NDAO::insertTranslation($box);
		
		$db->query("INSERT INTO box_custom(box_name,lang,content,content_filter) VALUES('%s','%s','%s','%s')",
			$box->m_name,$box->m_lang,$box->m_content,$box->m_content_filter);
		
		if(!$db->commitTransaction())
			return false;
		
		return TRUE;
	}
	
	
	/**
	 * Update an existing custom box.
	 *
	 * @param xBoxCustom $box
	 * @return bool FALSE on error
	 * @static 
	 */
	function update($box)
	{
		$db =& xDB::getDB();
		$db->startTransaction();
		
		xBoxI18NDAO::update($box);
		
		$db->query("UPDATE box_custom SET content = '%s',content_filter = '%s' 
			WHERE box_name = '%s' AND lang = '%s'",
			$box->m_content,$box->m_content_filter,$box->m_name,$box->m_lang);
		
		if(!$db->commitTransaction())
			return false;
		
		return TRUE;
	}
	
	/**
	 *
	 */
	function _boxcustomFromRow($row)
	{
		$db =& xDB::getDB();
		return new xBoxCustom($row->name,$row->type,$row->weight,
			new xShowFilter($row->show_filters_type,$row->show_filters),$row->title,$row->lang,
			$row->content,$row->content_filter);
	}
	
	/**
	 * Extract specific data for static box and build and return a new xBoxStatic
	 *
	 * @param xBox $box
	 * @return xBoxCustom or NULL no error
	 * @static
	 */
	function find($name,$type,$lang,$flexible_lang)
	{
		$db =& xDB::getDB();
		if($flexible_lang && $lang !== NULL)
		{
			//now extract all menus with specified lang
			$objs = xBoxCustomDAO::find($name,$type,NULL,false);
			return xBoxI18NDAO::_selectFlexiLang($objs,$lang);
		}
		else
		{
			$where[0]["clause"] = "box_custom.box_name = '%s'";
			$where[0]["connector"] = "AND";
			$where[0]["value"] = $name;
			
			$where[0]["clause"] = "box_custom.lang = '%s'";
			$where[0]["connector"] = "AND";
			$where[0]["value"] = $lang;
			
			$where[1]["clause"] = "box_i18n.box_name = box_custom.box_name";
			$where[1]["connector"] = "AND";
			
			$where[1]["clause"] = "box_i18n.lang = box_custom.lang";
			$where[1]["connector"] = "AND";
			
			$where[2]["clause"] = "box.name = box_i18n.box_name";
			$where[2]["connector"] = "AND";
			
			$where[3]["clause"] = "box.type = '%s'";
			$where[3]["connector"] = "AND";
			$where[3]["value"] = $type;
			
			$result = $db->autoQuerySelect('*','box,box_i18n,box_custom',$where);
			$objs = array();
			while($row = $db->fetchObject($result))
				$objs[] = xBoxCustomDAO::_boxcustomFromRow($row);
			return $objs;
		}
	}
};


?>