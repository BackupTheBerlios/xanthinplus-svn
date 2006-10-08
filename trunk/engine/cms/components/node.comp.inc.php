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
	function xm_fetchContent($path)
	{
		if($path->m_resource === 'node' && $path->m_action === 'view' && $path->m_type === NULL)
		{
			//get node type
			$type = xNode::getNodeTypeById($path->m_id);
			if($type == NULL)
				return NULL;
			
			$path->m_type = $type;
			return xModule::callWithSingleResult1('xm_fetchContent',$path);
		}
		elseif($path->m_resource === 'node' && $path->m_action === 'view' && $path->m_type === 'page')
		{
			$node = xNodePage::dbLoad($path->m_id);
			if($node === NULL)
			{
				return xPageContentNotFound($path);
			}
			return new xPageContentNodePageView($path,$node);
		}
		elseif($path->m_resource === 'node' && $path->m_action === 'create' && $path->m_action === NULL)
		{
			//todo
			return new xPageContentNodeCreateChooseType($path);
		}
		elseif($path->m_resource === 'node' && $path->m_type === 'page' && $path->m_action === 'create')
		{
			return new xPageContentNodePageCreate($path);
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
	function xPageContentNodeCreate($path)
	{
		$this->xPageContent($path);
	}
	
	/**
	 * Checks cathegory and type create permission.
	 * If you inherit the xPageContentNodeView clas and override this member, remember
	 * to call the xPageContentNodeCreate::onCheckPreconditions() before your checks.
	 */
	function onCheckPreconditions()
	{
		assert($this->m_path->m_type != NULL);
		
		//check type permission
		if(!xAccessPermission::checkCurrentUserPermission('node',$this->m_path->m_type,NULL,'create'))
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
			if(! $cathegory->checkCurrentUserPermissionRecursive('create_node_inside'))
				return new xPageContentNotAuthorized($this->m_path);
		}
		
		
		
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
			
		//check cathegory permission
		foreach($this->m_node->m_parent_cathegories as $cathegory)
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
		$title = xContentFilterController::applyFilter('notags',$this->m_node->m_title,$error);
		
		xPageContent::_set($title,$this->m_node->render(),'','');
		return true;
	}

};





/**
 * 
 */
class xPageContentNodePageCreate extends xPageContentNodeCreate
{	
	
	function xPageContentNodePageCreate($path)
	{
		$this->xPageContentNodeCreate($path);
	}
	
	/**
	 * Nothing else to check here in addition to standard checks
	 */
	function onCheckPreconditions()
	{
		return xPageContentNodeCreate::onCheckPreconditions();
	}
	
	
	/**
	 * Create and outputs node creation form
	 */
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
			
			$form->m_elements[] = new xFormElementOptions('cathegory','Cathegories','','',$options,TRUE,TRUE,
				new xCreateNodeIntoCathegoryValidator($this->m_path->m_type));
		}
		
		
		//item title
		$form->m_elements[] = new xFormElementTextField('title','Title','','',true,new xInputValidatorText(256));
		
		
		
		//item body
		$form->m_elements[] = new xFormElementTextArea('body','Body','','',true,
			new xDynamicInputValidatorApplyContentFilter(0,'filter'));
			
			
			
			
		//item filter
		$filters = xContentFilterController::getCurrentUserAvailableFilters();
		$content_filter_radio_group = new xFormRadioGroup('Content filter');
		foreach($filters as $filter)
		{
			$content_filter_radio_group->m_elements[] = new xFormElementRadio('filter',$filter['name'],
				$filter['description'],$filter['name'],false,TRUE,new xInputValidatorContentFilter(64));
		}
		$form->m_elements[] = $content_filter_radio_group;
		
		
		
		$group = new xFormGroup('Parameters');
		//item published
		$group->m_elements[] = new xFormElementCheckbox('published','Published','',1,FALSE,FALSE,new xInputValidatorInteger());
		//item approved
		$group->m_elements[] = new xFormElementCheckbox('approved','Approved','',1,FALSE,FALSE,new xInputValidatorInteger());
		//item sticky
		$group->m_elements[] = new xFormElementCheckbox('sticky','Sticky','',1,FALSE,FALSE,new xInputValidatorInteger());
		//item accept replies
		$group->m_elements[] = new xFormElementCheckbox('accept_replies','Accept Replies','',1,FALSE,FALSE,new xInputValidatorInteger());
		$form->m_elements[] = $group;
		
		$group = new xFormGroup('Metadata');
		//item description
		$group->m_elements[] = new xFormElementTextField('meta_description','Description','','',false,new xInputValidatorText(128));
		//item keywords
		$group->m_elements[] = new xFormElementTextField('meta_keywords','Keywords','','',false,new xInputValidatorText(128));
		$form->m_elements[] = $group;
		
		//submit buttom
		$form->m_elements[] = new xFormSubmit('submit','Create');
		
		$ret = $form->validate();
		if(! $ret->isEmpty())
		{
			if(empty($ret->m_errors))
			{
				$cathegories = array();
				if($this->m_path->m_parent_cathegory != NULL)
					$cathegories[] = $this->m_path->m_parent_cathegory;
				else
					$cathegories = $ret->m_valid_data['cathegory'];
					
				$node = new xNodePage(-1,$ret->m_valid_data['title'],NULL,$this->m_path->m_type,xUser::getLoggedinUsername(),
					$ret->m_valid_data['body'],$ret->m_valid_data['filter'],$cathegories,NULL,NULL,
					$ret->m_valid_data['published'],$ret->m_valid_data['sticky'],$ret->m_valid_data['accept_replies'],
					$ret->m_valid_data['approved'],0,$ret->m_valid_data['meta_description'],
					$ret->m_valid_data['meta_keywords']);
				
				if($node->dbInsert())
				{
					xNotifications::add(NOTIFICATION_NOTICE,'New node successfully created');
				}
				else
				{
					xNotifications::add(NOTIFICATION_ERROR,'Error: node was not created');
				}
				
				$this->_set("Create new node page",'','','');
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

		$this->_set("Create new node page",$form->render(),'','');
		return TRUE;
	}
};



/**
 * 
 */
class xPageContentNodePageView extends xPageContentNodeView
{	
	function xPageContentNodePageView($path,$node)
	{
		xPageContentNodeView::xPageContentNodeView($path,$node);
	}
	
	/**
	 * Only basic checks.No additional checks here.
	 */
	function onCheckPreconditions()
	{
		//todo check approved,sticky,published ecc...
		return xPageContentNodeView::onCheckPreconditions();
	}
	
	
	/**
	 * Fill this object with node properties by calling xNode->render(). Only metadata are not filled-id, 
	 * so override this funciton in your node type implementation.
	 */
	function onCreate()
	{
		$res = xPageContentNodeView::onCreate();
		if($res !== TRUE)
			return $res;
		
		$this->m_meta_description = $this->m_node->m_meta_description;
		$this->m_meta_keywords = $this->m_node->m_meta_keywords;
		
		return true;
	}
};



?>
