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
 * A node and cathegory type
 */
class xNodeType
{
	/**
	 * @var string
	 * @access public
	 */
	var $m_name;
	
	/**
	 * @var string
	 * @access public
	 */
	var $m_description;
	
	/**
	 *
	 */
	function xNodeType($name,$description)
	{
		$this->m_name = $name;
		$this->m_description = $description;
	}
	
	
	/** 
	 * Inserts this into db
	 *
	 * @return bool FALSE on error
	 */
	function insert()
	{
		$this->m_id = xNodeTypeDAO::insert($this);
		
		return $this->m_id;
	}
	
	/** 
	 * Delete this from db
	 *
	 * @return bool FALSE on error
	 */
	function delete()
	{
		return xNodeTypeDAO::delete($this->m_name);
	}
	
	
	/** 
	 * Delete a node type from db using its name
	 *
	 * @param int $typename
	 * @return bool FALSE on error
	 * @static
	 */
	function deleteByName($typename)
	{
		return xNodeTypeDAO::delete($typename);
	}
	
	/**
	 * Update this in db
	 *
	 * @return bool FALSE on error
	 */
	function update()
	{
		return xNodeTypeDAO::update($this);
	}
	
	/**
	 * Retrieve a specific item type from db
	 *
	 * @return xItemType
	 * @static
	 */
	function load($typename)
	{
		return xNodeTypeDAO::load($typename);
	}
	
	/**
	 * Retrieves all node types.
	 *
	 * @return array(xNodeType)
	 * @static
	 */
	function findAll()
	{
		return xNodeTypeDAO::findAll();
	}
};


//###########################################################################
//###########################################################################
//###########################################################################

/**
 * Represent a node in the CMS.
 */
class xNode extends xElement
{
	/**
	 * @var int
	 * @access public
	 */
	var $m_id;
	
	/**
	 * @var string
	 * @access public
	 */
	var $m_type;
	
	/**
	 * @var string
	 * @access public
	 */
	var $m_author;
	
	/**
	 * @var string
	 * @access public
	 */
	var $m_content_filter;
	
	/**
	 * @var timestamp
	 * @access public
	 */
	var $m_creation_time;

	/**
	 * @var timestamp
	 * @access public
	 */
	var $m_edit_time;
	
	/**
	 * @var array(xCathegory)
	 * @access public
	 */
	var $m_parent_cathegories;

	/**
	 *
	 * @param array(mixed) $parent_cathegories An array of xCathegory objects 
	 * or an array of cathegories ids
	 */
	function xNode($id,$type,$author,$content_filter,
		$parent_cathegories = array(),$creation_time = NULL,$edit_time = NULL)
	{
		$this->xElement();
		
		$this->m_id = (int) $id;
		$this->m_type = $type;
		$this->m_author = $author;
		$this->m_content_filter = $content_filter;
		$this->m_creation_time = $creation_time;
		$this->m_edit_time = $edit_time;
		
		if(count($parent_cathegories) > 0)
		{
			if(is_numeric($parent_cathegories[0]))
			{
				foreach($parent_cathegories as $parent_cat)
				{
					$tmp = xCathegory::load($parent_cat);
					if($tmp != NULL)
						$this->m_parent_cathegories[] = $tmp;
				}
			}
			else
				$this->m_parent_cathegories = $parent_cathegories;
		}
		else
			$this->m_parent_cathegories =  array();
	}
	
	/**
	 * @abstract
	 */
	function renderBrief()
	{
		assert(false);
	}
	
	/** 
	 * Delete a node from db using its id
	 *
	 * @param int $id
	 * @return bool FALSE on error
	 * @static
	 */
	function deleteById($id)
	{
		return xNodeDAO::delete($id);
	}
	
	
	/**
	 * @return string NULL on error
	 */
	function getNodeTypeById($id)
	{
		return xNodeDAO::getNodeTypeById($id);
	}
	
	
	function find($order = array(),$limit = array(),$id = NULL,$type = NULL,
		$author = NULL,$parent_cat = NULL)
	{
		return xNodeDAO::find($id,$type,$author,$parent_cat);
	}
	
	/**
	 * @abstract
	 * @return array(xOperation)
	 */
	function getOperations()
	{
		assert(false);
	}
	
	/**
	 * @static
	 */
	function getNodeTypeClass($node_type)
	{
		$res = xModule::invoke('xm_fetchNodeTypeClassName',array($node_type));
		if(!$res->isError())
			return $res->m_value;
		
		return NULL;
	}
	
	
	/**
	 * 
	 */
	function loadCathegories()
	{
		$this->m_parent_cathegories = 
			xCathegoryDAO::findNodeCathegories($this->m_id);	
	}
};


//###########################################################################
//###########################################################################
//###########################################################################


/**
 * Represent a node in the CMS.
 */
class xNodeI18N extends xNode
{
	/**
	 * @var string
	 * @access public
	 */
	var $m_title;
	
	/**
	 * @var string
	 * @access public
	 */
	var $m_content;

	/**
	 * @var string
	 */
	var $m_lang;
	
	/**
	 * @var string
	 */
	var $m_translator;
	
	/**
	 *
	 * @param array(mixed) $parent_cathegories An array of xCathegory 
	 * objects or an array of cathegories ids
	 */
	function xNodeI18N($id,$type,$author,$content_filter,$title,$content,
		$lang,$translator,$parent_cathegories = array(),$creation_time = NULL,
		$edit_time = NULL)
	{
		$this->xNode($id,$type,$author,$content_filter,$parent_cathegories,
			$creation_time,$edit_time);
		
		$this->m_title = $title;
		$this->m_content = $content;
		$this->m_lang = $lang;
		$this->m_translator = $translator;
	}
	
	
	// DOCS INHERITHED  ========================================================
	function render()
	{
		$error = '';
		$content = xContentFilterController::applyFilter(
			$this->m_content_filter,$this->m_content,$error);
		$title = xContentFilterController::applyFilter(
			'notags',$this->m_title,$error);
		
		//format operations
		$ops = $this->getOperations();
		$formatted = array();
		foreach($ops as $op)
			$formatted[$op->m_name] = array('link' => 
				$op->getLink('node',$this->m_type,$this->m_id,$this->m_lang),
				'description' => $op->m_description);
		
		$operations = xTheme::render('renderNodeOperations',array($formatted));
		
		return xTheme::render('renderNode',array($this->m_type,$title,$content,
			$operations));
	}
	
	
	// DOCS INHERITHED  ========================================================
	function renderBrief()
	{
		$error = '';
		$content = xContentFilterController::applyFilter($this->m_content_filter,$this->m_content,$error);
		$title = xContentFilterController::applyFilter('notags',$this->m_title,$error);
		
		//format operations
		$ops = $this->getOperations();
		$formatted = array();
		foreach($ops as $op)
			$formatted[$op->m_name] = array('link' => $op->getLink('node',$this->m_type,$this->m_id,$this->m_lang),
				'description' => $op->m_description);
			
		$operations = xTheme::render('renderNodeOperations',array($formatted));
		
		return xTheme::render('renderBriefNode',array($this->m_type,$title,$content,$operations));
	}
	
	/** 
	 * Delete the node translation and if this is the last translation deletes the node at all.
	 */
	function deleteTranslation($id,$lang)
	{
		return xNodeI18NDAO::deleteTranslation($id,$lang);
	}
	
	
	function find($order = array(),$limit = array(),$id = NULL,$type = NULL,$author = NULL,$parent_cat = NULL,$lang = NULL,$flexible_lang = TRUE,
		$translator = NULL)
	{
		return xNodeI18NDAO::find($order,$limit,$id,$type,$author,$parent_cat,$lang,$flexible_lang,$translator);
	}
	
	/**
	 * 
	 */
	function getNodeTranslations()
	{
		return xNodeI18NDAO::getNodeTranslations($this->m_id);
	}
	
	
	/**
	 * @static
	 */
	function s_getNodeTranslations($nodeid)
	{
		return xNodeI18NDAO::getNodeTranslations($nodeid);
	}
	
	/**
	 * @static
	 */
	function isTranslatable($nodeid)
	{
		return xNodeI18NDAO::isTranslatable($nodeid);
	}
	
	/**
	 * @static
	 */
	function existsTranslation($nodeid,$lang)
	{
		return xNodeI18NDAO::existsTranslation($nodeid,$lang);
	}
	
	
	/**
	 * @return array(xOperation)
	 */
	function getOperations()
	{
		return array
			(
				new xOperation('edit_translation','Edit translation',''),
				new xOperation('delete_translation','Delete translation',''),
				new xOperation('delete_node','Delete node','')
			);
	}
};



//###########################################################################
//###########################################################################
//###########################################################################


/**
 * Represent the standard node in xanthin+
 */
class xNodePage extends xNodeI18N
{
	/**
	 * @var bool
	 * @access public
	 */
	var $m_sticky;
	
	/**
	 * @var bool
	 * @access public
	 */
	var $m_accept_replies;
	
	/**
	 * @var bool
	 * @access public
	 */
	var $m_published;
	
	/**
	 * @var bool
	 * @access public
	 */
	var $m_approved;
	
	/**
	 * @var string
	 * @access public
	 */
	var $m_meta_description;
	
	/**
	 * @var bool
	 * @access public
	 */
	var $m_meta_keywords;
	
	/**
	 *
	 */
	function xNodePage($id,$type,$author,$content_filter,$title,$content,$lang,$translator,
		$parent_cathegories,$creation_time,
		$edit_time,$published,$sticky,$accept_replies,$approved,$meta_description,$meta_keywords)
	{
		$this->xNodeI18N($id,$type,$author,$content_filter,$title,$content,$lang,$translator,$parent_cathegories,
			$creation_time,$edit_time);
			
		$this->m_sticky = (bool) $sticky;
		$this->m_accept_replies = (bool) $accept_replies;
		$this->m_published = (bool) $published;
		$this->m_approved = (bool) $approved;
		$this->m_meta_description = (string) $meta_description;
		$this->m_meta_keywords = (string) $meta_keywords;
	}
	
	/** 
	 * Inserts this into db
	 *
	 * @return bool FALSE on error
	 */
	function insert()
	{
		$this->m_id = xNodePageDAO::insert($this);
		return $this->m_id;
	}
	
	/** 
	 * Inserts this into db
	 *
	 * @return bool FALSE on error
	 */
	function insertTranslation()
	{
		return xNodePageDAO::insertTranslation($this);
	}
	
	/**
	 * Update this in db
	 *
	 * @return bool FALSE on error
	 */
	function update()
	{
		return xNodePageDAO::update($this);
	}
	
	
	/**
	 * Update this in db
	 *
	 * @return bool FALSE on error
	 */
	function updateTranslation()
	{
		return xNodePageDAO::updateTranslation($this);
	}
	
	/**
	 * @static
	 */	
	function find($order = array(),$limit = array(),$id = NULL,$type = NULL,$author = NULL,$parent_cat = NULL,$lang = NULL,$flexible_lang = TRUE,
		$translator = NULL)
	{
		return xNodePageDAO::find($order,$limit,$id,$type,$author,$parent_cat,$lang,$flexible_lang,$translator);
	}
	
	/**
	 * @static
	 */
	function isNodePage($id)
	{
		return xNodePageDAO::isNodePage($id);
	}
	
	/**
	 * @return array(xOperation)
	 */
	function getOperations()
	{
		$def = xNodeI18N::getOperations();
		
		return array_merge($def,
			array(
					new xOperation('edit_properties','Edit properties','')
				)
			);
	}
};


//###########################################################################
//###########################################################################
//###########################################################################

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
		if($path->m_resource === 'node')
		{
			if($path->m_type === NULL)
			{
				if($path->m_action === 'admin')
				{
					return new xResult(new xPageContentAdminNode($path));
				}
				elseif($path->m_action === 'translate')
				{
					return new xResult(new xPageContentNodeTranslate($path));
				}
			}
			
			elseif($path->m_type === 'page')
			{
				if($path->m_action === 'admin')
				{
					return new xResult(new xPageContentNodeAdminPage($path));
				}
				
				elseif($path->m_action === 'view')
				{
					return new xResult(new xPageContentNodeViewPage($path));
				}
				
				elseif($path->m_action === 'create')
				{
					return new xResult(new xPageContentNodePageCreate($path));
				}
				
				elseif($path->m_action === 'translate' && $path->m_id !== NULL)
				{
					return new xResult(new xPageContentNodeTranslatePage($path));
				}
				
				elseif($path->m_action === 'edit_translation' && $path->m_id !== NULL)
				{
					return new xResult(new xPageContentNodeEdittranslationPage($path));
				}
				
				elseif($path->m_action === 'delete_translation'	&& $path->m_id !== NULL)
				{
					return new xResult(new xPageContentNodeDeleteTranslation($path));
				}
			}
		}
		
		return NULL;
	}
	
	
	/**
	 * @see xDummyModule::xm_fetchNodeTypeClassName()
	 */
	function xm_fetchNodeTypeClassName($type)
	{
		if($type == 'page')
			return new xResult('xNodePage');
			
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
		
		return new xResult($descr);
	}
	
};
xModule::registerDefaultModule(new xModuleNode());


//###########################################################################
//###########################################################################
//###########################################################################


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
			return new xPageContentError($this->m_path,'Cannot translate this node');
			
		
		if(xNodeI18N::existsTranslation($this->m_path->m_id,$this->m_path->m_lang))
			return new xPageContentError($this->m_path,'A translation of this node in this language already exists');
			
		
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


//###########################################################################
//###########################################################################
//###########################################################################


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
		if(!xAccessPermission::checkCurrentUserPermission('node',$this->m_path->m_type,NULL,'edit_translation'))
			return new xPageContentNotAuthorized($this->m_path);
			
		if(! xNodeI18N::existsTranslation($this->m_path->m_id,$this->m_path->m_lang))
			return new xPageContentError($this->m_path,'A translation of this node does exists');
			
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


//###########################################################################
//###########################################################################
//###########################################################################

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
			return new xPageContentError($this->m_path,'A translation of this node does exists');
			
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
		$form = new xForm('delete_node',$this->m_path->getLink());
		
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
					if(xNodeI18N::deleteTranslation($this->m_path->m_id,$this->m_path->m_lang))
					{
						xNotifications::add(NOTIFICATION_NOTICE,'Node translation successfully deleted');
					}
					else
						xNotifications::add(NOTIFICATION_ERROR,'There was an error while deleting translation');
				}
				else
				{
					$this->_set("Delete node translation",'','','');
					$this->m_headers[] = 'Location: ' . Path::renderLink($this->m_path->m_lang,'node','view',
						$this->m_path->m_type,$this->m_path->m_id);
					
					return TRUE;
				}
				
				$this->_set("Delete node translation",'','','');
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

		$out = 'Are you sure do you want to delete this node translation? <br/> 
			Note: If this is the last translation of the node, the node will we deleted at all.<br/>';
		$this->_set("Delete node translation",$out . $form->render(),'','');
		return TRUE;
	}
};


//###########################################################################
//###########################################################################
//###########################################################################

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
	 * Let choose node type.
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
		
		xPageContent::_set("Manage nodes: choose type",$out,'','');
		return true;
	}
};


//###########################################################################
//###########################################################################
//###########################################################################


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
			$cathegory = xCathegory::load($this->m_path->m_id);
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


//###########################################################################
//###########################################################################
//###########################################################################


/**
 * 
 */
class xPageContentNodeView extends xPageContent
{	
	/**
	 * @var xNode
	 */
	var $m_node = NULL;
	
	function xPageContentNodeView($path)
	{
		$this->xPageContent($path);
	}
	
	/**
	 * Checks that node exists, checks cathegory and type view permission.
	 * If you inherit the xPageContentNodeView clas and override this member, remember
	 * to call the xPageContentNodeView::onCheckPreconditions() before your checks.
	 */
	function onCheckPreconditions()
	{
		//check type permission
		if(!xAccessPermission::checkCurrentUserPermission('node',$this->m_path->m_type,NULL,'view'))
			return new xPageContentNotAuthorized($this->m_path);
			
		//load node
		$class_name = xNode::getNodeTypeClass($this->m_path->m_type);
		if(empty($class_name))
			return new xPageContentNotFound($this->m_path);
			
		if(($this->m_node = 
			reset(call_user_func(array($class_name,'find'),array(),array(),$this->m_path->m_id))) === FALSE)
			return new xPageContentNotFound($this->m_path);
			
		$this->m_node->loadCathegories();
		
		//check cathegory permission
		foreach($this->m_node->m_parent_cathegories as $cathegory)
			if(! $cathegory->checkCurrentUserPermissionRecursive('view'))
				return new xPageContentNotAuthorized($this->m_path);
		
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

//###########################################################################
//###########################################################################
//###########################################################################


/**
 * 
 */
class xPageContentNodeViewI18N extends xPageContentNodeView
{	
	function xPageContentNodeView($path)
	{
		$this->xPageContentNodeView($path);
	}
	
	/**
	 * Checks that node exists, checks cathegory and type view permission.
	 * If you inherit the xPageContentNodeView clas and override this member, remember
	 * to call the xPageContentNodeView::onCheckPreconditions() before your checks.
	 */
	function onCheckPreconditions()
	{
		//check type permission
		if(!xAccessPermission::checkCurrentUserPermission('node',$this->m_path->m_type,NULL,'view'))
			return new xPageContentNotAuthorized($this->m_path);
			
		//load node
		$class_name = xNode::getNodeTypeClass($this->m_path->m_type);
		if(empty($class_name))
			return new xPageContentNotFound($this->m_path);
			
		if(($this->m_node = reset(call_user_func(array($class_name,'find'),
			array(),array(),$this->m_path->m_id,NULL,NULL,NULL,$this->m_path->m_lang))) === FALSE)
			return new xPageContentNotFound($this->m_path);
			
		$this->m_node->loadCathegories();
		
		//check cathegory permission
		foreach($this->m_node->m_parent_cathegories as $cathegory)
			if(! $cathegory->checkCurrentUserPermissionRecursive('view'))
				return new xPageContentNotAuthorized($this->m_path);
		
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


//###########################################################################
//###########################################################################
//###########################################################################


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
	 * 
	 */
	function onCreate()
	{
		$order = array();
		if(isset($this->m_path->m_params['order']))
		{
			$order[0]['column'] = $this->m_path->m_params['order'];
			$order[0]['direction'] = $this->m_path->m_params['direction'];
		}
		$nodes = xNodePage::find($order,array(),NULL,NULL,NULL,NULL,NULL,TRUE);
		$columns = array();
		$columns[] = new xColumn('ID','id',true);
		$columns[] = new xColumn('Created','creation_time',true);
		$columns[] = new xColumn('Title','title',false);
		$columns[] = new xColumn('Translations','translations',false);
		
		$data = array();
		$i = 0;
		foreach($nodes as $node)
		{
			$data[$i][] = (string) $node->m_id;
			$data[$i][] = strftime('%c',$node->m_creation_time);
			
			$error = '';
			$data[$i][] = '<a href="'.
				xPath::renderLink($node->m_lang,'node','view',$node->m_type,$node->m_id) . '">'.
				xContentFilterController::applyFilter('notags',$node->m_title,$error) . '</a>';
			
			$translations = $node->getNodeTranslations();
			$tmp = array();
			foreach($translations as $translation)
				$tmp[] = $translation->m_name;
				
			$data[$i][] = implode(', ',$tmp);
			
			$i++;
		}
		
		$table = new xTable($this->m_path,new xDefaultTableModel($columns,$data),'admin');
		$out = '<a href="'.xPath::renderLink($this->m_path->m_lang,'node','create','page').
			'">Create new node page</a><br/><br/>' . $table->render();
		
		xPageContent::_set('Manage "'.$this->m_path->m_type.'" nodes',$out,'','');
		return true;
	}
};


//###########################################################################
//###########################################################################
//###########################################################################

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
			return new xPageContentError($this->m_path,'The node is not of type page');
			
		return true;
	}
	
	
	/**
	 * Create and outputs node creation form
	 */
	function onCreate()
	{
		$node = xNodePage::load($this->m_path->m_id,xSettings::get('default_lang'));
		
		//create form
		$form = new xForm('translate_page',xanth_relative_path($this->m_path->m_full_path));
		
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
				
				if($node->insertTranslation())
				{
					xNotifications::add(NOTIFICATION_NOTICE,'Node successfully translated',2);
					$this->m_headers[] = 'Location: ' . xPath::renderLink($this->m_path->m_lang,'node','view',
						$this->m_path->m_type,$this->m_path->m_id);
				}
				else
					xNotifications::add(NOTIFICATION_ERROR,'There was an error while creating translation');
				
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


//###########################################################################
//###########################################################################
//###########################################################################


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
			return new xPageContentError($this->m_path,'The node is not of type page');
			
		return true;
	}
	
	
	/**
	 * Create and outputs node creation form
	 */
	function onCreate()
	{
		$node = xNodePage::load($this->m_path->m_id,$this->m_path->m_lang);
		
		//create form
		$form = new xForm('edit_page_translation',xanth_relative_path($this->m_path->m_full_path));
		
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
		$form->m_elements[] = new xFormSubmit('submit','Update');
		
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
				
				if($node->updateTranslation())
				{
					xNotifications::add(NOTIFICATION_NOTICE,'Node translation successfully updated',2);
					$this->m_headers[] = 'Location: ' . xPath::renderLink($this->m_path->m_lang,'node',
						'view',$this->m_path->m_type,$this->m_path->m_id);
				}
				else
					xNotifications::add(NOTIFICATION_ERROR,'There was an error while updating translation');
				
				$this->_set("Edit \"page\" node translation",'','','');
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

		$this->_set("Edit \"page\" node translation",$form->render(),'','');
		return TRUE;
	}
};

//###########################################################################
//###########################################################################
//###########################################################################


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
		$form = new xForm('create_page',xanth_relative_path($this->m_path->m_full_path));
		
		//no cathegory in path so let user choose according to its permissions
		if($this->m_path->m_id == NULL)
		{
			$cathegories = xCathegoryI18N::find($this->m_path->m_type,NULL,NULL,'en');
			$options = array();
			foreach($cathegories as $cathegory)
				$options[$cathegory->m_name] = $cathegory->m_id;
			
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
			$checked = false;
			if($filter['name'] == 'html')
				$checked = true;
				
			$content_filter_radio_group->m_elements[] = new xFormElementRadio('filter',$filter['name'],
				$filter['description'],$filter['name'],$checked,TRUE,new xInputValidatorContentFilter(64));
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
					
				if($node->insert())
				{
					xNotifications::add(NOTIFICATION_NOTICE,'New node successfully created',2);
					$this->m_headers[] = 'Location: ' . xPath::renderLink($this->m_path->m_lang,'node','view',
						$this->m_path->m_type,$node->m_id);
				}
				else
					xNotifications::add(NOTIFICATION_ERROR,'There was an error while creating the node');
				
				$this->_set("Create new node page",'','','');
				return TRUE;
			}
			else
			{
				foreach($ret->m_errors as $error)
					xNotifications::add(NOTIFICATION_WARNING,$error);
			}
		}

		$this->_set("Create new node page",$form->render(),'','');
		return TRUE;
	}
};



//###########################################################################
//###########################################################################
//###########################################################################


/**
 * 
 */
class xPageContentNodeViewPage extends xPageContentNodeViewI18N
{	
	function xPageContentNodeViewPage($path)
	{
		$this->xPageContentNodeViewI18N($path);
	}
	
	/**
	 * Only basic checks.No additional checks here.
	 */
	function onCheckPreconditions()
	{
		//todo check approved,sticky,published ecc...
		return xPageContentNodeViewI18N::onCheckPreconditions();
	}
	
	
	/**
	 * Fill this object with node properties by calling xNode->render(). Only metadata are not filled-id, 
	 * so override this funciton in your node type implementation.
	 */
	function onCreate()
	{
		$res = xPageContentNodeViewI18N::onCreate();
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