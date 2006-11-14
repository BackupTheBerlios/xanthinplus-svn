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
class xModuleDAO extends xObject
{
	function __construct()
	{
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
	function _dtoFromRow($row_object)
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
		
		$result = $db->autoQuerySelect(array('*'),array('active_modules'),$where);
		$objs = array();
		while($row = $db->fetchObject($result))
			$objs[] = xModuleDAO::_dtoFromRow($row);
		return $objs;
	}
}

?>