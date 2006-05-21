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
 * An item page subtype.
 */
class xItemPageType
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
	 * @var array(string)
	 * @access public
	 */
	var $m_allowed_content_filters;
	
	/**
	 * @var bool
	 * @access public
	 */
	var $m_default_published;
	
	/**
	 * @var bool
	 * @access public
	 */
	var $m_default_sticky;
	
	/**
	 * @var bool
	 * @access public
	 */
	var $m_default_accept_replies;
	
	/**
	 * @var bool
	 * @access public
	 */
	var $m_default_approved;

	/**
	 *
	 */
	function xItemPageType($name,$description,$allowed_content_filters,$default_published,$default_sticky,
		$default_accept_replies,$default_approved)
	{
		$this->m_name = $name;
		$this->m_description = $description;
		$this->m_allowed_content_filters = $allowed_content_filters;
		$this->m_default_published = $default_published;
		$this->m_default_sticky = $default_sticky;
		$this->default_accept_replies = $default_accept_replies;
		$this->m_default_approved = $default_approved;
	}
	
	
	/** 
	 * Inserts this into db
	 *
	 * @return bool FALSE on error
	 */
	function dbInsert()
	{
		$this->m_id = xItemPageTypeDAO::insert($this);
		
		return $this->m_id;
	}
	
	/** 
	 * Delete this from db
	 *
	 * @return bool FALSE on error
	 */
	function dbDelete()
	{
		return xItemPageTypeDAO::delete($this->m_name);
	}
	
	
	/** 
	 * Delete an item type from db using its name
	 *
	 * @param int $typename
	 * @return bool FALSE on error
	 * @static
	 */
	function dbDeleteById($typename)
	{
		return xItemPageTypeDAO::delete($typename);
	}
	
	/**
	 * Update this in db
	 *
	 * @return bool FALSE on error
	 */
	function dbUpdate()
	{
		return xItemPageTypeDAO::update($this);
	}
	
	/**
	 * Retrieve a specific item type from db
	 *
	 * @return xItemPageType
	 * @static
	 */
	function dbLoad($typename)
	{
		return xItemPageTypeDAO::load($typename);
	}
	
	/**
	 * Retrieves all itme types.
	 *
	 * @return array(xItemPageType)
	 * @static
	 */
	function findAll()
	{
		return xItemPageTypeDAO::findAll();
	}
	
	
	/**
	 * Return a form element representing all item page subtypes presents in db
	 *
	 * @param string $var_name The name of the form element
	 * @param string $description
	 * @param string $label
	 * @param string $value
	 * @param bool $mandatory True if this input is manadtory
	 * @return xFormElement
	 * @static
	 */
	function getFormItemPageTypeChooser($var_name,$label,$description,$value,$mandatory)
	{
		$types = xItemPageType::findAll();
		$options = array();
		foreach($types as $type)
		{
			$options[$type->m_name] = $type->m_name;
		}
		return new xFormElementOptions($var_name,$label,$description,$value,$options,FALSE,$mandatory,
			new xInputValidatorTextNameId(32));
	}
};


?>
