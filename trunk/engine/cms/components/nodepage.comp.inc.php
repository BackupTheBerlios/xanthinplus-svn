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
class xModuleNodePage extends xModule
{
	function xModuleNodePage()
	{
		$this->xModule();
	}


	/**
	 * @see xDummyModule::xm_fetchContent()
	 */ 
	function xm_fetchContent($path)
	{
		if($path->m_resource === 'admin/node' && $path->m_action === NULL && $path->m_type === 'page')
		{
			return new xPageContentAdminNodePage($path);
		}
		
		elseif($path->m_resource === 'node' && $path->m_action === 'view' && $path->m_type === 'page')
		{
			$node = xNodePage::dbLoad($path->m_id);
			if($node === NULL)
			{
				return new xPageContentNotFound($path);
			}
			return new xPageContentNodePageView($path,$node);
		}
		
		elseif($path->m_resource === 'node' && $path->m_type === 'page' && $path->m_action === 'create')
		{
			return new xPageContentNodePageCreate($path);
		}
		
		return NULL;
	}
	
	
	/**
	 * @see xDummyModule::xm_fetchContent()
	 */
	function xm_fetchPermissionDescriptors()
	{
		$descr = array();
		
		//extract types
		$types = xNodeType::findAll();
		foreach($types as $type)
		{
			$descr[] = new xAccessPermissionDescriptor('node',$type->m_name,NULL,'view','View node '.$type->m_name);
		}
		
		foreach($types as $type)
		{
			$descr[] = new xAccessPermissionDescriptor('node',$type->m_name,NULL,'create','Create node '.$type->m_name);
		}
		
		//todo insert permission for cathegory in cat.comp
		
		return $descr;
	}
	
};
xModule::registerDefaultModule(new xModuleNodePage());




/**
 * 
 */
class xPageContentAdminNodePage extends xPageContent
{	
	function xPageContentAdminNodePage($path)
	{
		$this->xPageContent($path);
	}
	
	/**
	 * No checks here
	 */
	function onCheckPreconditions()
	{
		if(!xAccessPermission::checkCurrentUserPermission('node',NULL,NULL,'admin'))
			return new xPageContentNotAuthorized($this->m_path);
	}
	
	
	/**
	 * Do nothing
	 */
	function onCreate()
	{
		$types = xNodePage::find();
		
		$out = "<table>\n";
		foreach($types as $type)
		{
			$out .= "<li><a href=\"".xanth_relative_path('node/create/'.$type->m_name)."\">" . $type->m_name . "</a></li>\n";
		}
		
		$out  .= "</ul>\n";
		
		xPageContent::_set("Create node: choose type",$out,'','');
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
			$cathegories = xCathegoryI18N::find($this->m_path->m_type,NULL,NULL,'en');
			$options = array();
			foreach($cathegories as $cathegory)
			{
				$options[$cathegory->m_name] = $cathegory->m_id;
			}
			
			$form->m_elements[] = new xFormElementOptions('cathegory','Cathegories','','',$options,TRUE,TRUE,
				new xCreateIntoCathegoryValidator($this->m_path->m_type));
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
					
				$node = new xNodePage(-1,$ret->m_valid_data['title'],$this->m_path->m_type,xUser::getLoggedinUsername(),
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
