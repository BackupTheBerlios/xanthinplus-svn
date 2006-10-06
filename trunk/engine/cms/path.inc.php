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
	* @var string
	*/
	var $m_full_path;
	
	/**
	* @var string
	*/
	var $m_resource;
	
	/**
	* @var string
	*/
	var $m_action;
	
	/**
	* @var string
	*/
	var $m_type;
	
	/**
	* @var string
	*/
	var $m_parent_cat;
	
	/**
	* @var mixed
	*/
	var $m_id;
	
	/**
	* @var int
	*/
	var $m_page;
	
	
	function xPath()
	{
		$this->m_full_path = NULL;
		
		$this->m_resource = NULL;
		$this->m_action = NULL;
		$this->m_type = NULL;
		$this->m_parent_cat = NULL;
		$this->m_id = NULL;
		$this->m_page = NULL;
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
	 * @access private
	 * @return bool 
	 */
	function _isSpecialResource($res)
	{
		switch($res)
		{
			case 'admin':
			case 'user':
			return true;
		}
		
		return false;
	}
	
	/**
	 * @access private
	 * @return bool 
	 */
	function _isActionWithId($act)
	{
		switch($act)
		{
			case 'view':
			case 'edit':
			return true;
		}
		
		return false;
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
	    if(!preg_match('#^[A-Z][A-Z0-9_-]*(/[A-Z0-9_-]+)*$#i',$path,$pieces))
		{
	        return NULL;
	    }
		else
		{
			$path = new xPath();
			$path->m_full_path = $pieces[0];
			$exploded = explode('/',$pieces[0]);
			
			$i = 0;
			if(! isset($exploded[$i]))
				return $path;
			$path->m_resource = $exploded[$i++];
			
			if(xPath::_isSpecialResource($path->m_resource))
			{
				if(! isset($exploded[$i]))
					return $path;
				$path->m_resource .= '/' . $exploded[$i++];
			}
			
			if(! isset($exploded[$i]))
				return $path;
			$path->m_action .= $exploded[$i++];
			
			
			if(xPath::_isActionWithId($path->m_action))
			{
				if(! isset($exploded[$i]))
					return $path;
				$path->m_id = $exploded[$i++];
				
				if(! isset($exploded[$i]))
					return $path;
				$path->m_page = $exploded[$i];
			}
			else
			{
				if(! isset($exploded[$i]))
					return $path;
				$path->m_type = $exploded[$i++];
				
				if(! isset($exploded[$i]))
					return $path;
				$path->m_parent_cat = $exploded[$i++];
			}
			
			return $path;
	    }
	}
};

?>