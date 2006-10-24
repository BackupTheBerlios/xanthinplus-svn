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
 * Represent a node in the CMS.
 */
class xNode extends xElement
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
	var $m_author;
	
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
	 * @var timestamp
	 * @access public
	 */
	var $m_edit_time;
	
	/**
	 * @var array(xCathegory)
	 * @access public
	 */
	var $m_parent_cathegories;

	/**
	 *
	 * @param array(mixed) $parent_cathegories An array of xCathegory objects or an array of cathegories ids
	 */
	function xNode($id,$type,$author,$content_filter,$parent_cathegories = array(),
		$creation_time = NULL,$edit_time = NULL)
	{
		$this->xElement();
		
		$this->m_id = (int) $id;
		$this->m_type = $type;
		$this->m_author = $author;
		$this->m_content_filter = $content_filter;
		$this->m_creation_time = $creation_time;
		$this->m_edit_time = $edit_time;
		
		if(count($parent_cathegories) > 0)
		{
			if(is_numeric($parent_cathegories[0]))
			{
				foreach($parent_cathegories as $parent_cat)
				{
					$tmp = xCathegory::dbLoad($parent_cat);
					if($tmp != NULL)
						$this->m_parent_cathegories[] = $tmp;
				}
			}
			else
			{
				$this->m_parent_cathegories = $parent_cathegories;
			}
		}
		else
		{
			$this->m_parent_cathegories =  array();
		}
	}
	
	/** 
	 * Delete a node from db using its id
	 *
	 * @param int $id
	 * @return bool FALSE on error
	 * @static
	 */
	function dbDeleteById($id)
	{
		return xNodeDAO::delete($id);
	}
	
	
	/**
	 * @return string NULL on error
	 */
	function getNodeTypeById($id)
	{
		return xNodeDAO::getNodeTypeById($id);
	}
	
	
	/**
	 * Retrieve a node from db.
	 *
	 * @return xItem
	 * @static
	 */
	function dbLoad($id)
	{
		return xNodeDAO::load($id);
	}
	
	
	function find()
	{
		return xNodeDAO::find();
	}
	
	/**
	 * @abstract
	 * @return array(xOperation)
	 */
	function getOperations()
	{
		assert(false);
	}
};



/**
 * Represent a node in the CMS.
 */
class xNodeI18N extends xNode
{
	/**
	 * @var string
	 * @access public
	 */
	var $m_title;
	
	/**
	 * @var string
	 * @access public
	 */
	var $m_content;

	/**
	 * @var string
	 */
	var $m_lang;
	
	/**
	 * @var string
	 */
	var $m_translator;
	
	/**
	 *
	 * @param array(mixed) $parent_cathegories An array of xCathegory objects or an array of cathegories ids
	 */
	function xNodeI18N($id,$type,$author,$content_filter,$title,$content,$lang,$translator,$parent_cathegories = array(),
		$creation_time = NULL,$edit_time = NULL)
	{
		$this->xNode($id,$type,$author,$content_filter,$parent_cathegories,$creation_time,$edit_time);
		
		$this->m_title = $title;
		$this->m_content = $content;
		$this->m_lang = $lang;
		$this->m_translator = $translator;
	}
	
	
	// DOCS INHERITHED  ========================================================
	function render()
	{
		$error = '';
		$content = xContentFilterController::applyFilter($this->m_content_filter,$this->m_content,$error);
		$title = xContentFilterController::applyFilter('notags',$this->m_title,$error);
		return xTheme::render3('renderNode',$this->m_type,$title,$content);
	}
	
	/**
	 * Retrieve a node from db.
	 *
	 * @return xItem
	 * @static
	 */
	function dbLoad($id,$lang)
	{
		return xNodeI18NDAO::load($id,$lang);
	}
	
	
	function findAll($lang)
	{
		return xNodeI18NDAO::findAll($lang);
	}
	
	/**
	 * @static
	 */
	function getNodeTranslations($nodeid)
	{
		return xNodeI18NDAO::getNodeTranslations($nodeid);
	}
	
	/**
	 * @static
	 */
	function isTranslatable($nodeid)
	{
		return xNodeI18NDAO::isTranslatable($nodeid);
	}
	
	/**
	 * @static
	 */
	function existsTranslation($nodeid,$lang)
	{
		return xNodeI18NDAO::existsTranslation($nodeid,$lang);
	}
};



?>
