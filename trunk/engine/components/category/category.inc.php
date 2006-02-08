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

require_once('engine/components/category/category.class.inc.php');



function xanth_category_admin_category_create($hook_primary_id,$hook_secondary_id,$arguments)
{
	if(!xUser::check_current_user_access('create category'))
	{
		return xSpecialPage::access_denied();
	}
	
	//create form
	$form = new xForm('?p=admin/category/create');
	$form->elements[] = new xFormElementTextField('cat_title','Title','','',TRUE,new xInputValidatorTextNoTags(256));
	$form->elements[] = new xFormElementTextArea('cat_description','Description','','',TRUE,new xInputValidatorText(-1));
	
	//parent category
	$categories = xCategory::find_all();
	$options = array();
	$options['[no parent]'] = '0';
	foreach($categories as $category)
	{
		$options[$category->title] = $category->id;
	}
	$form->elements[] = new xFormElementOptions('parent_category','Parent category','','',$options,FALSE,FALSE,new xInputValidatorInteger());
	
	//submit buttom
	$form->elements[] = new xFormSubmit('submit','Create');
	
	$ret = $form->validate_input();
	if(isset($ret->valid_data['submit']))
	{
		if(empty($ret->errors))
		{
			$cat = new xCategory(NULL,$ret->valid_data['cat_title'],$ret->valid_data['cat_description'],
				0,$ret->valid_data['parent_category']);
			$cat->insert();
			
			
			return new xPageContent('Category created','Category created');
		}
		else
		{
			foreach($ret->errors as $error)
			{
				xanth_log(LOG_LEVEL_USER_MESSAGE,$error);
			}
		}
	}

	return new xPageContent('Create category',$form->render());
}


function xanth_category_admin_menu_add_link($hook_primary_id,$hook_secondary_id,$arguments)
{
	return 'admin/category/create';
}

/*
*
*/
function xanth_init_component_category()
{
	xanth_register_mono_hook(MONO_HOOK_PAGE_CONTENT_CREATE, 'admin/category/create','xanth_category_admin_category_create');
	
	xanth_register_multi_hook(MULTI_HOOK_ADMIN_MENU_ADD_PATH,NULL,'xanth_category_admin_menu_add_link');
}



?>