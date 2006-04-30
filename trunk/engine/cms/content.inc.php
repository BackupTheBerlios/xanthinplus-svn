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
* Represent  a page content.
*/
class xContent extends xElement
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
	var $m_description;
	
	/**
	* @var string
	* @access public
	*/
	var $m_keywords;
	
	/**
	* 
	*
	*/
	function xContent($id,$title,$description,$keywords)
	{
		$this->xElement($id);
		
		$this->m_title = $title;
		$this->m_description = $description;
		$this->m_keywords = $keywords;
	}
	
	// DOCS INHERITHED  ========================================================
	function render()
	{
		//must override
		assert(FALSE);
	}

	/**
	 * Gets the content.
	 *
	 * @return xContent
	 * @static
	 */
	function getContent()
	{
		$content = NULL;
		
		//extract the current path
		$path = xXanthPath::getCurrent();
		
		//ask modules for a valid content for the current path.
		$modules = xModule::getModules();
		foreach($modules as $module)
		{
			if(method_exists($module,'getContent'))
			{
				$content = $module->getContent($path);
				if($content != NULL)
				{
					return $content;
				}
			}
		}
		
		return $content;
	}
};


?>
