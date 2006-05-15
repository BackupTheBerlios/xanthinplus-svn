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
* Module responsible of item management
*/
class xModuleItem extends xModule
{
	function xModuleItem()
	{
		$this->xModule('Item','engine/cms/components/');
	}
	
	// DOCS INHERITHED  ========================================================
	function getContent($path)
	{
		if($path->m_base_path == 'admin/item')
		{
			return $this->_getContentAdminItem();
		}
		elseif($path->m_base_path == 'admin/item/create')
		{
			return $this->_getContentAdminItemCreate();
		}
		
		return NULL;
	}
	
	/**
	 * @access private
	 */
	function _getContentAdminItemCreate()
	{
		if(!xAccessPermission::checkPermission('admin item create'))
		{
			return new xContentNotAuthorized();
		}
		
		//create form
		$form = new xForm('?p=admin/entry/create');
		
		//item type
		$form->m_elements[] = xItemType::getFormTypeChooser('type','',true);
		
		//item title
		$form->m_elements[] = xItem::getFormTitleInput('title','',true);
		
		//item body
		$form->m_elements[] = xItem::getFormBodyInput('body','',true);
		
		//item filter
		$form->m_elements[] = xContentFilterController::getFormContentFilterChooser('filter','html',TRUE);
		
		//submit buttom
		$form->m_elements[] = new xFormSubmit('submit','Create');
		
		$ret = $form->validate();
		if(isset($ret->m_valid_data['submit']))
		{
			if(empty($ret->m_errors))
			{
				return new xContentSimple("Create new item (generic)",'Here insert the items','','');
			}
			else
			{
				foreach($ret->m_errors as $error)
				{
					xanth_log(LOG_LEVEL_USER_MESSAGE,$error);
				}
			}
		}
		
		return new xContentSimple("Create new item (generic)",$form->render(),'','');
	}
	
	
	/**
	 * @access private
	 */
	function _getContentAdminItem()
	{
		if(!xAccessPermission::checkPermission('admin item'))
		{
			return new xContentNotAuthorized();
		}
		
		$items = xItem::find();
		
		$output = 
		'<table class="admin-table">
		<tr><th>Name</th><th>Title</th><th>Type</th><th>Filter name</th><th>Area</th><th>Operations</th></tr>
		';
		foreach($boxes as $box)
		{
			if(!empty($box->m_filterset))
			{
				$filter = xAccessFilterSet::dbLoad($box->m_filterset);
				$filtername = $filter->m_name;
			}
			else
			{
				$filtername = '[No Filter]';
			}
			
			$output .= '<tr><td>' . $box->m_name . '</td><td>' . $box->m_title . '</td><td>'.
			$box->m_type . '</td><td>' . $filtername . '</td><td>' . $box->m_area . '</td><td>Edit</td></tr>';
		}
		$output .= "</table>\n";
		
		return new xContentSimple("Manage box",$output,'','');
	}
};

xModule::registerModule(new xModuleItem());

	
?>
