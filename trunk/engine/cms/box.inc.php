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
*/
class xBox extends xElement
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
	var $m_title;
	
	/**
	 * if empty (NULL,FALSE,...) no area assignation
	 *
	 * @var string
	 * @access public
	 */
	var $m_area;
	
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
	 * @var int
	 * @access public
	 */
	var $m_filterset;
	
	/**
	* Contructor
	*
	* @param string $id
	* @param string $title
	* @param string $type
	* @param int $filterset
	* @param string $area
	*/
	function xBox($name,$title,$type,$weight,$filterset = NULL,$area = NULL)
	{
		$this->xElement();
		
		$this->m_weight = $weight;
		$this->m_filterset = $filterset;
		$this->m_name = $name;
		$this->m_title = $title;
		$this->m_area = $area;
		$this->m_type = $type;
	}
	
	// DOCS INHERITHED  ========================================================
	function render()
	{
		//here we will provide a check for access filter.
		if(! xAccessFilterSet::checkAccessByFilterSetId($this->m_filterset))
		{
			return NULL;
		}
		
		return $this->onRender();
	}
	
	// DOCS INHERITHED  ========================================================
	function onRender()
	{
		//cannot render a simple box, you should convert this box into a 
		//specified box to be able to render it.
		assert(FALSE);
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
	 * Insert this object into db
	 *
	 * @return bool FALSE on error
	 */
	function dbInsert()
	{
		return xBoxDAO::insert($this);
	}
	
	/**
	 * Convert a simple xBox into a specific xBox child object that correspond to the box type 
	 * and ready to be rendered.
	 *
	 * @return xBox A specific xBox child object corresponding to the specified type or NULL if not found.
	 * @static
	 */
	function toSpecificBox($box)
	{
		$newbox = NULL;
		
		//check for built-in box type
		if($box->m_type == "dynamic")
		{
			$newbox = xBoxDynamic::toSpecificBox($box);
		}
		elseif($box->m_type == "static")
		{
			$newbox = xBoxStatic::toSpecificBox($box);
		}
		elseif($box->m_type == "menu")
		{
			$newbox = xMenu::toSpecificBox($box);
		}
		
		return $newbox;
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

	/**
	 * Retrieve all boxes assigned to a specified area. Returned boxe object correspond already to their 
	 * type and are ready to be rendered.
	 *
	 * @return array(xBox)
	 * @static
	 */
	function getBoxesForArea($name)
	{
		$boxes = xBoxDAO::find($name);
		$boxes_new = array();
		
		//convert in dynamic or static
		foreach($boxes as $box)
		{
			$boxnew = xBox::toSpecificBox($box);
			if($boxnew != NULL)
			{
				$boxes_new[] = $boxnew;
			}
		}
		
		//now order the boxes by weight
		usort($boxes_new, "_objWeightCompare");
		
		return $boxes_new;
	}
};
	

/**
 * Represent a static box. Static boxes have their content stored in database an renderized by selected filter.
 */
class xBoxStatic extends xBox
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
	function xBoxStatic($name,$title,$type,$weight,$content,$content_filter,$filterset,$area = NULL)
	{
		xBox::xBox($name,$title,$type,$weight,$filterset,$area);
		
		$this->m_content = $content;
		$this->m_content_filter = $content_filter;
	}
	
	// DOCS INHERITHED  ========================================================
	function onRender()
	{
		//!@TODO: filter content here
		
		return xTheme::render3('renderBox',$this->m_name,$this->m_title,$this->m_content);
	}
	
	/**
	 * Insert this object into db
	 *
	 * @return bool FALSE on error
	 */
	function dbInsert()
	{
		return xBoxStaticDAO::insert($this);
	}
	
	/**
	 * Constructs an return a xBoxStatic object derived from a simple xBox 
	 *
	 * @return xBoxStatic
	 * @static
	 */
	function toSpecificBox($box)
	{
		//retrieve additional data from db
		return xBoxStaticDAO::toSpecificBox($box);
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
	 *
	 * @param string $name
	 * @param string $title
	 * @param string $area
	 * @param string $type
	 */
	function xBoxStatic($name,$title,$type,$weight,$filterset,$area = NULL)
	{
		xBox::xBox($name,$title,$type,$weight,$filterset,$area);
	}
	
	/**
	 * @abstract
	 */
	function onRender()
	{
		//virtual
		assert(FALSE);
	}
	
	
	/**
	 * Constructs an return a xBoxStatic object derived from a simple xBox 
	 *
	 * @return xBoxDynamic
	 * @static
	 */
	function toSpecificBox($box)
	{
		//ask for box from module
		$newbox = xModule::callWithSingleResult1('xm_getDynamicBox',$box);
		
		if($newbox == NULL)
		{
			xLog::log(LOG_LEVEL_ERROR,'Cannot retrieve dynamic box'. $box->m_name);
		}
		
		return $newbox;
	}
	
};

?>
