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

/*
* Handle index creation.
*/
function xanth_frontpage_content_creation($hook_primary_id,$hook_secondary_id,$arguments)
{
	return new xPageContent('Homepage',"this is the frontpage");
}


function xanth_init_component_frontpage()
{
	xanth_register_mono_hook(MONO_HOOK_PAGE_CONTENT_CREATE,NULL,'xanth_frontpage_content_creation');
}



?>