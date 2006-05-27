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
 * Represent the simplest item in xanthin+
 */
class xItemPage extends xItem
{
	/**
	 * @var string
	 * @access public
	 */
	var $m_subtype;
	
	/**
	 * @var bool
	 * @access public
	 */
	var $m_sticky;
	
	/**
	 * @var bool
	 * @access public
	 */
	var $m_accept_replies;
	
	/**
	 * @var bool
	 * @access public
	 */
	var $m_published;
	
	/**
	 * @var bool
	 * @access public
	 */
	var $m_approved;
	
	/**
	 * @var string
	 * @access public
	 */
	var $m_meta_description;
	
	/**
	 * @var bool
	 * @access public
	 */
	var $m_meta_keywords;
	
	
	/**
	 *
	 */
	function xItemPage($id,$title,$type,$author,$content,$content_filter,$creation_time,$lastedit_time,
		$subtype,$published,$sticky,$accept_replies,$approved,$meta_description,$meta_keywords)
	{
		$this->xItem($id,$title,$type,$author,$content,$content_filter,$creation_time,$lastedit_time);
		
		$this->m_subtype = $subtype;
		$this->m_sticky = $sticky;
		$this->m_accept_replies = $accept_replies;
		$this->m_published = $published;
		$this->m_approved = $approved;
		$this->m_meta_description = $meta_description;
		$this->m_meta_keywords = $meta_keywords;
	}
	
	
	// DOCS INHERITHED  ========================================================
	function onRender()
	{
		$error = '';
		$content = xContentFilterController::applyFilter($this->m_content_filter,$this->m_content,$error);
		$title = xContentFilterController::applyFilter('notags',$this->m_title,$error);
		return xTheme::render3('renderItemPage',$this->m_subtype,$title,$content);
	}
	
	/** 
	 * Inserts this into db
	 *
	 * @return bool FALSE on error
	 */
	function dbInsert()
	{
		$this->m_id = xItemPageDAO::insert($this);
		return $this->m_id;
	}
	
	/** 
	 * Delete this from db
	 *
	 * @return bool FALSE on error
	 */
	function dbDelete()
	{
		return xItemPageDAO::delete($this->m_id);
	}
	
	/**
	 * Update this in db
	 *
	 * @return bool FALSE on error
	 */
	function dbUpdate()
	{
		return xItemPageDAO::update($this);
	}
	
	/**
	 * Retrieve a specific item page from db
	 *
	 * @return xItemPage
	 * @static
	 */
	function dbLoad($id)
	{
		return xItemPageDAO::load($id);
	}
	
	/**
	 *
	 */
	function toSpecificItem($item)
	{
		return xItemPageDAO::toSpecificItem($item);
	}
	
	
	/**
	 * Retrieves all items.
	 *
	 * @param string $type Exact search
	 * @param string $title Like search
	 * @param string $author Exact search
	 * @param string $content Like search
	 * @param int $cathegory Exact search on category id
	 * @param int $nelementpage Number of elements per page
	 * @param int $npage Number of page (starting from 1).
	 * @return array(xItem)
	 * @static
	 */
	function find($subtype = NULL,$title = NULL,$author = NULL,$content = NULL,$cathegory = NULL,$nelementpage = 0,$npage = 0)
	{
		return xItemPageDAO::find($subtype,$title,$author,$content,$cathegory,$nelementpage,$npage);
	}
	
	
	/**
	 * Return a form element for asking for published input
	 *
	 * @param string $var_name The name of the form element
	 * @param string $label
	 * @param string $description
	 * @param bool $checked
	 * @return xFormElement
	 * @static
	 */
	function getFormPublishedCheck($var_name,$label,$description,$checked)
	{
		return new xFormElementCheckbox($var_name,$label,$description,1,$checked,FALSE,new xInputValidatorInteger());
	}
	
	/**
	 * Return a form element for asking for approved check
	 *
	 * @param string $var_name The name of the form element
	 * @param string $label
	 * @param string $description
	 * @param bool $checked
	 * @return xFormElement
	 * @static
	 */
	function getFormApprovedCheck($var_name,$label,$description,$checked)
	{
		return new xFormElementCheckbox($var_name,$label,$description,1,$checked,FALSE,new xInputValidatorInteger());
	}
	
	
	/**
	 * Return a form element for asking for accept replies check
	 *
	 * @param string $var_name The name of the form element
	 * @param string $label
	 * @param string $description
	 * @param bool $checked
	 * @return xFormElement
	 * @static
	 */
	function getFormAcceptRepliesCheck($var_name,$label,$description,$checked)
	{
		return new xFormElementCheckbox($var_name,$label,$description,1,$checked,FALSE,new xInputValidatorInteger());
	}
	
	
	/**
	 * Return a form element for asking for sticky input
	 *
	 * @param string $var_name The name of the form element
	 * @param string $label
	 * @param string $description
	 * @param bool $checked
	 * @return xFormElement
	 * @static
	 */
	function getFormStickyCheck($var_name,$label,$description,$checked)
	{
		return new xFormElementCheckbox($var_name,$label,$description,1,$checked,FALSE,new xInputValidatorInteger());
	}
	
	/**
	 * Return a form element for asking for description input
	 *
	 * @param string $var_name The name of the form element
	 * @param string $value
	 * @param string $label
	 * @param string $description
	 * @param bool $mandatory True if this input is manadtory
	 * @return xFormElement
	 * @static
	 */
	function getFormDescriptionInput($var_name,$label,$description,$value,$mandatory)
	{
		return new xFormElementTextField($var_name,$label,$description,$value,$mandatory,new xInputValidatorText(512));
	}
	
	/**
	 * Return a form element for asking for keywords input
	 *
	 * @param string $var_name The name of the form element
	 * @param string $value
	 * @param string $label
	 * @param string $description
	 * @param bool $mandatory True if this input is manadtory
	 * @return xFormElement
	 * @static
	 */
	function getFormKeywordsInput($var_name,$label,$description,$value,$mandatory)
	{
		return new xFormElementTextField($var_name,$label,$description,$value,$mandatory,new xInputValidatorText(128));
	}
};


?>
