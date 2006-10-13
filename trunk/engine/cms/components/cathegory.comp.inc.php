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
* Cathegory module
*/
class xModuleCathegory extends xModule
{
	function xModuleCathegory()
	{
		$this->xModule();
	}


	/**
	 * @see xDummyModule::xm_fetchContent()
	 */ 
	function xm_fetchContent($path)
	{
		if($path->m_resource === "cathegory" && $path->m_type == NULL && $path->m_action == 'create')
		{
			return new xPageContentCathegoryCreateChooseType($path);
		}
		elseif($path->m_resource === "cathegory" && $path->m_type == 'page' && $path->m_action == 'create')
		{
			return new xPageContentCathegoryCreatePage($path);
		}
		
		return NULL;
	}
	
	/**
	 * @see xDummyModule::xm_fetchCathegory()
	 */
	function xm_fetchCathegory($cat_id,$cat_type)
	{
		switch($cat_type)
		{
			case 'page':
				return xCathegory::dbLoad($cat_id);
		}
		
		return NULL;
	}
	
	
	/**
	 * @see xDummyModule::xm_fetchPermissionDescriptors()
	 */ 
	function xm_fetchPermissionDescriptors()
	{
		$descrs = array();
		
		$types = xNodeType::findAll();
		foreach($types as $type)
		{
			$descrs[] = new xAccessPermissionDescriptor('cathegory',$type->m_name,NULL,'view',
				'View cathegory '.$type->m_name);
		}
		
		foreach($types as $type)
		{
			$descrs[] = new xAccessPermissionDescriptor('cathegory',$type->m_name,NULL,'create',
				'Create cathegory '.$type->m_name);
		}
		
		return $descrs;
	}
	
};

xModule::registerDefaultModule(new xModuleCathegory());




/**
 *
 */
class xPageContentCathegoryCreateChooseType extends xPageContent
{

	function xPageContentCathegoryCreateChooseType($path)
	{
		xPageContent::xPageContent($path);
	}
	
	// DOCS INHERITHED  ========================================================
	function onCheckPreconditions()
	{
		return TRUE;
	}
	
	// DOCS INHERITHED  ========================================================
	function onCreate()
	{
		$types = xNodeType::findAll();
		
		$out = "Choose type:\n <ul>\n";
		foreach($types as $type)
		{
			$out .= "<li><a href=\"".xanth_relative_path('cathegory/create/'.$type->m_name)."\">" . $type->m_name . "</a></li>\n";
		}
		
		$out  .= "</ul>\n";
		
		xPageContent::_set("Create cathegory: choose type",$out,'','');
		return true;
	}
}


/**
 * Base class for all cathegory creation pages
 */
class xPageContentCathegoryCreate extends xPageContent
{

	function xPageContentCathegoryCreate($path)
	{
		xPageContent::xPageContent($path);
	}
	
	/**
	 * Checks parent cathegory and type create permission.
	 * If you inherit the xPageContentCathegoryCreate class and override this member, remember
	 * to call the xPageContentNodeCreate::onCheckPreconditions() before your checks.
	 */
	function onCheckPreconditions()
	{
		if(!xAccessPermission::checkCurrentUserPermission('cathegory',$this->m_path->m_type,NULL,'create'))
			return new xPageContentNotAuthorized($this->m_path);
			
		$cathegory = NULL;
		if($this->m_path->m_parent_cathegory != NULL)
		{
			$cathegory = xCathegory::dbLoad($this->m_path->m_parent_cathegory);
			if($cathegory == NULL)
				return new xPageContentNotFound($this->m_path);
			
			//check for matching node type and cathegory type
			if($this->m_path->m_type !== $cathegory->m_type)
				return new xPageContentError($this->m_path,'Node type and parent cathegory type does not match');
			
			//check cathegories permission
			if(! $cathegory->checkCurrentUserPermissionRecursive('create_inside'))
				return new xPageContentNotAuthorized($this->m_path);
		}
		
		return TRUE;
	}
	
	/**
	 * @abstract
	 */
	function onCreate()
	{
		assert(false);
	}
}




/**
 *
 */
class xPageContentCathegoryCreatePage extends xPageContentCathegoryCreate
{

	function xPageContentCathegoryCreatePage($path)
	{
		xPageContentCathegoryCreate::xPageContentCathegoryCreate($path);
	}
	
	// DOCS INHERITHED  ========================================================
	function onCreate()
	{
		//create form
		$form = new xForm(xanth_relative_path($this->m_path->m_full_path));
		
		//no cathegory in path so let user choose according to its permissions
		if($this->m_path->m_parent_cathegory == NULL)
		{
			$cathegories = xCathegory::find(NULL,$this->m_path->m_type);
			
			$options = array();
			foreach($cathegories as $cathegory)
			{
				$options[$cathegory->m_name] = $cathegory->m_id;
			}
			
			$form->m_elements[] = new xFormElementOptions('parent_cathegory','Parent cathegory','','',$options,FALSE,
				TRUE,new xCreateIntoCathegoryValidator($this->m_path->m_type));
		}
		
		//cat name
		$form->m_elements[] = new xFormElementTextField('name','Unique Name','','',true,new xInputValidatorTextNameId(32));
		
		//cat title
		$form->m_elements[] = new xFormElementTextField('title','Title','','',true,new xInputValidatorText(128));
		
		//cat description
		$form->m_elements[] = new xFormElementTextArea('description','Description','','',false,
			new xInputValidatorText());
			
		//submit buttom
		$form->m_elements[] = new xFormSubmit('submit','Create');
		
		$ret = $form->validate();
		if(! $ret->isEmpty())
		{
			if(empty($ret->m_errors))
			{
				$cathegory = array();
				if($this->m_path->m_parent_cathegory != NULL)
					$cathegory = $this->m_path->m_parent_cathegory;
				else
					$cathegory = $ret->m_valid_data['parent_cathegory'];
					
				$cat = new xCathegory(-1,$ret->m_valid_data['title'],$ret->m_valid_data['name'],
					'page',$ret->m_valid_data['description'],$cathegory);
				
				if($cat->dbInsert())
				{
					xNotifications::add(NOTIFICATION_NOTICE,'New cathegory successfully created');
				}
				else
				{
					xNotifications::add(NOTIFICATION_ERROR,'Error: cathegory was not created');
				}
				
				$this->_set("Create new cathegory page",'','','');
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

		$this->_set("Create new cathegory page",$form->render(),'','');
		return TRUE;
	}
}
	
?>
