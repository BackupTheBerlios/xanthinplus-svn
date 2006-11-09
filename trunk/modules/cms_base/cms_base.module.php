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


function xm_load_cms_base()
{
	return new xModuleCmsBase();
}


/**
 * <strong> Weight = 0 <strong>
 */
class xModuleCmsBase extends xModule
{
	function xModuleCmsBase()
	{
		$this->xModule(0,'Provides xWidgetGroup,xPage,xContent,xContentManager objects'.
			'and starts page creation workflow',
			'Mario Casciaro <xshadow [at] email (dot) it>','pre-alhpa5');	
	}
	
	
	/**
	 * @see xDummyModule::xm_install()
	 */
	function xm_install($db_name)
	{
		$db =& xDB::getDB();
		
		//widget group
		$db->query("
			CREATE TABLE widget_group (
			name VARCHAR(64) NOT NULL,
			PRIMARY KEY (name)
			)TYPE=InnoDB DEFAULT CHARACTER SET utf8"
		);
		
		//group to widget
		$db->query("
			CREATE TABLE group_to_widget (
			group_name VARCHAR(64) NOT NULL,
			class_name VARCHAR(64) NOT NULL,
			widget_name VARCHAR(64) NOT NULL,
			PRIMARY KEY (group_name,class_name,widget_name),
			FOREIGN KEY (group_name) REFERENCES widget_group(name)
			)TYPE=InnoDB DEFAULT CHARACTER SET utf8"
		);
	}
	
	
	/**
	 * @see xDummyModule::xm_createPage()
	 */
	function xm_createPage(&$path)
	{
		
	}
}



//###########################################################################
//###########################################################################
//###########################################################################


/**
 * 
 */
class xWidgetGroup extends xWidget
{
	var $m_widgets;
	
	/**
	 * 
	 */
	function xWidgetGroup($name,$widgets = array())
	{
		$this->xWidget($name);	
	}
	
	
	/**
	 * 
	 */
	function dbInsert()
	{
		xWidgetGroupDAO::insert($this);
	}
	
	/**
	 * 
	 */
	function dbUpdate()
	{
		xWidgetGroupDAO::update($this);
	}
	
	
	/**
	 * @static
	 */
	function find($name)
	{
		xWidgetGroupDAO::find();
	}
	
	
	/**
	 * {@inheritdoc}
	 */
	function _preconditions()
	{
		$result_set = new xResultSet();
		foreach($this->m_widgets as $widget)
			$result_set = $widget->preconditions();
		
		if($result_set->containsError())
			return new xErrorGroup($result_set->getErrors());
		elseif($result_set->containsValue(false))
			return false;
			
		return true;
	}
	
	
	/**
	 * {@inheritdoc}
	 */
	function _process()
	{
		foreach($this->m_widgets as $widget)
			$result_set = $widget->process();
	}
	
	
	/**
	 * {@inheritdoc}
	 */
	function _filter()
	{
		foreach($this->m_widgets as $widget)
			$result_set = $widget->filter();
	}
}


//###########################################################################
//###########################################################################
//###########################################################################


/**
 * Represent the whole web page
 */
class xPage extends xWidgetGroup
{
	function xPage(&$path)
	{
		$this->xWidgetGroup('default');
	}
}



//###########################################################################
//###########################################################################
//###########################################################################


/**
 * Represent the main content of a web page
 */
class xContentManager extends xWidget
{
	/**
	 * @var xContent
	 * @access public
	 */
	var $m_content;
	
	/**
	 * 
	 */
	var $m_path;
	
	/**
	 * Creates an empty content. Call preconditions(),process() and filter() to fill out this object.
	 */
	function xContentManager(&$path)
	{
		$this->xWidget('default');
		$this->m_path = $path;
		$this->m_content = xModuleManager::invoke('xm_fetchContent',array($this->m_path));
	}
	
	
	/**
	 * {@inheritdoc}
	 * <br> The xContent version fetch the content manager and check its preconditions
	 */
	function _process()
	{
		if($this->m_content === NULL)
			$this->m_content = xContentPageNotFound($this->m_path);
		
		echo '';
	}
}



/**
 * Represent the main content of a web page
 */
class xContent extends xRenderable
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
	 * @access public
	 */
	var $m_meta_description;
	
	/**
 	 * @var array(string)
	 * @access public
	 */
	var $m_meta_keywords;
	
	/**
	 * @var array(string)
	 * @access public
	 */
	var $m_headers;
	
	/**
	 * @var xPath 
	 */
	var $m_path;
	
	
	/**
	 * Creates an empty content manager. Call preconditions(),process() and filter() to fill out this object.
	 */
	function xContent(&$path)
	{
		$this->xRenderable();
		$this->m_title = '';
		$this->m_content = '';
		$this->m_meta_description = '';
		$this->m_meta_keywords = array();
		$this->m_headers = array();
	}
	
	
	/**
	 * @access protected
	 */
	function _set($title,$content,$meta_description,$meta_keywords)
	{
		$this->m_title = $title;
		$this->m_meta_description = $meta_description;
		$this->m_meta_keywords = $meta_keywords;
		$this->m_content = $content;
	}
	
	
	/**
	 * {@inheritdoc}
	 * 
	 * <br> The xContent version convert special characters in html 
	 * entities in title, description and keywords. 
	 */
	function _filter()
	{
		$this->m_title = htmlspecialchars($this->m_title);
		
		foreach($this->m_keywords as $k => $v)
			$this->m_meta_keywords[$k] = htmlspecialchars($v);
		
		$this->m_meta_description = htmlspecialchars($this->m_meta_description);
	}
	
	
	/**
	 * {@inheritdoc}
	 * 
	 * <br> The xContent version set http-headers, and html header metadata and title.
	 */
	function _render(&$res)
	{
		//output headers
		foreach($this->m_headers as $header)
			header($header);
			
		$tmp =& xHeaderManager::getTitle();
		$tmp = $this->m_title;
		
		$tmp =& xHeaderManager::getKeywords();
		$tmp = array_merge($tmp,$this->m_keywords);
		
		$tmp =& xHeaderManager::getDescription();
		$tmp = $this->m_description;
	}
}




/**
 * Represent the main content of a web page
 */
class xContentPageNotFound extends xContent
{
	/**
	 * 
	 */
	function xContentPageNotFound($path)
	{
		$this->xContent($path);
	}
	
	/**
	 * {@inheritdoc}
	 */
	function _process()
	{
		parent::_process();
		$this->_set('Page not found','The page you requested was not found','',array());
	}
}



?>