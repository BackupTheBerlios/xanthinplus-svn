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

define('XANTH_SHOW_FILTER_INCLUSIVE',1);
define('XANTH_SHOW_FILTER_EXCLUSIVE',2);
define('XANTH_SHOW_FILTER_PHP',3);


/**
* 
*/
class xShowFilter
{
	var $m_type;
	
	var $m_filters;
	
	/**
	 * Create a filter
	 */
	function xShowFilter($type,$filters)
	{
		$this->m_type = $type;
		$this->m_filters = $filters;
	}
	
	/**
	 *
	 */
	function check($path)
	{
		if($this->m_type == XANTH_SHOW_FILTER_INCLUSIVE)
		{
			$filters = explode('\n',$this->m_filters);
			foreach($filters as $filter)
			{
				if($filter != NULL)
				{
					if(preg_match($filter,$path->m_full_path))
					{
						return true;
					}
				}
			}
			
			return false;
		}
		elseif($this->m_type == XANTH_SHOW_FILTER_EXCLUSIVE)
		{
			$filters = explode('\n',$this->m_filters);
			foreach($filters as $filter)
			{
				if($filter != NULL)
				{
					if(preg_match($filter,$path->m_full_path))
					{
						return false;
					}
				}
			}
			return true;
		}
		elseif($this->m_type == XANTH_SHOW_FILTER_PHP)
		{
			if($this->m_filters != NULL)
			{
				if(eval($this->m_filters))
					return true;
			}
				
			return false;
		}
		
		return false;
	}
};


?>
