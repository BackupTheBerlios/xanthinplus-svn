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
 * An item type.
 */
class xCathegoryType
{
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
	 *
	 */
	function xCathegoryType($name,$description)
	{
		$this->m_name = $name;
		$this->m_description = $description;
	}
	
	
	/** 
	 * Inserts this into db
	 */
	function dbInsert()
	{
		$this->m_id = xCathegoryTypeDAO::insert($this);
	}
	
	/** 
	 * Delete this from db
	 */
	function dbDelete()
	{
		xCathegoryTypeDAO::delete($this->m_name);
	}
	
	
	/** 
	 * Delete an item type from db using its name
	 *
	 * @param int $typename
	 * @static
	 */
	function dbDeleteByName($typename)
	{
		xCathegoryTypeDAO::delete($typename);
	}
	
	/**
	 * Update this in db
	 */
	function dbUpdate()
	{
		xCathegoryTypeDAO::update($this);
	}
	
	/**
	 * Retrieve a specific item type from db
	 *
	 * @return xItemType
	 * @static
	 */
	function dbLoad($typename)
	{
		return xCathegoryTypeDAO::load($typename);
	}
	
	/**
	 * Retrieves all itme types.
	 *
	 * @return array(xCathegoryType)
	 * @static
	 */
	function findAll()
	{
		return xCathegoryTypeDAO::findAll();
	}
};


?>
