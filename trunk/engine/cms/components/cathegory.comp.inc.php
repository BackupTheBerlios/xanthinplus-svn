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
		if($path->m_resource === "cathegory" && $path->m_type === NULL && $path->m_action == 'admin')
		{
			return new xPageContentCathegoryAdmin($path);
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
class xPageContentCathegoryView extends xPageContent
{	
	/**
	 * @var xCathegory
	 */
	var $m_cat;
	
	function xPageContentCathegoryView($path,$cat)
	{
		$this->xPageContent($path);
		$this->m_cat = $cat;
	}
	
	/**
	 * Checks that cat exists, checks cathegory and type view permission.
	 * If you inherit the xPageContentCathegoryView clas and override this member, remember
	 * to call the xPageContentCathegoryView::onCheckPreconditions() before your checks.
	 */
	function onCheckPreconditions()
	{
		assert($this->m_cat != NULL);
		
		//check type permission
		if(!xAccessPermission::checkCurrentUserPermission('cathegory',$this->m_cat->m_type,NULL,'view'))
			return new xPageContentNotAuthorized($this->m_path);
		
		if(!empty($this->m_cat->m_parent_cathegory))
		{
			$cathegory = xCathegory::dbLoad($this->m_cat->m_parent_cathegory);
			if(! $cathegory->checkCurrentUserPermissionRecursive('view'))
					return new xPageContentNotAuthorized($this->m_path);
		}
		
		return TRUE;
	}
	
	
	/**
	 * Do nothing. Only asserts cat != NULL and returns true.
	 */
	function onCreate()
	{
		assert($this->m_cat != NULL);
		return true;
	}
}



/**
 *
 */
class xPageContentCathegoryAdmin extends xPageContent
{

	function xPageContentCathegoryAdmin($path)
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
			$out .= "<li><a href=\"".xPath::renderLink($this->m_path->m_lang,'cathegory','admin',$type->m_name) 
				."\">" . $type->m_name . "</a></li>\n";
		}
		
		$out  .= "</ul>\n";
		
		xPageContent::_set("Admin cathegory: choose type",$out,'','');
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
		if($this->m_path->m_id != NULL)
		{
			$cathegory = xCathegory::dbLoad($this->m_path->m_id);
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


?>