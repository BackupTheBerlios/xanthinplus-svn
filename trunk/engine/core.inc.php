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
* An event triggered on main entry creation.The main entry is the main content of the page.
* You can also register for a secondary hook that represent the path of the page you want to manage creation.
* Additional arguments
* $arguments[0] : resource id if one is selected in xanth path.
*/
define('MONO_HOOK_MAIN_ENTRY_CREATE','mono_hook_main_entry_create');


class xXanthPath
{
	var $base_path;
	var $resource_id;
	
	function xXanthPath($base_path = NULL,$resource_id = NULL)
	{
		$this->resource_id = $resource_id;
		$this->base_path = $base_path;
	}
};

/**
*Return NULL if fails to parse, otherwise a xanthPth object
*/
function xanth_xanthpath_parse($path) 
{
    if (!preg_match('/^(([A-Z_]+)?(\/[A-Z_]+)*)(\/(\d+))?$/i', $path,$pieces))
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


/**
 * Return a valid xCmsPath object on success, false on parsing error.
 */
function xanth_get_xanthpath()
{
	if(isset($_GET['p']))
	{
		$p = $_GET['p'];
	}
	else
	{
		return new xXanthPath();
	}
	
	return xanth_xanthpath_parse($p);
}


?>