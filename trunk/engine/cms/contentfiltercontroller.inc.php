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
 * A helper class for content filtering
 */
class xContentFilterController
{
	function xContentFilterController()
	{
		assert(FALSE);
	}
	
	/**
	 *
	 * @return string
	 */
	function applyFilter($filtername,$input)
	{
		switch($filtername)
		{
			case 'html':
				$filter = xContentFilterBypass();
				break;
				
			case 'php':
				$filter = xContentFilterPhp();
				break;
				
			case 'bbcode':
				$filter = xContentFilterBBCode();
				break;
				
			case 'notags':
				$filter = xContentFilterNoTags();
				break;
				
			default:
				xLog::log(LOG_LEVEL_WARNING,'Invalid content filter name: ' . $filtername);
				return '';
		}
		
		$ret = $filter->filter($input);
		if($ret === NULL)
		{
			xLog::log(LOG_LEVEL_WARNING,'Error while filtering content: ' . $filter->m_last_error);
			return '';
		}
		
		return $ret;
	}
}

?>