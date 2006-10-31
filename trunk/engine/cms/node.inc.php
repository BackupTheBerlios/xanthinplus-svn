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
					$tmp = xCathegory::load($parent_cat);
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
	 * @abstract
	 */
	function renderBrief()
	{
		assert(false);
	}
	
	/** 
	 * Delete a node from db using its id
	 *
	 * @param int $id
	 * @return bool FALSE on error
	 * @static
	 */
	function deleteById($id)
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
	function load($id)
	{
		return xNodeDAO::load($id);
	}
	
	
	function find($type,$parent_cat,$author)
	{
		//todo
	}
	
	/**
	 * @abstract
	 * @return array(xOperation)
	 */
	function getOperations()
	{
		assert(false);
	}
	
	/**
	 * @static
	 */
	function registerNodeTypeClass($node_type,$class_name)
	{
		global $xanth_node_type_classes;
		$xanth_node_type_classes[$node_type] = $class_name;
	}
	
	
	/**
	 * @static
	 */
	function getNodeTypeClass($node_type)
	{
		global $xanth_node_type_classes;
		if(isset($xanth_node_type_classes[$node_type]))
			return $xanth_node_type_classes[$node_type];
		
		return NULL;
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
		
		//format operations
		$ops = $this->getOperations();
		$formatted = array();
		foreach($ops as $op)
			$formatted[$op->m_name] = array('link' => $op->getLink('node',$this->m_type,$this->m_id,$this->m_lang),
				'description' => $op->m_description);
			
		$operations = xTheme::render1('renderNodeOperations',$formatted);
		
		return xTheme::render4('renderNode',$this->m_type,$title,$content,$operations);
	}
	
	
	// DOCS INHERITHED  ========================================================
	function renderBrief()
	{
		$error = '';
		$content = xContentFilterController::applyFilter($this->m_content_filter,$this->m_content,$error);
		$title = xContentFilterController::applyFilter('notags',$this->m_title,$error);
		
		//format operations
		$ops = $this->getOperations();
		$formatted = array();
		foreach($ops as $op)
			$formatted[$op->m_name] = array('link' => $op->getLink('node',$this->m_type,$this->m_id,$this->m_lang),
				'description' => $op->m_description);
			
		$operations = xTheme::render1('renderNodeOperations',$formatted);
		
		return xTheme::render4('renderBriefNode',$this->m_type,$title,$content,$operations);
	}
	
	
	/**
	 * Retrieve a node from db.
	 *
	 * @return xItem
	 * @static
	 */
	function load($id,$lang)
	{
		return xNodeI18NDAO::load($id,$lang);
	}
	
	/** 
	 * Delete the node translation and if this is the last translation deletes the node at all.
	 */
	function deleteTranslation($id,$lang)
	{
		return xNodeI18NDAO::deleteTranslation($id,$lang);
	}
	
	
	function find($type,$parent_cat,$author,$lang)
	{
		//todo
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
	
	
	/**
	 * @return array(xOperation)
	 */
	function getOperations()
	{
		return array
			(
				new xOperation('edit_translation','Edit translation',''),
				new xOperation('delete_translation','Delete translation',''),
				new xOperation('delete_node','Delete node','')
			);
	}
};



?>
