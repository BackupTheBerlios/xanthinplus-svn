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
class xXanthPath
{
	var $m_base_path;
	var $m_vars;
	var $m_full_path;
	
	
	function xXanthPath()
	{
		$this->m_base_path = NULL;
		$this->m_vars = array();
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
	 * @return xXanthPath
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
			return new xXanthPath();
		}
		
		return xXanthPath::_parse($p);
	}


	/**
	* Return NULL if fails to parse, otherwise a xXanthPath object
	*
	* @return xXanthPath
	* @static
	* @private
	*/
	function _parse($path) 
	{
	    if(!preg_match('#^(([A-Z_]+)?(/[A-Z_]+)*)((//[A-Z0-9_-]+\[[A-Z0-9_-]*\])*)$#i', $path,$pieces))
		{
	        return NULL;
	    }
		else 
		{
			$path = new xXanthPath();
			$path->m_full_path = $pieces[0];
			$path->m_base_path = $pieces[1];
			
			if(isSet($pieces[4]))
			{
				if(preg_match_all('#//([A-Z0-9_-]+)\[([A-Z0-9_-]*)\]*#i',$pieces[4],$pieces))
				{
					for($i = 0;$i < count($pieces[1]);$i++)
					{
						$path->m_vars[$pieces[1][$i]] = $pieces[2][$i];
					}
				}
			}
			
			return $path;
	    }
	}
};

?>