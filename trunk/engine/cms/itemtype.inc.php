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
	 * @var string
	 * @access public
	 */
	var $m_default_content_filter;
	
	/**
	 * @var bool
	 * @access public
	 */
	var $m_default_approved;
	
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
	 *
	 */
	function xItemType($id,$name,$description,$default_content_filter,$default_approved,$default_published,$default_sticky,
		$default_accept_replies)
	{
		$this->m_id = $id;
		$this->m_name = $name;
		$this->m_description = $description;
		$this->m_default_content_filter = $default_content_filter;
		$this->m_default_approved = $default_approved;
		$this->m_default_published = $default_published;
		$this->m_default_sticky = $default_sticky;
		$this->m_default_accept_replies = $default_accept_replies;
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
		xItemTypeDAO::delete($this->m_id);
	}
	
	
	/** 
	 * Delete an item type from db using its id
	 *
	 * @param int $typeid
	 * @static
	 */
	function dbDeleteById($typeid)
	{
		xItemTypeDAO::delete($typeid);
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
	function dbLoad($id)
	{
		return xItemTypeDAO::load($id);
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
			$options[$type->m_name] = $type->m_id;
		}
		return new xFormElementOptions($var_name,'Select item type','',$value,$options,FALSE,$mandatory,
			new xInputValidatorInteger());
	}
};


?>
