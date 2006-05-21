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
class xAccessFilterSetDAO
{
	function xAccessFilterSetDAO()
	{
		//non instaltiable
		assert(FALSE);
	}
	
	/**
	 *
	 * @param xAccessFilterSet $access_filter_set
	 * @return int The new filter id or FALSE on error
	 * @static
	 */
	function insert($access_filter_set,$transaction = true)
	{
		if($transaction)
			xDB::getDB()->startTransaction();
		
		$id = xUniqueId::generate('access_filter_set');
		if(! xDB::getDB()->query("INSERT INTO access_filter_set(id,name,description) VALUES (%d,'%s','%s')",
			$id,$access_filter_set->m_name,$access_filter_set->m_description))
			return FALSE;
		
		if(! xAccessFilterSetDAO::_insertFilters($id,$access_filter_set->m_filters))
			return FALSE;
		
		if($transaction)
			xDB::getDB()->commit();
		
		return $id;
	}
	
	/**
	 * @access private
	 * @static
	 */
	function _insertFilters($setid,$filters)
	{
		foreach($filters as $filter)
		{
			$ret = TRUE;
			
			if(xanth_instanceof($filter,'xAccessFilterPathInclude'))
			{
				$ret = xDB::getDB()->query("INSERT INTO access_filter_path_include(filterid,incpath) VALUES (%d,'%s')",
					$setid,$filter->m_path);
			}
			elseif(xanth_instanceof($filter,'xAccessFilterPathExclude'))
			{
				$ret = xDB::getDB()->query("INSERT INTO access_filter_path_exclude(filterid,excpath) VALUES (%d,'%s')",
					$setid,$filter->m_path);
			}
			elseif(xanth_instanceof($filter,'xAccessFilterRole'))
			{
				$ret = xDB::getDB()->query("INSERT INTO access_filter_role(filterid,roleName) VALUES (%d,'%s')",
					$setid,$filter->m_role_name);
			}
			else
			{
				//should never arrive here
				assert(FALSE);
			}
			
			if($ret === FALSE)
			{
				return FALSE;
			}
		}
		
		return TRUE;
	}
	
	/**
	 *
	 * @param xAccessFilterSet $access_filter_set
	 * @return bool FALSE on error
	 * @static
	 */
	function update($access_filter_set,$transaction)
	{
		if($transaction)
			xDB::getDB()->startTransaction();
		
		//update name and description
		if(! xDB::getDB()->query("UPDATE access_filter_set SET name = '%s',description = '%s' WHERE id = %d",
			$access_filter_set->m_name,$access_filter_set->m_description,$access_filter_set->m_id))
			return FALSE;
			
		//clear all previous associated filters
		if(! xDB::getDB()->query("DELETE FROM access_filter_path_include WHERE filterid = %d",$access_filter_set->m_id))
			return FALSE;
		
		if(! xDB::getDB()->query("DELETE FROM access_filter_path_exclude WHERE filterid = %d",$access_filter_set->m_id))
			return FALSE;
		
		if(! xDB::getDB()->query("DELETE FROM access_filter_path_role WHERE filterid = %d",$access_filter_set->m_id))
			return FALSE;

		//reinsert filters
		if(! xAccessFilterSetDAO::_insertFilters($access_filter_set->m_id,$access_filter_set->m_filters))
			return FALSE;
		
		if($transaction)
			xDB::getDB()->commit();
		
		return TRUE;
	}
	
	/**
	 *
	 * @param int $filterid
	 * @return bool FALSE on error
	 * @static
	 */
	function delete($filterid)
	{
		return xDB::getDB()->query("DELETE FROM access_filter_set WHERE filterid = %d",$filterid);
	}
	
	/**
	 *
	 * @param int $filterid
	 * @return xAccessFilterSet Return the loaded object or NULL if not found
	 * @static
	 */
	function load($filterid)
	{
		$set = NULL;
		$result = xDB::getDB()->query("SELECT name,description FROM access_filter_set WHERE id = %d",$filterid);
		if($row = xDB::getDB()->fetchObject($result))
		{
			$set = new xAccessFilterSet($filterid,$row->name,$row->description);
			$set->m_filters = xAccessFilterSetDAO::_getFilters($filterid);
		}
		
		return $set;
	}
	
	/**
	 *
	 * @return array(xAccessFilter)
	 * @access private
	 * @static
	 */
	function _getFilters($filterid)
	{
		$filters = array();
		$result = xDB::getDB()->query("SELECT incpath FROM access_filter_path_include WHERE filterid = %d",$filterid);
		while($row = xDB::getDB()->fetchObject($result))
		{
			$filters[] = new xAccessFilterPathInclude($row->incpath);
		}
		$result = xDB::getDB()->query("SELECT excpath FROM access_filter_path_exclude WHERE filterid = %d",$filterid);
		while($row = xDB::getDB()->fetchObject($result))
		{
			$filters[] = new xAccessFilterPathExclude($row->excpath);
		}
		$result = xDB::getDB()->query("SELECT roleName FROM access_filter_role WHERE filterid = %d",$filterid);
		while($row = xDB::getDB()->fetchObject($result))
		{
			$filters[] = new xAccessFilterRole($row->roleName);
		}
		
		return $filters;
	}
	
	
	/**
	 *
	 * @return array(xAccessFilterSet)
	 * @static
	 */
	function findAll()
	{
		$sets = array();
		$result = xDB::getDB()->query("SELECT * FROM access_filter_set");
		while($row = xDB::getDB()->fetchObject($result))
		{
			$set = new xAccessFilterSet($row->id,$row->name,$row->description);
			$set->m_filters = xAccessFilterSetDAO::_getFilters($row->id);
			$sets[] = $set;
		}
		
		return $sets;
	}
}

?>