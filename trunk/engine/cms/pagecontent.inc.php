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
class xPageContent extends xElement
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
	var $m_meta_description;
	
	/**
	* @var string
	* @access public
	*/
	var $m_meta_keywords;
	
	/**
	* @var array(string)
	* @access public
	*/
	var $m_headers;
	
	
	
	var $m_path;
	
	/**
	 * Create an empty content. You call onCreate() to let the object fill itself with all data.
	 * Do not forgive to check permission with onCheckPermission() method before you call onCreate(),
	 * this is VERY IMPORTANT for security and correctness.
	 */
	function xPageContent($path)
	{
		$this->xElement();
		
		$this->m_title = '';
		$this->m_meta_description = '';
		$this->m_meta_keywords = '';
		$this->m_content = '';
		$this->m_headers = array();
		$this->m_path = $path;
	}
	
	
	/**
	 * @access protected
	 */
	function _set($title,$content,$meta_description,$meta_keywords,$headers = array())
	{
		$this->m_title = $title;
		$this->m_meta_description = $meta_description;
		$this->m_meta_keywords = $meta_keywords;
		$this->m_content = $content;
		$this->m_headers = $headers;
	}
	
	/**
	 * Simply return $this->m_content.
	 */
	function render()
	{
		//output headers
		foreach($this->m_headers as $header)
		{
			header($header);
		}
		
		return $this->m_content;
	}

	/**
	 * After permissions were checked, the xContent will be filled with data created by the code contained
	 * in this method.
	 *
	 * @return bool Return boolean TRUE if content was created successfully, otherwise it returns an alternative
	 * xPageContent object representing the error.
	 * Usually if the error is not critical (eg. a db insert failed) is better to return TRUE and
	 * post an user notification.
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
	 * @return mixed Boolean TRUE if the content can be created an alternative xPageContent otherwise.
	 * @abstract
	 */
	function onCheckPreconditions()
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
	 * @param xPath $path
	 * @return xContent
	 * @static
	 */
	function fetchContent($path)
	{
		$content = NULL;
		$emptystr = '';

		//try to extract resource and action
		$count = count($path->m_base_path);
		if($count === 0)
		{
			$content = xModule::callWithSingleResult3('xm_fetchContent',$emptystr,$emptystr,$path);
		}
		elseif($count === 1)
		{
			$content = xModule::callWithSingleResult3('xm_fetchContent',$path->m_base_path[0],$emptystr,$path);
		}
		else
		{
			$tmp = $path->m_base_path;
			$action = array_pop($tmp);
			$resource = implode("/",$tmp);
			
			$content = xModule::callWithSingleResult3('xm_fetchContent',$resource,$action,$path);
		}
		
		//search for automatic or full aliasing
		if($content === NULL)
		{
			$content = xModule::callWithSingleResult1('xm_fetchAliasContent',$path);
		}
		
		//not found
		if($content === NULL)
		{
			return new xPageContentNotFound($path);
		}
		
		$res = $content->onCheckPreconditions();
		if($res !== TRUE)
		{
			if(xanth_instanceof($res,'xPageContent'))
			{
				return $res;
			}
			else
			{
				assert('FALSE');
			}
		}
		else
		{
			$res = $content->onCreate();
			if($res !== TRUE)
			{
				if(xanth_instanceof($res,'xPageContent'))
				{
					return $res;
				}
				else
				{
					assert('FALSE');
				}
			}
		}
		
		
		return $content;
	}
};


/**
 * Represent a simple content, with no permission check and immmediately created content.
 */
class xPageContentSimple extends xPageContent
{

	/**
	 * Create a simple content.
	 */
	function xPageContentSimple($title,$content,$meta_description,$meta_keywords,$path,$headers = array())
	{
		xPageContent::_set($title,$content,$meta_description,$meta_keywords,$headers);
		
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
	function onCheckPrecontitions()
	{
		return TRUE;
	}
};




/**
 * Represent a generic error page.
 */
class xPageContentError extends xPageContentSimple
{
	/**
	 * 
	 */
	function xPageContentError($error,$path,$headers = array())
	{
		$content = '<b>There was an error while creating the page content: ' . $error . '</b>';
		
		$this->xPageContentSimple('Error',$content,'','',$headers);
	}
};




/**
 * Represent a not authorized page.
 */
class xPageContentNotAuthorized extends xPageContentSimple
{
	/**
	 * 
	 */
	function xPageContentNotAuthorized($path,$extra_content = '',$headers = array())
	{
		$this->xPageContentSimple('Access Denied','You are not authorized to access this page' . $extra_content,
			'','',$path,$headers);
	}
};


/**
 * Represent a not found error page
 */
class xPageContentNotFound extends xPageContentSimple
{
	/**
	 * 
	 */
	function xPageContentNotFound($path,$extra_content = '',$headers = array())
	{
		$this->xPageContentSimple('Page not found','The page you requested was not found' . $extra_content,
			'','',$path,$headers);
	}
};



?>
