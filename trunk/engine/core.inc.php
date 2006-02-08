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
* @defgroup Hooks Hooks
*/

/**
* @ingroup Hooks
* An event triggered on page creation.Usually you never need to handle this event, it exists only for use in cms core.\n
* Return the created page.
*/
define('MONO_HOOK_PAGE_CREATE','mono_hook_page_create');

/**
* @ingroup Hooks
* An event triggered on page creation.Usually you never need to handle this event, it exists only for use in cms core.\n
* No return needed.
*/
define('MULTI_HOOK_PAGE_CREATE_EVT','multi_hook_page_create_evt');

/**
* @ingroup Hooks
* An event triggered on main entry creation.The main entry is the main content of the page.
* You can also register for a secondary hook that represent the path of the page you want to manage creation.
* @param $arguments[0]  resource id if one is selected in xanth path.
* @returns an xPageContent object
*/
define('MONO_HOOK_PAGE_CONTENT_CREATE','mono_hook_page_content_create');

/**
*
*/
class xPageContent
{
	var $title;
	var $body;
	var $description;
	var $keywords;
	
	function xPageContent($title,$body,$description = NULL,$keywords = NULL)
	{
		$this->title = $title;
		$this->body = $body;
		$this->description = $description;
		$this->keywords = $keywords;
	}
}

/**
*
*/
class xSpecialPage
{

	/**
	*
	*/
	function access_denied()
	{
		xanth_log(LOG_LEVEL_ERROR,"Access denied");
		return new xPageContent("Access denied",'');
	}
}

/**
*
*/
class xXanthPath
{
	var $base_path;
	var $resource_id;
	
	function xXanthPath($base_path = NULL,$resource_id = NULL)
	{
		$this->resource_id = $resource_id;
		$this->base_path = $base_path;
	}
	
	
	/**
	 * Return a valid xCmsPath object on success, false on parsing error.
	 */
	function get_current()
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
	*Return NULL if fails to parse, otherwise a xXanthPath object
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
			$path->base_path = $pieces[1];
			if(isSet($pieces[5]))
			{
				$path->resource_id = $pieces[5];
			}
			return $path;
	    }
	}
};


?>