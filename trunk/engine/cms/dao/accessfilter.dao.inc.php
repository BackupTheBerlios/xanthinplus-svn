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
	 * @return int The new filter id
	 * @static
	 * @TODO correct problem with autoincrement and transaction
	 */
	function insert($access_filter_set)
	{
		//cannot instantiate a transaction cause the autogenerated id is created only on commit!!!
		//xDB::getDB()->startTransaction();
		
		xDB::getDB()->query("INSERT INTO access_filter_set(name,description) VALUES ('%s','%s')",
			$access_filter_set->m_name,$access_filter_set->m_description);
		$id = xDB::getDB()->getLastId();
		
		xAccessFilterSetDAO::_insertFilters($id,$access_filter_set->m_filters);
		
		//xDB::getDB()->commit();
	}
	
	/**
	 * @access private
	 * @static
	 */
	function _insertFilters($setid,$filters)
	{
		foreach($filters as $filter)
		{
			if(xanth_instanceof($filter,'xAccessFilterPathInclude'))
			{
				xDB::getDB()->query("INSERT INTO access_filter_path_include(filterid,incpath) VALUES (%d,'%s')",
					$setid,$filter->m_path);
			}
			elseif(xanth_instanceof($filter,'xAccessFilterPathExclude'))
			{
				xDB::getDB()->query("INSERT INTO access_filter_path_exclude(filterid,excpath) VALUES (%d,'%s')",
					$setid,$filter->m_path);
			}
			elseif(xanth_instanceof($filter,'xAccessFilterRole'))
			{
				xDB::getDB()->query("INSERT INTO access_filter_role(filterid,roleName) VALUES (%d,'%s')",
					$setid,$filter->m_role_name);
			}
			else
			{
				print_r($filter);
				//should never arrive here
				assert(FALSE);
			}
		}
	}
	
	/**
	 *
	 * @param xAccessFilterSet $access_filter_set
	 * @return int The new filter id
	 * @static
	 */
	function update($access_filter_set)
	{
		xDB::getDB()->startTransaction();
		
		//update name and description
		xDB::getDB()->query("UPDATE access_filter_set SET name = '%s',description = '%s' WHERE id = %d",
			$access_filter_set->m_name,$access_filter_set->m_description,$access_filter_set->m_id);
			
		//clear all previous associated filters
		xDB::getDB()->query("DELETE FROM access_filter_path_include WHERE filterid = %d",$access_filter_set->m_id);
		xDB::getDB()->query("DELETE FROM access_filter_path_exclude WHERE filterid = %d",$access_filter_set->m_id);
		xDB::getDB()->query("DELETE FROM access_filter_path_role WHERE filterid = %d",$access_filter_set->m_id);		
		
		//reisert filters
		xAccessFilterSetDAO::_insertFilters($access_filter_set->m_id,$access_filter_set->m_filters);
		
		xDB::getDB()->commit();
	}
	
	/**
	 *
	 * @param int $filterid
	 * @static
	 */
	function delete($filterid)
	{
		xDB::getDB()->query("DELETE FROM access_filter_set WHERE filterid = %d",$filterid);
	}
	
	/**
	 *
	 * @param int $filterid
	 * @return xAccessFilterSet Return the loaded object or NULL if not found
	 * @static
	 */
	function load($filterid)
	{
		$result = xDB::getDB()->query("SELECT name,description FROM access_filter_set WHERE id = %d",$filterid);
		if($row = xDB::getDB()->fetchObject($result))
		{
			$set = new xAccessFilterSet($filterid,$row->name,$row->description);
			
			$filters = array();
			$result = xDB::getDB()->query("SELECT incpath FROM access_filter_path_include WHERE filterid = %d",$filterid);
			while($row = xDB::getDB()->fetchObject($result))
			{
				$filters[] = new xAccessFilterPathInclude($row->incpath);
			}
			$result = xDB::getDB()->query("SELECT excpath FROM access_filter_path_exclude WHERE filterid = %d",$filterid);
			while($row = xDB::getDB()->fetchObject($result))
			{
				$filters[] = new xAccessFilterPathInclude($row->excpath);
			}
			$result = xDB::getDB()->query("SELECT roleName FROM access_filter_role WHERE filterid = %d",$filterid);
			while($row = xDB::getDB()->fetchObject($result))
			{
				$filters[] = new xAccessFilterPathInclude($row->roleName);
			}
			
			$set->m_filters = $filters;
		}
		print_r($filters);
		//assert(FALSE);//TODO
	}
}

?>