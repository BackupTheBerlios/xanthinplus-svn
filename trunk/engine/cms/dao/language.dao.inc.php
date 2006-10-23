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


class xLanguageDAO
{
	function xLanguageDAO()
	{
		//non instaltiable
		assert(FALSE);
	}
	
	/**
	 * Insert a new node type
	 *
	 * @param xLanguage $node_type
	 * @return bool FALSE on error
	 * @static
	 */
	function insert($language)
	{
		return xDB::getDB()->query("INSERT INTO language(name,full_name) 
			VALUES ('%s','%s')",$language->m_name,$language->m_full_name);
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
		return xDB::getDB()->query("DELETE FROM language WHERE name = '%s'",$name);
	}
	
	/**
	 *
	 * @return xLanguage
	 * @static
	 * @access private
	 */
	function _languageFromRow($row_object)
	{
		return new xLanguage($row_object->name,$row_object->full_name);
	}
	
	
	/**
	 * Load an Item type from db.
	 *
	 * @return xLanguage
	 * @static
	 */
	function load($name)
	{
		$result = xDB::getDB()->query("SELECT * FROM language WHERE name = '%s'",$name);
		if($row = xDB::getDB()->fetchObject($result))
		{
			return xLanguageDAO::_languageFromRow($row);
		}
		
		return NULL;
	}
	
	/**
	 * Retrieves all language names.
	 *
	 * @return array(string)
	 * @static
	 */
	function findNames()
	{
		$languages = array();
		$result = xDB::getDB()->query("SELECT name FROM language");
		while($row = xDB::getDB()->fetchObject($result))
		{
			$languages[] = $row->name;
		}
		
		return $languages;
	}
	
	
	/**
	 * Retrieves all item type.
	 *
	 * @return array(xItemType)
	 * @static
	 */
	function find()
	{
		$languages = array();
		$result = xDB::getDB()->query("SELECT * FROM language");
		while($row = xDB::getDB()->fetchObject($result))
		{
			$languages[] = xLanguageDAO::_languageFromRow($row);
		}
		
		return $languages;
	}
}

?>