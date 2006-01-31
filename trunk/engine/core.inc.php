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
* @defgroup Events Events
*/

/**
* \ingroup Events
* An event triggered on page creation.Usually you never need to handle this event, it exists only for use in cms core.\n
*/
define('EVT_CORE_PAGE_CREATE','evt_core_page_create');


/**
* \ingroup Events
* An event triggered on main entry creation.The main entry is the main content of the page.
* This is a special event, you should append to this event identifier the name of the level path you would like to handle content creation.\n
* For example if you want to handle a path named admin/my_module you should register for an event named EVT_CORE_MAIN_ENTRY_CREATE_ . 'admin/module'
* Additional arguments
* $arguments[0] : resource id if one is selected in xanth path.
*/
define('EVT_CORE_MAIN_ENTRY_CREATE_','evt_core_main_entry_create_');


class xCmsPath
{
	var $base_path;
	var $resource_id;
	
	function xCmsPath($base_path = NULL,$resource_id = NULL)
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
		$path = new xCmsPath();
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
		return new xCmsPath();
	}
	
	return xanth_xanthpath_parse($p);
}


?>