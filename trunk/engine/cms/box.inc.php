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
	var $m_id;
	
	/**
	* @var string
	* @access public
	*/
	var $m_title;
	
	/**
	* if dynamic, content and content format will be ignored. Contents will be generated dymanically by some module.
	*
	* @var bool
	* @access public
	*/ 
	var $m_is_dynamic;
	
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
	* if empty (NULL,FALSE,...) no area assignation
	*
	* @var string
	* @access public
	*/
	var $m_area;
	
	/**
	* Contructor
	*
	* @param string $id
	* @param string $title
	* @param bool $is_dynamic
	* @param string $content
	* @param string $content_format
	* @param string $area
	*/
	function xBox($id,$title,$is_dynamic,$content,$content_filter,$area = NULL)
	{
		$this->xElement();
		
		$this->m_id = $id;
		$this->m_title = $title;
		$this->m_is_dynamic = $is_dynamic;
		$this->m_content = $content;
		$this->content_filter = $content_filter;
		$this->m_area = $area;
	}
	
	// DOCS INHERITHED  ========================================================
	function render()
	{
		return NULL;
	}
	
	/**
	* Insert a this box element into database.
	*/
	function dbInsert()
	{
		xBoxDAO::insert($this);
	}
	
	/**
	*
	*
	* @static
	*/
	function getBoxesForArea($name)
	{
		$boxes = xBoxDAO::find($this->m_name);
		$boxes_new = array();
		
		//convert in dynamic or static
		foreach($boxes as $box)
		{
			if($box->m_is_dynamic)
			{
				//ask for box from module
				$newbox = xModule::callWithSingleResult1('getDynamicBox',$box);
				
				if($newbox == NULL)
				{
					xLog::log(LOG_LEVEL_ERROR,'Cannot retrieve dynamic box'. $box->m_id);
				}
				else
				{
					$boxes_new[] = $newbox; 
				}
			}
			else
			{
				$boxes_new[] = new xBoxStatic($box->m_id,$box->m_title,$box->m_is_dynamic,$box->m_content,
					$box->m_content_filter,$box->m_area);
			}
		}
		
		return $boxes_new;
	}
};


/**
* Represent a static box. Static boxes have their content stored in database an renderized by selected filter.
*/
class xBoxStatic extends xBox
{
	/**
	* Contructor
	*
	* @param string $id
	* @param string $title
	* @param bool $is_dynamic
	* @param string $content
	* @param string $content_format
	* @param string $area
	*/
	function xBoxStatic($id,$title,$is_dynamic,$content,$content_filter,$area = NULL)
	{
		xBox::xBox($id,$title,$is_dynamic,$content,$content_filter,$area);
	}
	
	// DOCS INHERITHED  ========================================================
	function render()
	{
		return xTheme::getActive()->renderBox($this->m_id,$this->m_title,$this->m_content);
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
	* @param string $id
	* @param string $title
	* @param bool $is_dynamic
	* @param string $content
	* @param string $content_format
	* @param string $area
	*/
	function xBoxStatic($id,$title,$is_dynamic,$content,$content_filter,$area = NULL)
	{
		xBox::xBox($id,$title,$is_dynamic,$content,$content_filter,$area);
	}
	
	/**
	 * @abstract
	 */
	function render()
	{
		//virtual
		assert(FALSE);
	}
};

?>
