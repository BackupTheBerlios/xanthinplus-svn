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
		if($path->m_resource === 'node' && $path->m_action === 'admin' && $path->m_type === 'page')
		{
			return new xPageContentNodeAdminPage($path);
		}
		
		elseif($path->m_resource === 'node' && $path->m_action === 'view' && $path->m_type === 'page')
		{
			$node = xNodePage::dbLoad($path->m_id,$path->m_lang);
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
		
		elseif($path->m_resource === 'node' && $path->m_type === 'page' && $path->m_action === 'translate'
			&& $path->m_id !== NULL)
		{
			return new xPageContentNodeTranslatePage($path);
		}
		
		elseif($path->m_resource === 'node' && $path->m_type === 'page' && $path->m_action === 'edit_translation'
			&& $path->m_id !== NULL)
		{
			return new xPageContentNodeEdittranslationPage($path);
		}
		
		elseif($path->m_resource === 'node' && $path->m_type === 'page' && $path->m_action === 'delete_translation'
			&& $path->m_id !== NULL)
		{
			return new xPageContentNodeDeleteTranslation($path);
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
class xPageContentNodeAdminPage extends xPageContent
{	
	function xPageContentAdminNodePage($path)
	{
		$this->xPageContent($path);
	}
	
	/**
	 * Check node admin type permission
	 */
	function onCheckPreconditions()
	{
		if(!xAccessPermission::checkCurrentUserPermission('node',$this->m_path->m_type,NULL,'admin'))
			return new xPageContentNotAuthorized($this->m_path);
		
		return true;
	}
	
	/**
	 * @access private
	 */
	function _groupNodes($nodes)
	{
		$out = array();
		foreach($nodes as $node)
		{
			$out[$node->m_id][$node->m_lang] = $node;
		}
		
		return $out;
	}
	
	/**
	 * 
	 */
	function onCreate()
	{
		$nodes = xNodePage::find(NULL,NULL,NULL,NULL);
		$nodes = $this->_groupNodes($nodes);
		$out = '<a href="'.xanth_relative_path($this->m_path->m_lang. '/node/create/page').'">Create new node page</a><br/><br/>';
		$out .= "<div class = 'admin'><table>\n";
		$out .= "<tr><th>ID</th><th>Title</th><th>In your lang?</th><th>Translated in</th><th>Translate in</th></tr>\n";
		$langs = xLanguage::findNames();
		foreach($nodes as $id => $node_array)
		{
			$node = NULL;
			
			if(isset($node_array[$this->m_path->m_lang])) 				//select current language node
			{
				$node = $node_array[$this->m_path->m_lang];
			}
			elseif(isset($node_array[xSettings::get('default_lang')]))	//select default language node
			{
				$node = $node_array[xSettings::get('default_lang')];
			}
			else														//select first found language node
			{
				$node = reset($node_array);
			}
			$error = '';
			$out .= '<tr><td>'.$id.'</td><td><a href="'.
				xPath::renderLink($node->m_lang,'node','view',$node->m_type,$node->m_id) . '">'.
				xContentFilterController::applyFilter('notags',$node->m_title,$error) . '</a></td><td>';
			if($node->m_lang == $this->m_path->m_lang)
				$out .= 'Yes';
			else			
				$out .= 'No';
			
			$out .= '</td><td>';
			foreach($node_array as $lang => $node_ignore)
			{
				$out .= $lang . '  ';
			}
			$out .= '</td><td>';
			
			
			foreach($langs as $lang)
			{
				if(!array_key_exists($lang, $node_array))
				{
					$out .= '<a href="'. 
						xanth_relative_path($lang . '/node/translate/'. $node->m_type . '/' . $id). 
						'">' . $lang . '</a>';
				}
			}
		}
		
		$out  .= "</table></div>\n";
		
		xPageContent::_set("Admin node page",$out,'','');
		return true;
	}
};



/**
 * 
 */
class xPageContentNodeTranslatePage extends xPageContentNodeTranslate
{	
	
	function xPageContentNodeTranslatePage($path)
	{
		$this->xPageContentNodeTranslate($path);
	}
	
	/**
	 * Checks if type is node page
	 */
	function onCheckPreconditions()
	{
		$ret = xPageContentNodeTranslate::onCheckPreconditions();
		if($ret !== TRUE)
			return $ret;
		
		if(! xNodePage::isNodePage($this->m_path->m_id))
			return new xPageContentError('The node is not of type page',$this->m_path);
			
		return true;
	}
	
	
	/**
	 * Create and outputs node creation form
	 */
	function onCreate()
	{
		$node = xNodePage::dbLoad($this->m_path->m_id,xSettings::get('default_lang'));
		
		//create form
		$form = new xForm(xanth_relative_path($this->m_path->m_full_path));
		
		//item title
		$form->m_elements[] = new xFormElementTextField('title','Title','','',true,new xInputValidatorText(256));
		
		//item body
		$form->m_elements[] = new xFormElementTextArea('body','Body ('.$node->m_content_filter.' filter)','','',true,
			new xInputValidatorApplyContentFilter(0,$node->m_content_filter));
		
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
				$node = new xNodePage($node->m_id,$node->m_type,$node->m_author,
					$node->m_content_filter,$ret->m_valid_data['title'],$ret->m_valid_data['body'],
					$this->m_path->m_lang,xUser::getLoggedinUsername(),$node->m_parent_cathegories,$node->m_creation_time,$node->m_edit_time,
					$node->m_published,$node->m_sticky,$node->m_accept_replies,
					$node->m_approved,$ret->m_valid_data['meta_description'],$ret->m_valid_data['meta_keywords']);
				
				if($node->dbInsertTranslation())
				{
					xNotifications::add(NOTIFICATION_NOTICE,'Node successfully translated');
				}
				else
				{
					xNotifications::add(NOTIFICATION_ERROR,'Error inserting translation');
				}
				
				$this->_set("Translate node page",'','','');
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

		$this->_set("Translate node page",$form->render(),'','');
		return TRUE;
	}
};



/**
 * 
 */
class xPageContentNodeEdittranslationPage extends xPageContentNodeEdittranslation
{	
	
	function xPageContentNodeEdittranslationPage($path)
	{
		$this->xPageContentNodeEdittranslation($path);
	}
	
	/**
	 * Checks if type is node page
	 */
	function onCheckPreconditions()
	{
		$ret = xPageContentNodeEdittranslation::onCheckPreconditions();
		if($ret !== TRUE)
			return $ret;
		
		if(! xNodePage::isNodePage($this->m_path->m_id))
			return new xPageContentError('The node is not of type page',$this->m_path);
			
		return true;
	}
	
	
	/**
	 * Create and outputs node creation form
	 */
	function onCreate()
	{
		$node = xNodePage::dbLoad($this->m_path->m_id,$this->m_path->m_lang);
		
		//create form
		$form = new xForm(xanth_relative_path($this->m_path->m_full_path));
		
		//item title
		$form->m_elements[] = new xFormElementTextField('title','Title','',$node->m_title,true,
			new xInputValidatorText(256));
		
		//item body
		$form->m_elements[] = new xFormElementTextArea('body','Body ('.$node->m_content_filter.' filter)','',
			$node->m_content,true,new xInputValidatorApplyContentFilter(0,$node->m_content_filter));
		
		$group = new xFormGroup('Metadata');
		//item description
		$group->m_elements[] = new xFormElementTextField('meta_description','Description','',$node->m_meta_description,
			false,new xInputValidatorText(128));
		//item keywords
		$group->m_elements[] = new xFormElementTextField('meta_keywords','Keywords','',$node->m_meta_keywords,
			false,new xInputValidatorText(128));
		$form->m_elements[] = $group;
		
		//submit buttom
		$form->m_elements[] = new xFormSubmit('submit','Edit');
		
		$ret = $form->validate();
		if(! $ret->isEmpty())
		{
			if(empty($ret->m_errors))
			{
				$node = new xNodePage($node->m_id,$node->m_type,$node->m_author,
					$node->m_content_filter,$ret->m_valid_data['title'],$ret->m_valid_data['body'],
					$this->m_path->m_lang,xUser::getLoggedinUsername(),$node->m_parent_cathegories,$node->m_creation_time,$node->m_edit_time,
					$node->m_published,$node->m_sticky,$node->m_accept_replies,
					$node->m_approved,$ret->m_valid_data['meta_description'],$ret->m_valid_data['meta_keywords']);
				
				if($node->dbUpdateTranslation())
				{
					xNotifications::add(NOTIFICATION_NOTICE,'Node translation successfully updated');
				}
				else
				{
					xNotifications::add(NOTIFICATION_ERROR,'Error during translation update');
				}
				
				$this->_set("Edit node page translation",'','','');
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

		$this->_set("Edit node page translation",$form->render(),'','');
		return TRUE;
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
		if($this->m_path->m_id == NULL)
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
				if($this->m_path->m_id != NULL)
					$cathegories[] = $this->m_path->m_id;
				else
					$cathegories = $ret->m_valid_data['cathegory'];
				
				$node = new xNodePage(-1,$this->m_path->m_type,xUser::getLoggedinUsername(),
					$ret->m_valid_data['filter'],$ret->m_valid_data['title'],$ret->m_valid_data['body'],
					$this->m_path->m_lang,xUser::getLoggedinUsername(),$cathegories,NULL,NULL,
					$ret->m_valid_data['published'],$ret->m_valid_data['sticky'],$ret->m_valid_data['accept_replies'],
					$ret->m_valid_data['approved'],$ret->m_valid_data['meta_description'],
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
		
		
		$error = '';
		$title = xContentFilterController::applyFilter('notags',$this->m_node->m_title,$error);
		
		xPageContent::_set($title,$this->m_node->render(),$this->m_node->m_meta_description,
			$this->m_node->m_meta_keywords);
		
		return true;
	}
};



?>
