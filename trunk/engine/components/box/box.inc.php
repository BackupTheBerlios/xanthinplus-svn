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

require_once('engine/components/box/box.class.inc.php');

//@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@

function xanth_box_admin_box($hook_primary_id,$hook_secondary_id,$arguments)
{
	if(!xUser::check_current_user_access('manage box'))
	{
		return xSpecialPage::access_denied();
	}
	
	$boxes = xBox::find_all();
	
	$output = "<table>\n";
	$output .= "<tr><th>Name</th><th>Title</th><th>Content format</th><th>Area</th><th>Edit</th><th>Delete</th></tr>\n";
	foreach($boxes as $box)
	{
		$output .= "<tr><td>".$box->name."</td><td>".$box->title."</td><td>".$box->content_format."</td>
		<td>".$box->area."</td><td>Edit</td><td>Delete</td></tr>";
	}
	$output .= "</table>\n";
	
	return new xPageContent('Admin Boxes',$output);
}

//@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@

function xanth_box_create_default_footer_box($hook_primary_id,$hook_secondary_id,$arguments)
{
	return 'Page crated with '. xPageElement::get_db_query_count() .' queries in '.xPageElement::get_execution_time().' seconds';
}

//@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@
function xanth_box_admin_menu_add_link($hook_primary_id,$hook_secondary_id,$arguments)
{
	return 'admin/box';
}


/*
*
*/
function xanth_init_component_box()
{
	xanth_register_mono_hook(MONO_HOOK_PAGE_CONTENT_CREATE, 'admin/box','xanth_box_admin_box');

	xanth_register_mono_hook(MONO_HOOK_CREATE_BOX_CONTENT,'default_footer_box','xanth_box_create_default_footer_box');
	
	xanth_register_multi_hook(MULTI_HOOK_ADMIN_MENU_ADD_PATH,NULL,'xanth_box_admin_menu_add_link');
}



?>