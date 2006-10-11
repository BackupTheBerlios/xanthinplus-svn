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
class xBoxType
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
	 * @var bool
	 * @access public
	 */
	var $m_user_editable;
	
	
	/**
	* Contructor
	*/
	function xBoxType($name,$description,$user_editable)
	{
		$this->m_description = $description;
		$this->m_name = $name;
		$this->m_user_editable = $user_editable;
	}
	
	/**
	 * Delete this object from db
	 *
	 * @return bool FALSE on error
	 */
	function dbDelete()
	{
		return  xBoxTypeDAO::delete($this);
	}
	
	/**
	 * Insert this object into db
	 *
	 * @return bool FALSE on error
	 */
	function dbInsert()
	{
		return xBoxTypeDAO::insert($this);
	}
	
	/**
	 * Retrieve all box types from db
	 *
	 * @return array(xBox)
	 */
	function findAll()
	{
		return xBoxTypeDAO::findAll();
	}
};

?>
