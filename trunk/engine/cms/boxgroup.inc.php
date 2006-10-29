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
* An area in the page. Tha page id is a string.
*/
class xBoxGroup extends xElement
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
	var $m_render;
	
	/**
	 * An array of specific xBox objects
	 * @var array(xBox)
	 * @access public
	 */
	var $m_boxes;
	
	
	/**
	 * Contructor
	 * 
	 * @param string $name
	 */
	function xBoxGroup($name,$description,$render,$boxes)
	{
		$this->xElement();
		
		$this->m_name = $name;
		$this->m_render = (bool)$render;
		$this->m_boxes = $boxes;
		$this->m_description = $description;
	}
	
	// DOCS INHERITHED  ========================================================
	function render()
	{
		$rendered_boxes = array();
		foreach($this->m_boxes as $box)
		{
			if($box->onCheckPreconditions())
				$rendered_boxes[] = $box->render();
		}
		
		return xTheme::render2('renderBoxGroup',$this->m_name,$rendered_boxes);
	}
	
	
	/**
	 * Delete this object from db
	 *
	 * @return bool FALSE on error
	 */
	function delete()
	{
		return  xBoxGroupDAO::delete($this);
	}
	
	/**
	 * Insert this object into db
	 *
	 * @return bool FALSE on error
	 */
	function insert()
	{
		return xBoxGroupDAO::insert($this);
	}
	
	
	/**
	 * Insert this object into db
	 *
	 * @return bool FALSE on error
	 */
	function update()
	{
		return xBoxGroupDAO::update($this);
	}
	
	/**
	 *
	 */
	function loadBoxes($lang = NULL,$flexible_lang = TRUE)
	{
		$rows = xBoxGroupDAO::findBoxNamesAndTypesByGroup($this->m_name);
		$this->m_boxes = array();
		foreach($rows as $row)
		{
			$box = xBoxI18N::fetchBox($row->name,$row->type,$lang,$flexible_lang);
			if($box != NULL)
				$this->m_boxes[] = $box;
		}
	}
		
		
	/**
	 * Insert this object into db
	 *
	 * @return bool FALSE on error
	 */
	function load($name)
	{
		return xBoxGroupDAO::load($name);
	}
	
	/**
	 * @param mixed $renderizable Can be true, false or NULL.
	 * @return bool FALSE on error
	 */
	function find($renderizable)
	{
		return xBoxGroupDAO::find($renderizable);
	}
}


?>