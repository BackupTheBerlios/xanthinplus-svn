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

require_once('engine/components/page/page.class.inc.php');

/*
* Handle page creation.
*/
function xanth_page_page_creation($hook_primary_id,$hook_secondary_id)
{
	//retrieve path
	$path = xXanthPath::get_current();
	if($path == NULL)
	{
		xanth_log(LOG_LEVEL_ERROR,'Invalid xanth path','page',__FUNCTION__);
	}
	
	$page = new xPage($path);
	
	echo $page->render();
}



function xanth_init_component_page()
{
	xanth_register_mono_hook(MONO_HOOK_PAGE_CREATE,NULL,'xanth_page_page_creation');
}



?>