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
* \ingroup Events
* This is a special event, you should append to this event identifier the name of the level path you would like to handle content creation.\n
* For example if you want to andle a path named admin/my_module you should register for an event named EVT_CORE_CREATE_CONTENT_ENTRY_ . 'admin/module'
* Arguments passed to callback are:\n
*/
define('EVT_CORE_CREATE_CONTENT_ENTRY_','evt_core_create_content_entry_');


class xanthContentEntry
{
	var $id;
	var $title;
	var $author;
	var $creation_time;
	var $body;
	var $cathegories;
}



?>