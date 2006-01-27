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
*/
define('EVT_CORE_MAIN_ENTRY_CREATE_','evt_core_main_entry_create_');



function xanth_get_xanthpath()
{
	if(isset($_GET['p']))
	{
		$p = $_GET['p'];
	}
	else
	{
		return '';
	}
	
	//see if it is correct
	if(xanth_valid_xanthpath($p))
	{
		return $p;
	}
	else
	{
		return '';
	}
}


/**
*
*/
function xanth_apply_content_format($content,$content_format)
{
	$result = xanth_db_query("SELECT * FROM content_format WHERE name = '%s'",$content_format);
	if($row = xanth_db_fetch_array($result))
	{
		if($row['php_source'])
		{
			ob_start();
			eval($content);
			return ob_get_clean();
		}
		elseif($row['stripped_html'])
		{
			$cont = strip_tags($content,'<strong>','<ul>','<li>','<br>');
			
			if($row['new_line_to_line_break'])
				$cont = nl2br($cont);
			
			return $cont;
		}
		else //full html
		{
			if($row['new_line_to_line_break'])
				$cont = nl2br($content);
			
			return $cont;
		}
	}
	else
	{
		xanth_log(LOG_LEVEL_ERROR,"Unknown content format: $content_format",'core',__FUNCTION__);
	}
}




?>