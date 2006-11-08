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
class xModuleDAO
{
	function xModuleDAO()
	{
		assert(FALSE);
	}
	
	/**
	 * Updates the status of the given module.
	 * @return bool FALSE on error
	 * @static 
	 */
	function update($dto)
	{
		$db =& xDB::getDB();
		$db->startTransaction();
		
		$res = reset(xModuleDAO::find($dto->m_path,NULL,NULL));
		
		
		if($res === FALSE)
		{
			if($dto->m_enabled || $dto->m_installed)
				$db->query("INSERT INTO active_modules(path,enabled,installed) VALUES('%s',%d,%d)",
					$dto->m_path,$dto->m_enabled,$dto->m_installed);
		}
		else
		{
			if($dto->m_enabled || $dto->m_installed)
				$db->query("UPDATE active_modules SET enabled = %d,installed = %d WHERE path = '%s'",
					$dto->m_enabled,$dto->m_installed,$dto->m_path);
			else
				$db->query("DELETE FROM active_modules WHERE path = '%s'",$dto->m_path);
		}
		
		if(!$db->commitTransaction())
			return false;
		
		return true;
	}
	
	/**
	 *
	 * @return xModuleDTO
	 * @static
	 * @access private
	 */
	function _moduleFromRow($row_object)
	{
		return new xModuleDTO($row_object->path,$row_object->enabled,$row_object->installed);
	}
	
	/**
	 *
	 */
	function find($path,$enabled,$installed)
	{
		$db =& xDB::getDB();
	
		$where[0]["clause"] = "path = '%s'";
		$where[0]["connector"] = "AND";
		$where[0]["value"] = $path;
	 
		$where[1]["clause"] = "enabled = %d";
		$where[1]["connector"] = "AND";
		$where[1]["value"] = $enabled;
		
		$where[2]["clause"] = "installed = %d";
		$where[2]["connector"] = "AND";
		$where[2]["value"] = $installed;
		
		$result = $db->autoQuerySelect('*','active_modules',$where);
		$objs = array();
		while($row = $db->fetchObject($result))
			$objs[] = xModuleDAO::_moduleFromRow($row);
		return $objs;
	}
}

//###########################################################################
//###########################################################################
//###########################################################################

/**
 * 
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
		$db =& xDB::getDB();
		return $db->query("INSERT INTO language(name,full_name) 
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
		$db =& xDB::getDB();
		return $db->query("DELETE FROM language WHERE name = '%s'",$name);
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
		$db =& xDB::getDB();
		$result = $db->query("SELECT * FROM language WHERE name = '%s'",$name);
		if($row = $db->fetchObject($result))
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
		$db =& xDB::getDB();
		$languages = array();
		$result = $db->query("SELECT name FROM language");
		while($row = $db->fetchObject($result))
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
		$result = $db->query("SELECT * FROM language");
		while($row = $db->fetchObject($result))
		{
			$languages[] = xLanguageDAO::_languageFromRow($row);
		}
		
		return $languages;
	}
}
?>