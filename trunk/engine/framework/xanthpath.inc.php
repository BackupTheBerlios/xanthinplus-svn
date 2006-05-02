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
	var $m_resource_id;
	
	function xXanthPath($base_path = NULL,$resource_id = NULL)
	{
		$this->m_resource_id = $resource_id;
		$this->m_base_path = $base_path;
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
	    if (!preg_match('#^(([A-Z_]+)?(/[A-Z_]+)*)(//([A-Z0-9_-]+))?$#i', $path,$pieces))
		{
	        return NULL;
	    }
		else 
		{
			$path = new xXanthPath();
			$path->m_base_path = $pieces[1];
			if(isSet($pieces[5]))
			{
				$path->m_resource_id = $pieces[5];
			}
			
			return $path;
	    }
	}
};

?>