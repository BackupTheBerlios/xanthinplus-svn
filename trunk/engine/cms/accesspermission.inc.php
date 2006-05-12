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
 * Defines an association between a permission name and a access filter set.
 */
class xAccessPermission
{
	/**
	 * @var string
	 * @access public
	 */
	var $m_name;
	
	/**
	 * @var int
	 * @access public
	 */
	var $m_filterset;
	
	/**
	 * Contructor
	 *
	 * @param string $name
	 * @param int $fitlerset
	 */
	function xAccessPermission($name,$filterset)
	{
		$this->m_name = $name;
		$this->m_filterset = $filterset;
	}
	
	
	/**
	 *
	 */
	function dbInsert()
	{
		xAccessPermissionDAO::insert($this);
	}
	
	
	/**
	 *
	 * @param string $name
	 * @return bool
	 * @static
	 */
	function checkPermission($name)
	{
		$permission = xAccessPermissionDAO::load($name);
		if($permission === NULL)
		{
			return FALSE;
		}
		
		$set = xAccessFilterSet::dbLoad($permission->m_filterset);
		if($set === NULL)
		{
			return FALSE;
		}
		
		return $set->checkAccess();
	}
}

?>