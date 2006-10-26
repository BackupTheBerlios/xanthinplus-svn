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
 * A module for box
 */
class xModuleBox extends xModule
{
	function xModuleBox()
	{
		xModule::xModule();
	}


	/**
	 * @see xDummyModule::xm_fetchContent()
	 */ 
	function xm_fetchContent($path)
	{
		if($path->m_resource === 'box' && $path->m_action === 'admin' && $path->m_type === NULL)
		{
			//let user choose type
		}
		elseif($path->m_resource === 'box' && $path->m_action === 'create' && $path->m_type === 'custom')
		{
			return new xPageContentAdminBoxCreateCustom($path);
		}
		
		return NULL;
	}
	
	/**
	 * @see xDummyModule::xm_fetchPermissionDescriptors()
	 */ 
	function xm_fetchPermissionDescriptors()
	{
		$descrs = array();
		$descr[] = new xAccessPermissionDescriptor('admin/box',NULL,NULL,'create','Create a custom box of any type');
		return $descrs;
	}
};

xModule::registerDefaultModule(new xModuleBox());





/**
 *
 */
class xPageContentAdminBoxCreateCustom extends xPageContent
{

	function xPageContentAdminBoxCreateCustom($path)
	{
		xPageContent::xPageContent($path);
	}
	
	// DOCS INHERITHED  ========================================================
	function onCheckPreconditions()
	{
		if(!xAccessPermission::checkCurrentUserPermission('admin/box',$this->m_path->m_type,NULL,'create'))
			return new xPageContentNotAuthorized($this->m_path);
			
		return TRUE;
	}
	
	// DOCS INHERITHED  ========================================================
	function onCreate()
	{
		//create form
		$form = new xForm(xanth_relative_path($this->m_path->m_full_path));
		
		//box name
		$form->m_elements[] = new xFormElementTextField('name','Name','','',true,new xInputValidatorTextNameId(32));
		
		//box title
		$form->m_elements[] = new xFormElementTextField('title','Title','','',true,new xInputValidatorText(128));
		
		//box content
		$form->m_elements[] = new xFormElementTextArea('content','Content','','',true,
			new xDynamicInputValidatorApplyContentFilter(0,'filter'));
			
			
		//box content filter
		$filters = xContentFilterController::getCurrentUserAvailableFilters();
		$content_filter_radio_group = new xFormRadioGroup('Content filter');
		foreach($filters as $filter)
		{
			$content_filter_radio_group->m_elements[] = new xFormElementRadio('filter',$filter['name'],
				$filter['description'],$filter['name'],false,TRUE,new xInputValidatorContentFilter(64));
		}
		$form->m_elements[] = $content_filter_radio_group;
		
		//show filter type
		$show_filter_radio = new xFormRadioGroup('Show filter type');
		$show_filter_radio->m_elements[] = new xFormElementRadio('show_filter_type','Inclusive filter',
				'',XANTH_SHOW_FILTER_INCLUSIVE,false,TRUE,new xInputValidatorInteger(1,3));
		$show_filter_radio->m_elements[] = new xFormElementRadio('show_filter_type','Exclusive filter',
				'',XANTH_SHOW_FILTER_EXCLUSIVE,false,TRUE,new xInputValidatorInteger(1,3));
		$show_filter_radio->m_elements[] = new xFormElementRadio('show_filter_type','PHP filter',
				'',XANTH_SHOW_FILTER_PHP,false,TRUE,new xInputValidatorInteger(1,3));
		$form->m_elements[] = $show_filter_radio;
		
		//show filter
		$form->m_elements[] = new xFormElementTextArea('show_filter','Show filter','','',false,
			new xInputValidatorText());
		
		//todo weight
		
		//submit buttom
		$form->m_elements[] = new xFormSubmit('submit','Create');
		
		$ret = $form->validate();
		if(! $ret->isEmpty())
		{
			if(empty($ret->m_errors))
			{
				$node = new xBoxCustom($ret->m_valid_data['name'],'custom',0,
					new xShowFilter($ret->m_valid_data['show_filter_type'],$ret->m_valid_data['show_filter']),
					$ret->m_valid_data['title'],$ret->m_valid_data['content'],$ret->m_valid_data['filter']);
				
				if($node->insert())
				{
					xNotifications::add(NOTIFICATION_NOTICE,'New box successfully created');
				}
				else
				{
					xNotifications::add(NOTIFICATION_ERROR,'Error: box was not created');
				}
				
				$this->_set("Create new box",'','','');
				return TRUE;
			}
			else
			{
				foreach($ret->m_errors as $error)
				{
					xNotifications::add(NOTIFICATION_WARNING,$error);
				}
			}
		}

		$this->_set("Create new box page",$form->render(),'','');
		return TRUE;
	}
}


	
?>
