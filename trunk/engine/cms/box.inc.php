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
		
		$this->m_weight = (int) $weight;
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
	function delete()
	{
		return  xBoxDAO::delete($this);
	}
	
	/**
	 * Update
	 *
	 * @return bool FALSE on error
	 */
	function update()
	{
		return  xBoxDAO::update($this);
	}
	
	
	/**
	 * Return the builtin box relative to given name.
	 */
	function registerBoxTypeClass($type,$class_name)
	{
		global $xanth_builtin_boxes;
		$xanth_builtin_boxes[$type] = $class_name;
			
		return NULL;
	}
	
	
	/**
	 * Return the builtin box relative to given name.
	 */
	function getBoxTypeClass($type)
	{
		global $xanth_builtin_boxes;
		if(isset($xanth_builtin_boxes[$type]))
			return $xanth_builtin_boxes[$type];
			
		return NULL;
	}
	
	/**
	 *
	 */
	function findBoxGroups()
	{
		return xBoxGroupDAO::findBoxGroups($this->m_name);
	}
	
	
	/**
	 *
	 */
	function find($name = NULL,$type = NULL)
	{
		return xBoxDAO::find($name,$type);
	}
	
	/**
	 * @return array(xOperation)
	 */
	function getOperations()
	{
		return array
			(
				new xOperation('edit','Edit properties',''),
				new xOperation('delete','Delete','')
			);
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
	 * Fetch a specific box object given the name and type.
	 *
	 * @return xBox A specific xBox child object or NULL if not found.
	 * @static
	 */
	function fetchBox($boxname,$type,$lang,$flexible_lang = TRUE)
	{
		$class_name = xBox::getBoxTypeClass($type);
		if($class_name === NULL)
		{
			xLog::log(LOG_LEVEL_ERROR,'Cannot retrieve box type class name: "'.$type.'"',__FILE__,__LINE__);
			return NULL;
		}
		
		return reset(call_user_func(array( $class_name,'find'),$boxname,$type,$lang,$flexible_lang));
	}
	
	
	/**
	 *
	 */
	function insert()
	{
		return xBoxI18NDAO::insert($this);
	}
	
	/**
	 * Retrieve all boxes from db
	 *
	 * @return array(xBoxI18N)
	 */
	function find($name = NULL,$type = NULL,$lang = NULL,$flexible_lang = true)
	{
		return xBoxI18NDAO::find($name,$type,$lang,$flexible_lang);
	}
	
	
	/**
	 * @return array(xOperation)
	 */
	function getOperations()
	{
		$prev = xBox::getOperations();
		return array_merge($prev,
			array
			(
				new xOperation('edit_translation','Edit translation',''),
				new xOperation('delete_translation','Delete translation','')
			)
		);
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
	function insert()
	{
		return xBoxCustomDAO::insert($this);
	}
	
	/**
	 * Insert as a new box translation
	 *
	 * @return bool FALSE on error
	 */
	function insertTranslation()
	{
		return xBoxCustomDAO::insertTranslation($this);
	}
	
	
	/**
	 * Delete this object from db
	 *
	 * @return bool FALSE on error
	 */
	function deleteTranslation()
	{
		return xBoxCustomDAO::deleteTranslation($this->m_name,$this->m_lang);
	}
	
	/**
	 * Retrieve all boxes from db
	 *
	 * @return array(xBoxI18N)
	 */
	function find($name = NULL,$type = 'custom',$lang = NULL,$flexible_lang = true)
	{
		return xBoxCustomDAO::find($name,$type,$lang,$flexible_lang);
	}
};
xBox::registerBoxTypeClass('custom','xBoxCustom');


/**
 * Represent a dynamic. A dynamic box is generated dynamically from a module.
 * @abstract
 */
class xBoxBuiltin extends xBoxI18N
{
	/**
	 * Contructor
	 */
	function xBoxBuiltin($name,$type,$weight,$show_filter,$title,$lang,$content,$content_filter)
	{
		xBoxI18N::xBoxI18N($name,$type,$weight,$show_filter,$title,$lang,$content,$content_filter);
	}
	
	
	/**
	 * Retrieve all boxes from db
	 *
	 * @return array(xBoxI18N)
	 */
	function find($name = NULL,$type = 'builtin',$lang = NULL,$flexible_lang = true)
	{
		$boxes = xBoxI18N::find($name,$type,$lang,$flexible_lang);
		$ret = array();
		foreach($boxes as $box)
			$ret[] = callWithSingleResult2('xm_fetchBuiltinBox',$name,$lang,$flexible_lang);
		
		return ret;
	}
};
xBox::registerBoxTypeClass('builtin','xBoxBuiltin');


?>