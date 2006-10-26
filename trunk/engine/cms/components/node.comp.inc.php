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


	/**
	 * @see xDummyModule::xm_fetchContent()
	 */ 
	function xm_fetchContent($path)
	{
		if($path->m_resource === 'node' && $path->m_action === 'admin' && $path->m_type === NULL)
		{
			return new xPageContentAdminNode($path);
		}
		
		elseif($path->m_resource === 'node' && $path->m_action === 'translate' && $path->m_type === NULL)
		{
			return new xPageContentNodeTranslate($path);
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
xModule::registerDefaultModule(new xModuleNode());



/**
 * 
 */
class xPageContentNodeTranslate extends xPageContent
{	
	function xPageContentNodeTranslate($path)
	{
		$this->xPageContent($path);
	}
	
	/**
	 * Checks that original node exists, that the translation is not already present, checks translation permission.
	 */
	function onCheckPreconditions()
	{
		if(! xNodeI18N::isTranslatable($this->m_path->m_id))
			return new xPageContentError('Cannot translate this node',$this->m_path);
			
		
		if(xNodeI18N::existsTranslation($this->m_path->m_id,$this->m_path->m_lang))
			return new xPageContentError('A translation of this node in this language already exists',$this->m_path);
			
		
		if(!xAccessPermission::checkCurrentUserPermission('node',$this->m_path->m_type,NULL,'translate'))
			return new xPageContentNotAuthorized($this->m_path);
			
		return true;
	}
	
	/**
	 * Do nothing
	 * @abstract
	 */
	function onCreate()
	{
		assert(false);
	}
};



/**
 * 
 */
class xPageContentNodeEdittranslation extends xPageContent
{	
	function xPageContentNodeEdittranslation($path)
	{
		$this->xPageContent($path);
	}
	
	/**
	 * Checks that the translation is  present, checks translation permission.
	 */
	function onCheckPreconditions()
	{
		if(! xNodeI18N::existsTranslation($this->m_path->m_id,$this->m_path->m_lang))
			return new xPageContentError('A translation of this node does exists',$this->m_path);
			
		if(!xAccessPermission::checkCurrentUserPermission('node',$this->m_path->m_type,NULL,'edit_translation'))
			return new xPageContentNotAuthorized($this->m_path);
			
		return true;
	}
	
	/**
	 * Do nothing
	 * @abstract
	 */
	function onCreate()
	{
		assert(false);
	}
};



/**
 * 
 */
class xPageContentNodeDeleteTranslation extends xPageContent
{	
	function xPageContentNodeDeleteTranslation($path)
	{
		$this->xPageContent($path);
	}
	
	/**
	 * Checks that the translation is  present, checks delete translation permission.
	 */
	function onCheckPreconditions()
	{
		if(! xNodeI18N::existsTranslation($this->m_path->m_id,$this->m_path->m_lang))
			return new xPageContentError('A translation of this node does exists',$this->m_path);
			
		if(!xAccessPermission::checkCurrentUserPermission('node',$this->m_path->m_type,NULL,'delete_translation'))
			return new xPageContentNotAuthorized($this->m_path);
			
		return true;
	}
	
	/**
	 * Ask the confirmation and delete node translation.
	 */
	function onCreate()
	{
		//create form
		$form = new xForm($this->m_path->getLink());
		
		//submit buttom
		$form->m_elements[] = new xFormSubmit('submit','Delete');
		
		//submit buttom
		$form->m_elements[] = new xFormSubmit('submit','Cancel');
		
		$ret = $form->validate();
		if(! $ret->isEmpty())
		{
			if(empty($ret->m_errors))
			{
				if($ret->m_valid_data['submit'] === 'Delete')
				{
					if(xNodeI18N::dbDeleteTranslation($this->m_path->m_id,$this->m_path->m_lang))
					{
						xNotifications::add(NOTIFICATION_NOTICE,'Node translation successfully deleted');
					}
					else
					{
						xNotifications::add(NOTIFICATION_ERROR,'Error deleting translation');
					}
					
					$this->_set("Delete node translation",'','','');
					return TRUE;
				}
				else
				{
					$js = new xJavascriptRedirect(xPath::renderLink($this->m_path->m_lang,'node','view',
						$this->m_path->m_type,$this->m_path->m_id),true,0);
					$this->_set("Delete node translation",$js->render(),'','');
					return TRUE;
				}
			}
			else
			{
				foreach($ret->m_errors as $error)
				{
					xNotifications::add(NOTIFICATION_WARNING,$error);
				}
			}
		}

		$out = 'Are you sure do you want to delete this node translation? <br/> 
			Note: If this is the last translation of the node, the node will we deleted at all.<br/>';
		$this->_set("Delete node translation",$out . $form->render(),'','');
		return TRUE;
	}
};



/**
 * 
 */
class xPageContentAdminNode extends xPageContent
{	
	function xPageContentAdminNode($path)
	{
		$this->xPageContent($path);
	}
	
	/**
	 * No checks here
	 */
	function onCheckPreconditions()
	{
		return TRUE;
	}
	
	/**
	 * Do nothing
	 */
	function onCreate()
	{
		$out = 'Choose type:
		<ul>
		';
		$types = xNodeType::findAll();
		foreach($types as $type)
		{
			$out .= "<li><a href=\"".xanth_relative_path($this->m_path->m_lang. '/node/admin/'.$type->m_name)."\">" 
				. $type->m_name . "</a></li>\n";
		}
		
		$out  .= "</ul>\n";
		
		xPageContent::_set("Create node: choose type",$out,'','');
		return true;
	}
};


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
	 * Do nothing. Only asserts node != NULL and returns true.
	 */
	function onCreate()
	{
		assert($this->m_node != NULL);
		return true;
	}

};


?>
