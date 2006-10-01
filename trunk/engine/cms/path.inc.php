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
* Represent a path in xanthin engine.
*/
class xPath
{
	/**
	* @var array(string)
	*/
	var $m_base_path;
	
	/**
	* @var int
	*/
	var $m_resource_id;
	
	/**
	* @var int
	*/
	var $m_resource_page_number;
	
	/**
	* @var string
	*/
	var $m_full_path;
	
	function xPath()
	{
		$this->m_base_path = array();
		$this->m_resource_id = NULL;
		$this->m_resource_page_number = NULL;
		$this->m_full_path = NULL;
	}
	
	/**
	 * Retrieve the current complete path as a simple string. Note that this method does not check
	 * the validity of the path.
	 *
	 * @return string
	 */
	function getCurrentAsString()
	{
		if(isset($_GET['p']))
		{
			return $_GET['p'];
		}
		else
		{
			return '';
		}
	}
	
	/**
	 * Return a valid xXanthPath object on success, NULL on parsing error.
	 *
	 * @return xPath
	 * @static
	 */
	function getCurrent()
	{
		if(isset($_GET['p']))
		{
			$p = $_GET['p'];
		}
		else
		{
			return new xPath();
		}
		
		return xPath::_parse($p);
	}


	/**
	* Return NULL if fails to parse, otherwise a xXanthPath object
	*
	* @return xPath
	* @static
	* @private
	*/
	function _parse($path) 
	{
	    if(!preg_match('#^([A-Z][A-Z0-9_-]*(/[A-Z][A-Z0-9_-]*)*)((/[0-9]+){0,2})$#i',$path,$pieces))
		{
	        return NULL;
	    }
		else
		{
			$path = new xPath();
			$path->m_full_path = $pieces[0];
			$path->m_base_path = explode('/',$pieces[1]);
			
			if(!empty($pieces[3]))
			{
				$tmp = explode('/',$pieces[3]);
				$path->m_resource_id = $tmp[1];
				$path->m_resource_page_number = $tmp[2];
			}

			return $path;
	    }
	}
};

?>