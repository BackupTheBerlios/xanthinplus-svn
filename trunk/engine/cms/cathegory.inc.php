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
	var $m_description;
	
	/**
	 * @var string
	 * @access public
	 */
	var $m_parent_cathegory;
	
	/**
	 * @var string
	 * @access public
	 */
	var $m_items_type;
	
	/**
	 *
	 */
	function xCathegory($id,$name,$type,$description,$parent_cathegory,$items_type)
	{
		$this->xElement();
		
		$this->m_id = $id;
		$this->m_type = $type;
		$this->m_name = $name;
		$this->m_description = $description;
		$this->m_parent_cathegory = $parent_cathegory;
		$this->m_items_type = $items_type;
	}
	
	
	// DOCS INHERITHED  ========================================================
	function onRender()
	{
		
	}
	
	
	/** 
	 * Inserts this into db
	 */
	function dbInsert()
	{
		xCathegoryDAO::insert($this);
	}
	
	/** 
	 * Delete this cathegory from db
	 */
	function dbDelete()
	{
		xCathegoryDAO::delete($this->m_id);
	}
	
	
	/** 
	 * Delete a cathegory from db using its id
	 *
	 * @param int $catid
	 * @static
	 */
	function dbDeleteById($catid)
	{
		xCathegoryDAO::delete($catid);
	}
	
	/**
	 * Update this in db
	 */
	function dbUpdate()
	{
		xCathegoryDAO::update($this);
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
	
	
	/**
	 * Return a form element for asking for name input
	 *
	 * @param string $var_name The name of the form element
	 * @param string $value
	 * @param bool $mandatory True if this input is manadtory
	 * @return xFormElement
	 * @static
	 */
	function getFormCathegoryChooser($var_name,$value,$mandatory)
	{
		$cathegories = xCathegory::findAll();
		
		$options = array();
		if(!$mandatory)
		{
			$options[''] = 0;
		}
		foreach($cathegories as $cathegory)
		{
			$options[$cathegory->m_name] = $cathegory->m_id;
		}
		
		return new xFormElementOptions($var_name,'Select cathegory','',$value,$options,FALSE,$mandatory,
			new xInputValidatorInteger());
	}
	
	
	/**
	 * Return a form element for asking for name input
	 *
	 * @param string $var_name The name of the form element
	 * @param string $value
	 * @param bool $mandatory True if this input is manadtory
	 * @return xFormElement
	 * @static
	 */
	function getFormNameInput($var_name,$value,$mandatory)
	{
		return new xFormElementTextField($var_name,'Name','',$value,$mandatory,new xInputValidatorText(32));
	}
	
	/**
	 * Return a form element for asking for Description input
	 *
	 * @param string $var_name The name of the form element
	 * @param string $value
	 * @param bool $mandatory True if this input is manadtory
	 * @return xFormElement
	 * @static
	 */
	function getFormDescriptionInput($var_name,$value,$mandatory)
	{
		return new xFormElementTextField($var_name,'Description','',$value,$mandatory,new xInputValidatorText(32));
	}
};


?>
