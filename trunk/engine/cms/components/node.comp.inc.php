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
* Module responsible of user management
*/
class xModuleNode extends xModule
{
	function xModuleNode()
	{
		$this->xModule();
	}


	// DOCS INHERITHED  ========================================================
	function xm_fetchContent($resource,$action,$path)
	{
		if($resource === 'node' && $action === 'create')
		{
			return new xPageContentNodeCreate($path);
		}
		elseif($resource === 'node' && $action === 'view')
		{
			//get node type
			$type = xNode::getNodeTypeById($path->m_resource_id);
			if($type == NULL)
				return NULL;
			
			return xModule::callWithSingleResult3('xm_fetchContent','node/'.$type,$action,$path);
		}
		
		return NULL;
	}
};
xModule::registerDefaultModule(new xModuleNode());




/**
 * 
 */
class xPageContentNodeCreate extends xPageContent
{	
	/**
	 * @var string
	 */
	var $m_type;
	
	/**
	 * @var string
	 */
	var $m_parent_cathegory;
	
	function xPageContentNodeCreate($path,$type,$parent_cathegory)
	{
		$this->xPageContent($path);
		$this->m_type = $type;
		$this->m_parent_cathegory = $parent_cathegory;
	}
	
	/**
	 * checks cathegory and type view permission.
	 * If you inherit the xPageContentNodeView clas and override this member, remember
	 * to call the xPageContentNodeCreate::onCheckPreconditions() before your checks.
	 */
	function onCheckPreconditions()
	{
		assert($this->m_type != NULL);
		
		//check type permission
		if(!xAccessPermission::checkCurrentUserPermission('node',$this->m_type,NULL,'create'))
			return new xPageContentNotAuthorized($this->m_path);
		
		if($this->m_parent_cathegory != NULL)
		{
			$cathegory = xCathegory::dbLoad($this->m_parent_cathegory);
			if($cathegory == NULL)
				return new xPageContentNotFound($this->m_path);
		}
		
		//check cathegories permission
		if(! $cathegory->checkCurrentUserPermissionRecursive('create_node_inside'))
				return new xPageContentNotAuthorized($this->m_path);
		
		return TRUE;
	}
	
	
	/**
	 * Do nothing
	 */
	function onCreate()
	{
		return new xPageContentNotFound($this->m_path);
	}
};


/**
 * 
 */
class xPageContentNodeView extends xPageContent
{	
	/**
	 * @var xNode
	 */
	var $m_node;
	
	function xPageContentNodeView($path,$node)
	{
		$this->xPageContent($path);
		$this->m_node = $node;
	}
	
	/**
	 * Checks that node exists, checks cathegory and type view permission.
	 * If you inherit the xPageContentNodeView clas and override this member, remember
	 * to call the xPageContentNodeView::onCheckPreconditions() before your checks.
	 */
	function onCheckPreconditions()
	{
		assert($this->m_node != NULL);
		
		//check type permission
		if(!xAccessPermission::checkCurrentUserPermission('node',$this->m_node->m_type,NULL,'view'))
			return new xPageContentNotAuthorized($this->m_path);
		
		//check cathegories permission
		foreach($this->m_node->m_cathegories as $cathegory)
		{
			if(! $cathegory->checkCurrentUserPermissionRecursive('view'))
				return new xPageContentNotAuthorized($this->m_path);
		}
		
		return TRUE;
	}
	
	
	/**
	 * Fill this object with node properties by calling xNode->render(). Only metadata are not filled-id, 
	 * so override this funciton in your node type implementation.
	 */
	function onCreate()
	{
		assert($this->m_node != NULL);
		
		$error = '';
		$title = xContentFilterController::applyFilter('notags',$m_node->m_title,&$error);
		
		xPageContent::_set($title,$item->render(),'','');
		return true;
	}
	

};
	
?>
