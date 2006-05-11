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
 * Manage the access to a resource using xAccessFilter's.
 */
class xAccessFilterSet
{
	/**
	 * @var int
	 * @access public
	 */
	var $m_id;
	
	/**
	 * @var string
	 * @access public
	 */
	var $m_name;
	
	/**
	 * @var string
	 * @access public
	 */
	var $m_description;
	
	/**
	 * @access public
	 * @var array(xAccessFilter)
	 */
	var $m_filters;
	
	/**
	 * Contructor
	 *
	 * @param int $id
	 * @param string $name
	 * @param string $description
	 * @param array(xAccessFilter) $filters
	 */
	function xAccessFilterSet($id,$name,$description,$filters = array())
	{
		$this->m_id = $id;
		$this->m_description = $description;
		$this->m_name = $name;
		$this->m_filters = $filters;
	}
	
	/**
	 * Check the current acces permission to all the finters in the set
	 *
	 * @return bool
	 */
	function checkAccess()
	{
		foreach($this->m_filters as $filter)
		{
			if(!$filter->checkAccess())
			{
				return FALSE;
			}
		}
		
		return TRUE;
	}
	
	/**
	 * Insert this object into db
	 */
	function dbInsert()
	{
		$this->m_id = xAccessFilterSetDAO::insert($this);
	}
}



/**
 * To filter the access to a resource.
 */
class xAccessFilter
{
	/**
	 *
	 */
	function xAccessFilter()
	{
	}
	
	/**
	 * Check che current access right.
	 *
	 * @return bool
	 * @abstract
	 */
	function checkAccess()
	{
		//virtual
		assert(FALSE);
	}
}


/**
 * Filter based on current xanthpath (inclusive).
 */
class xAccessFilterPathInclude extends xAccessFilter
{
	/**
	 * @var string
	 * @access public
	 */
	var $m_path;
	
	/**
	 *
	 */
	function xAccessFilterPathInclude($path)
	{
		$this->m_path = $path;
	}
}

/**
 * Filter based on current xanthpath (exclusive).
 */
class xAccessFilterPathExclude extends xAccessFilter
{
	/**
	 * @var string
	 * @access public
	 */
	var $m_path;
	
	/**
	 *
	 */
	function xAccessFilterPathExclude($path)
	{
		$this->m_path = $path;
	}
}


/**
 * Filter based on current user access level.
 */
class xAccessFilterRole extends xAccessFilter
{
	/**
	 * @var string
	 * @access public
	 */
	var $m_role_name;
	
	/**
	 *
	 */
	function xAccessFilterRole($m_role_name)
	{
		$this->m_role_name = $m_role_name;
	}
	
}

?>