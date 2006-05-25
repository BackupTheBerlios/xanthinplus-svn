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
	 * @return bool FALSE on error
	 */
	function dbInsert()
	{
		$this->m_id = xCathegoryTypeDAO::insert($this);
		
		return $this->m_id;
	}
	
	/** 
	 * Delete this from db
	 *
	 * @return bool FALSE on error
	 */
	function dbDelete()
	{
		return xCathegoryTypeDAO::delete($this->m_name);
	}
	
	
	/** 
	 * Delete an item type from db using its name
	 *
	 * @param int $typename
	 * @return bool FALSE on error
	 * @static
	 */
	function dbDeleteByName($typename)
	{
		return xCathegoryTypeDAO::delete($typename);
	}
	
	/**
	 * Update this in db
	 *
	 * @return bool FALSE on error
	 */
	function dbUpdate()
	{
		return xCathegoryTypeDAO::update($this);
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
	
	
	/**
	 * Return a form element for asking for cathegory type input
	 *
	 * @param string $var_name The name of the form element
	 * @param string $value
	 * @param bool $mandatory True if this input is manadtory
	 * @return xFormElement
	 * @static
	 */
	function getFormCathegoryTypeChooser($var_name,$label,$description,$value,$mandatory)
	{
		$types = xCathegoryType::findAll();
		
		$options = array();
		if(!$mandatory)
		{
			$options[''] = 0;
		}
		foreach($types as $type)
		{
			$options[$type->m_name] = $type->m_name;
		}
		
		return new xFormElementOptions($var_name,$label,$description,$value,$options,FALSE,$mandatory,
			new xInputValidatorTextNameId(32));
	}
};


?>
