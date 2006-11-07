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
	function xContent($path)
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
	function _set($title,$content,$meta_description,$meta_keywords)
	{
		$this->m_title = $title;
		$this->m_meta_description = $meta_description;
		$this->m_meta_keywords = $meta_keywords;
		$this->m_content = $content;
	}
	
	/**
	 * Simply return $this->m_content.
	 */
	function render()
	{
		//output headers
		foreach($this->m_headers as $header)
			header($header);
		
		return $this->m_content;
	}

	/**
	 * After permissions were checked, this xContent will be filled with data created by the code contained
	 * in this method.
	 *
	 * @return bool Return boolean TRUE if content was created successfully, otherwise it returns an alternative
	 * xContent object representing the error.
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
	 * @return mixed Boolean TRUE if the content can be created an alternative xContent otherwise.
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
	 * @access private
	 *
	 * @return mixed
	 */
	function  _processContent($content,$path)
	{
		$res = $content->onCheckPreconditions();
		if($res !== TRUE)
		{
			if(xanth_instanceof($res,'xContent'))
			{
				return xContent::_processContent($res,$path);
			}
			else
			{
				xLog::log(LOG_LEVEL_ERROR,'Invalid result from onCheckPreconditions(). PageContent: '.
					var_export($content,true).' . Result: '. var_export($res,true));
				return xContent::_processContent(new xContentError(),$path);
			}
		}
		else
		{
			$res = $content->onCreate();
			if($res !== TRUE)
			{
				if(xanth_instanceof($res,'xContent'))
					return xContent::_processContent($res,$path);
				else
					assert('FALSE');
			}
		}
		
		return $content;
	}
	
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
		$result = xModule::invoke('xm_fetchContent',array($path));
		if($result !== NULL)
			return xContent::_processContent($result->m_value,$path);
		else
			return new xContentNotFound($path);
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
	function xContentSimple($title,$content,$meta_description,$meta_keywords,$path,$headers = array())
	{
		xContent::_set($title,$content,$meta_description,$meta_keywords);
		$this->m_headers = $headers;
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
	function onCheckPreconditions()
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
	function xContentError($path,$error = 'An unexpected error occurs. Please contact administrator',
		$headers = array())
	{
		$content = '<b>Error: ' . $error . '</b>';
		xContentSimple::xContentSimple('Error',$content,'','',$path,$headers);
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
	function xContentNotAuthorized($path,$extra_content = '',$headers = array())
	{
		xContentSimple::xContentSimple('Access Denied','You are not authorized to access this page' . $extra_content,
			'','',$path,$headers);
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
	function xContentNotFound($path,$extra_content = '',$headers = array())
	{
		xContentSimple::xContentSimple('Page not found','The page you requested was not found' . $extra_content,
			'','',$path,$headers);
		
		xLog::log(LOG_LEVEL_WARNING,'Page not found','',0);
	}
};



?>