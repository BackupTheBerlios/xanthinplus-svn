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
	* @var mixed
	*/
	var $m_id;
	
	/**
	* @var int
	*/
	var $m_page;
	
	/**
	* @var string
	*/
	var $m_lang;
	
	
	function xPath($lang,$resource,$action,$type = NULL,$id = NULL,$page = NULL,$full_path = NULL)
	{
		$this->m_resource = $resource;
		$this->m_action = $action;
		$this->m_type = $type;
		$this->m_id = $id;
		$this->m_page = $page;
		$this->m_lang = $lang;
		
		
		if($full_path === NULL) //generate the path dinamically
		{
			$this->m_full_path = $lang . '/' . $resource . '/' . $action;
			
			if($type !== NULL)
			{
				$this->m_full_path .= '/'. $type;
				
				if($id !== NULL)
				{
					$this->m_full_path .= '/'. $id;
					
					if($page !== NULL)
						$this->m_full_path .= '/'. $page;
				}
			}
		}
		else
			$this->m_full_path = $full_path;
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
	function _isActionWithId($act)
	{
		switch($act)
		{
			case 'view':
			case 'edit':
			case 'translate':
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
			$path = new xPath(NULL,NULL,NULL,NULL,NULL,NULL,NULL);
			$path->m_full_path = $pieces[0];
			$exploded = explode('/',$pieces[0]);
			
			$i = 0;
			if(! isset($exploded[$i]))
				return $path;
			
			if(! preg_match('#^[A-Z]{2}$#i',$exploded[$i]))
				return NULL;
			$path->m_lang = $exploded[$i++];
			
			if(! isset($exploded[$i]))
				return $path;
			$path->m_resource = $exploded[$i++];
			
			if(! isset($exploded[$i]))
				return $path;
			$path->m_action .= $exploded[$i++];
			
			if(! isset($exploded[$i]))
				return $path;
			$path->m_type = $exploded[$i++];
			
			if(! isset($exploded[$i]))
				return $path;
			$path->m_id = $exploded[$i++];
			
			if(! isset($exploded[$i]))
				return $path;
			$path->m_page = $exploded[$i];
			
			return $path;
	    }
	}
	
	/**
	 * Outputs a link based on this path.
	 *
	 * @return string
	 */
	function getLink()
	{
		//todo
		return '?p='.$this->m_full_path;
	}
	
	
	/**
	 * Outputs a link based on given params.
	 *
	 * @return string
	 * @static
	 */
	function renderLink($lang,$resource,$action,$type = NULL,$id = NULL,$page = NULL)
	{
		$path = new xPath($lang,$resource,$action,$type,$id,$page);
		return $path->getLink();
	}
	
};

?>