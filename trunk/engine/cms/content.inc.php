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
	function xContent($title,$description,$keywords)
	{
		$this->xElement();
		
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
	 * @param xXanthPath $path
	 * @return xContent
	 * @static
	 */
	function getContent($path)
	{
		$content = NULL;
		
		//ask modules for a valid content for the current path.
		$content = xModule::callWithSingleResult1('getContent',$path);
		
		//not found
		if($content === NULL)
		{
			$content = new xContentSimple("Page not found",'The page you requested was not found','','');
		}
		
		return $content;
	}
};



/**
 * Represent a simple page content that can contain only statically renderizable data.
 */
class xContentSimple extends xContent
{
	/**
	 * @var string
	 * @access public
	 */
	var $m_content;
	
	/**
	* 
	*
	*/
	function xContentSimple($title,$content,$description,$keywords)
	{
		$this->xContent($title,$description,$keywords);
		
		$this->m_content = $content;
	}
	
	// DOCS INHERITHED  ========================================================
	function render()
	{
		return $this->m_content;
	}
};


/**
 * Represent an error page.
 */
class xContentError extends xContent
{
	/**
	 * @var string
	 * @access public
	 */
	var $m_error;
	
	/**
	 * 
	 *
	 */
	function xContentError($error)
	{
		$this->xContent('Error','Error','');
		
		$this->m_error = $error;
	}
	
	// DOCS INHERITHED  ========================================================
	function render()
	{
		return '<b>There was an error while creating the page content: ' . $this->m_error . '</b>';
	}
};




/**
 * Represent a not authorized page.
 */
class xContentNotAuthorized extends xContentError
{
	/**
	 * 
	 */
	function xContentNotAuthorized()
	{
		$this->xContentError('You are not authorized to access this page');
	}
};

?>
