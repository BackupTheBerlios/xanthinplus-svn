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
 * Represent an item in the CMS. An item can be an article, a blog entry, a forum post.
 */
class xItem extends xElement
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
	var $m_title;
	
	/**
	 * @var string
	 * @access public
	 */
	var $m_type;
	
	/**
	 * @var string
	 * @access public
	 */
	var $m_author;
	
	/**
	 * @var string
	 * @access public
	 */
	var $m_content;
	
	/**
	 * @var string
	 * @access public
	 */
	var $m_content_filter;
	
	/**
	 * @var timestamp
	 * @access public
	 */
	var $m_creation_time;
			
	/**
	 * @var timestamp Can be NULL
	 * @access public
	 */
	var $m_lastedit_time;
	
	
	/**
	 *
	 */
	function xItem($id,$title,$type,$author,$content,$content_filter,$creation_time = NULL,$lastedit_time = NULL)
	{
		$this->xElement();
		
		$this->m_id = $id;
		$this->m_title = $title;
		$this->m_type_id = $type_id;
		$this->m_author = $author;
		$this->m_content = $content;
		$this->m_content_filter = $content_filter;
		$this->m_creation_time = $creation_time;
		$this->m_lastedit_time = $lastedit_time;
	}
	
	
	/** 
	 * Delete an item from db using its id
	 *
	 * @param int $catid
	 * @static
	 */
	function dbDeleteById($id)
	{
		xItemDAO::delete($id);
	}
	
	/**
	 * Convert a simple xItem into a specific xItem child object that correspond to the item type 
	 * and ready to be rendered. Works also with array of items.
	 *
	 * @return xItem (or array(xItem)) A specific xItem child object corresponding to the specified type or NULL if not found.
	 * @todo add the ask to modules
	 * @static
	 */
	function toSpecificItem($item)
	{
		$newitem = NULL;
		
		if(! is_array($item))
		{
			//check for built-in box type
			if($item->m_type == "page")
			{
				$newitem = xItemPage::toSpecificItem($item);
			}
			else
			{
				//todo
			}
		}
		else
		{
			$newitem = array();
			foreach($item as $a_item)
			{
				$tmp = xItem::toSpecificItem($a_item);
				
				if(empty($tmp))
				{
					xLog::log(LOG_LEVEL_WARNING,'Cannot convert a generic item to a specific one (type: '.$a_item->m_type .')' );
				}
				else
				{
					$newitem[] = $tmp;
				}
			}
		}
		
		return $newitem;
	}
	
	
	/**
	 * Retrieve a specific item from db.(already converted in specific item)
	 *
	 * @return xItem
	 * @static
	 */
	function dbLoad($id)
	{
		$item = xItemDAO::load($id);
		
		if($item === NULL)
		{
			return NULL;
		}
		
		return xItem::toSpecificItem($item);
	}
	
	/**
	 * Retrieves all replies associated with an item.(already converted in specific items)
	 *
	 * @param int $parentid
	 * @param int $nelementpage Number of elements per page
	 * @param int $npage Number of page (starting from 1).
	 * @return array(xItem)
	 * @static
	 */
	function findReplies($parentid,$nelementpage = 0,$npage = 0)
	{
		return xItem::toSpecificItem(xItemDAO::findReplies($parentid,$nelementpage,$npage));
	}
	
	
	/**
	 * Retrieves all items (already converted in specific items).
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
	function find($type = NULL,$title = NULL,$author = NULL,$content = NULL,$cathegory = NULL,$nelementpage = 0,$npage = 0)
	{
		return xItem::toSpecificItem(xItemDAO::find($type,$title,$author,$content,$cathegory,$nelementpage,$npage))
	}
	
	
	/**
	 * Return a form element for asking for title input
	 *
	 * @param string $var_name The name of the form element
	 * @param string $value
	 * @param bool $mandatory True if this input is manadtory
	 * @return xFormElement
	 * @static
	 */
	function getFormTitleInput($var_name,$value,$mandatory)
	{
		return new xFormElementTextField($var_name,'Title','',$value,$mandatory,new xInputValidatorText(256));
	}
	
	
	/**
	 * Return a form element for asking for body input
	 *
	 * @param string $var_name The name of the form element
	 * @param string $value
	 * @param bool $mandatory True if this input is manadtory
	 * @return xFormElement
	 * @static
	 */
	function getFormBodyInput($var_name,$value,$mandatory)
	{
		return new xFormElementTextArea($var_name,'Body','',$value,$mandatory,new xInputValidatorText(0));
	}
};


?>
