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
class xItemType
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
	function xItemType($name,$description)
	{
		$this->m_name = $name;
		$this->m_description = $description;
	}
	
	
	/** 
	 * Inserts this into db
	 */
	function dbInsert()
	{
		$this->m_id = xItemTypeDAO::insert($this);
	}
	
	/** 
	 * Delete this from db
	 */
	function dbDelete()
	{
		xItemTypeDAO::delete($this->m_name);
	}
	
	
	/** 
	 * Delete an item type from db using its name
	 *
	 * @param int $typename
	 * @static
	 */
	function dbDeleteById($typename)
	{
		xItemTypeDAO::delete($typename);
	}
	
	/**
	 * Update this in db
	 */
	function dbUpdate()
	{
		xItemTypeDAO::update($this);
	}
	
	/**
	 * Retrieve a specific item type from db
	 *
	 * @return xItemType
	 * @static
	 */
	function dbLoad($typename)
	{
		return xItemTypeDAO::load($typename);
	}
	
	/**
	 * Retrieves all itme types.
	 *
	 * @return array(xItemType)
	 * @static
	 */
	function findAll()
	{
		return xItemTypeDAO::findAll();
	}
	
	/**
	 * Return a form element representing all item types presents in db
	 *
	 * @param string $var_name The name of the form element
	 * @param string $value
	 * @param bool $mandatory True if this input is manadtory
	 * @return xFormElement
	 * @static
	 */
	function getFormTypeChooser($var_name,$value,$mandatory)
	{
		$types = xItemType::findAll();
		$options = array();
		foreach($types as $type)
		{
			$options[$type->m_name] = $type->m_name;
		}
		return new xFormElementOptions($var_name,'Select item type','',$value,$options,FALSE,$mandatory,
			new xInputValidatorInteger());
	}
};


?>
