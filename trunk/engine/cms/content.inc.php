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
 * @abstract
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
	var $m_content;
	
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
	
	
	var $m_path;
	
	/**
	 * Create an empty content. You call onCreate() to let the object fill itself with all data.
	 * Do not forgive to check permission with onCheckPermission() method before you call onCreate(),
	 * this is VERY IMPORTANT for security and correctness.
	 */
	function xContent($path)
	{
		$this->xElement();
		
		$this->m_title = '';
		$this->m_description = '';
		$this->m_keywords = '';
		$this->m_content = '';
		$this->m_path = $path;
	}
	
	
	/**
	 * @access protected
	 */
	function _set($title,$content,$description,$keywords)
	{
		$this->m_title = $title;
		$this->m_description = $description;
		$this->m_keywords = $keywords;
		$this->m_content = $content;
	}
	
	/**
	 * Simply return $this->m_content.
	 */
	function onRender()
	{
		return $this->m_content;
	}

	/**
	 * After permissions were checked, the xContent will be filled with data created by the code contained
	 * in this method.
	 *
	 * @return bool Return boolean TRUE if content was created successfully, otherwise it returns an alternative
	 * xContentSimple object representing the error. 
	 * Usually if the error is not critical (eg. a db insert failed) is better to return TRUE and
	 * leave an user notification.
	 * @abstract
	 */
	function onCreate()
	{
		//must override
		assert(FALSE);
	}
	
	/**
	 * Check if the content can be created.
	 *
	 * @return bool
	 * @abstract
	 */
	function onCheckPermission()
	{
		//must override
		assert(FALSE);
	}
	
	
	//----------------STATIC FUNCTIONS----------------------------------------------
	//----------------STATIC FUNCTIONS----------------------------------------------
	//----------------STATIC FUNCTIONS----------------------------------------------
	
	
	/**
	 * Get the content.
	 *
	 * @param xXanthPath $path
	 * @return xContent
	 * @static
	 */
	function getContent($path)
	{
		$content = NULL;
		
		//ask modules for a valid content for the current path.
		$content = xModule::callWithSingleResult1('xm_contentFactory',$path);
		
		//not found
		if($content === NULL)
		{
			$content = new xContentNotFound($path);
		}
		elseif($content->onCheckPermission())
		{
			$res = $content->onCreate();
			if($res !== TRUE)
			{
				$content = $res;
			}
		}
		else
		{
			$content = new xContentNotAuthorized($path);
		}
		
		
		
		return $content;
	}
};


/**
 * Represent a simple content, with no permission check and immmediately created content.
 */
class xContentSimple extends xContent
{

	/**
	 * Create a simple content.
	 */
	function xContentSimple($title,$content,$description,$keywords,$path)
	{
		xContent::_set($title,$content,$description,$keywords);
		
		$this->m_path = $path;
	}
	
	/**
	 * Simply do nothing and returns true;
	 */
	function onCreate()
	{
		return true;
	}
	
	/**
	 * Returns always TRUE.
	 */
	function onCheckPermission()
	{
		return TRUE;
	}
};




/**
 * Represent a generic error page.
 */
class xContentError extends xContentSimple
{
	/**
	 * 
	 */
	function xContentError($error,$path)
	{
		$content = '<b>There was an error while creating the page content: ' . $error . '</b>';
		
		$this->xContentSimple('Error',$content,'','');
	}
};




/**
 * Represent a not authorized page.
 */
class xContentNotAuthorized extends xContentSimple
{
	/**
	 * 
	 */
	function xContentNotAuthorized($path)
	{
		$this->xContentSimple('Access Denied','You are not authorized to access this page','','',$path);
	}
};


/**
 * Represent a not found error page
 */
class xContentNotFound extends xContentSimple
{
	/**
	 * 
	 */
	function xContentNotFound($path)
	{
		$this->xContentSimple('Page not found','The page you requested was not found','','',$path);
	}
};



?>
