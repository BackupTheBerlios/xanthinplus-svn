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
* Represent box visual element. The box id is a string.
* @abstract
*/
class xBox extends xElement
{
	/**
	 * @var string
	 * @access public
	 */
	var $m_name;
	
	/**
	 * The type of the box
	 *
	 * @var string
	 * @access public
	 */
	var $m_type;
	
	/**
	 * @var int
	 * @access public
	 */
	var $m_weight;
	
	/**
	 * @var xShowFilter
	 * @access public
	 */
	var $m_show_filter;
	
	
	/**
	* Contructor
	*/
	function xBox($name,$type,$weight,$show_filter)
	{
		$this->xElement();
		
		$this->m_weight = $weight;
		$this->m_name = $name;
		$this->m_type = $type;
		$this->m_show_filter = $show_filter;
	}
	
	// DOCS INHERITHED  ========================================================
	function render()
	{
		//abstract method
		assert(FALSE);
	}
	
	/**
	 * Check if the box can be rendered.In particular checks show filters.
	 * If you override this method, please call xBox::onCheckPreconditions()
	 * before doing your checks.
	 *
	 * @return bool Boolean TRUE if the content can be created, FALSE otherwise.
	 */
	function onCheckPreconditions()
	{
		$path = xPath::getCurrent();
		return $this->m_show_filter->check($path);
	}
	
	/**
	 * Delete this object from db
	 *
	 * @return bool FALSE on error
	 */
	function dbDelete()
	{
		return  xBoxDAO::delete($this);
	}
	
	/**
	 * Fetch a specific box object given the name and type.
	 *
	 * @return xBox A specific xBox child object or NULL if not found.
	 * @static
	 */
	function fetchBox($boxname,$type,$lang)
	{
		return xModule::callWithSingleResult3('xm_fetchBox',$boxname,$type,$lang);
	}
	
	/**
	 * Retrieve all boxes from db
	 *
	 * @return array(xBox)
	 */
	function findAll()
	{
		return xBoxDAO::findAll();
	}
};
	


/**
* Represent an internationalized box.
* @abstract
*/
class xBoxI18N extends xBox
{
	/**
	 * @var string
	 * @access public
	 */
	var $m_title;
	
	/**
	 * The type of the box
	 *
	 * @var string
	 * @access public
	 */
	var $m_lang;
	
	/**
	* Contructor
	*/
	function xBoxI18N($name,$type,$weight,$show_filter,$title,$lang)
	{
		$this->xBox($name,$type,$weight,$show_filter);
		
		$this->m_title = $title;
		$this->m_lang = $lang;
	}
	
	/**
	 * Retrieve all boxes from db
	 *
	 * @return array(xBoxI18N)
	 */
	function findAll($lang)
	{
		return xBoxI18NDAO::findAll($lang);
	}
};

	
	
	

/**
 * Represent a custom user box.
 */
class xBoxCustom extends xBoxI18N
{
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
	* Contructor
	*
	* @param string $name
	* @param string $title
	* @param string $type
	* @param string $content
	* @param string $content_filter
	* @param string $area
	*/
	function xBoxCustom($name,$type,$weight,$show_filter,$title,$lang,$content,$content_filter)
	{
		xBoxI18N::xBoxI18N($name,$type,$weight,$show_filter,$title,$lang);
		
		$this->m_content = $content;
		$this->m_content_filter = $content_filter;
	}
	
	
	// DOCS INHERITHED  ========================================================
	function render()
	{
		$error = '';
		$title = xContentFilterController::applyFilter('notags',$this->m_title,$error);
		$content = xContentFilterController::applyFilter($this->m_content_filter,$this->m_content,$error);
		
		return xTheme::render3('renderBox',$this->m_name,$this->m_title,$this->m_content);
	}
	
	/**
	 * Insert this object into db
	 *
	 * @return bool FALSE on error
	 */
	function dbInsert()
	{
		return xBoxCustomDAO::insert($this);
	}
	
	/**
	 * Insert as a new box translation
	 *
	 * @return bool FALSE on error
	 */
	function dbInsertTranslation()
	{
		return xBoxCustomDAO::insertTranslation($this);
	}
	
	
	/**
	 * Delete this object from db
	 *
	 * @return bool FALSE on error
	 */
	function dbDeleteTranslation()
	{
		return xBoxCustomDAO::deleteTranslation($this->m_name,$this->m_lang);
	}
	
	/**
	 * Load an xBoxCustom from db
	 *
	 * @return bool NULL on error
	 */
	function dbLoad($name)
	{
		return xBoxCustomDAO::load($name);
	}
};


/**
 * Represent a dynamic. A dynamic box is generated dynamically from a module.
 * @abstract
 */
class xBoxDynamic extends xBox
{
	/**
	 * Contructor
	 */
	function xBoxStatic($name,$title,$type,$weight,$show_filter)
	{
		xBox::xBox($name,$title,$type,$weight,$show_filter);
	}
};


?>
