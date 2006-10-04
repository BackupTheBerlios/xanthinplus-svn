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

$g_xanth_cathegory_managers = array();


/**
 * An items cathegory.
 */
class xCathegory extends xElement
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
	var $m_type;
	
	/**
	 * @var string
	 * @access public
	 */
	var $m_name;
	
	/**
	 * @var string
	 * @access public
	 */
	var $m_title;
	
	/**
	 * @var string
	 * @access public
	 */
	var $m_description;
	
	/**
	 * @var string
	 * @access public
	 */
	var $m_parent_cathegory;
	
	/**
	 *
	 */
	function xCathegory($id,$name,$title,$type,$description,$parent_cathegory)
	{
		$this->xElement();
		
		$this->m_id = $id;
		$this->m_type = $type;
		$this->m_name = $name;
		$this->m_title = $title;
		$this->m_description = $description;
		$this->m_parent_cathegory = $parent_cathegory;
	}
	
	
	/** 
	 * Inserts this into db
	 *
	 * @return bool FALSE on error
	 */
	function dbInsert()
	{
		return xCathegoryDAO::insert($this);
	}
	
	/** 
	 * Delete this cathegory from db
	 *
	 * @return bool FALSE on error
	 */
	function dbDelete()
	{
		return xCathegoryDAO::delete($this->m_id);
	}
	
	
	/** 
	 * Delete a cathegory from db using its id
	 *
	 * @param int $catid
	 * @return bool FALSE on error
	 * @static
	 */
	function dbDeleteById($catid)
	{
		return xCathegoryDAO::delete($catid);
	}
	
	/**
	 * Update this in db
	 *
	 * @return bool FALSE on error
	 */
	function dbUpdate()
	{
		return xCathegoryDAO::update($this);
	}
	
	/**
	 * Retrieve a specific cathegory from db
	 *
	 * @return xCathegory
	 * @static
	 */
	function dbLoad($id)
	{
		return xCathegoryDAO::load($id);
	}
	
	/**
	 * Retrieves all cathegories.
	 *
	 * @return array(xCathegory)
	 * @static
	 */
	function findAll()
	{
		return xCathegoryDAO::findAll();
	}
};


?>
