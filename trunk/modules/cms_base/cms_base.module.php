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


require_once(dirname(__FILE__) . '/cms_base.dao.php');



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
	 * 
	 */
	function xm_declareWidget()
	{
		
	}
	
	
	/**
	 * @see xDummyModule::xm_createPage()
	 */
	function xm_createPage(&$path)
	{
		$page = new xPageManager();
		$res = $page->preconditions();
		if(xError::isError($res))
			var_dump($res);
		$page->process();
		$page->filter();
		echo $page->render();
	}
	
	/**
	 * 
	 */
	function xm_fetchContent($path)
	{
			
	}
	
	
	/**
	 * Renders page by default
	 */
	function xm_render($renderable)
	{
		if(xanth_instanceof($renderable,'xPageManager'))
		{
			$contents = new xContentManager();
			$output = 
			'<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
			<html>
				<head>
					<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
					'.xHeaderManager::renderTitle().
					xHeaderManager::renderKeywords().
					xHeaderManager::renderDescription().
					xHeaderManager::renderStylesheets().'
				</head>
				<body>
					<div id="page">
						<div id="content">
							'.$contents->display().'
						</div>
					</div>
				</body>
			</html>';
			
			return $output;
		}
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
	var $m_widgets = array();
	
	/**
	 * On creation automatically loads all child widgets
	 */
	function xWidgetGroup($name,$widgets = array())
	{
		$this->xWidget($name);
		$this->m_widgets = $widgets;
	}
	
	/**
	 * 
	 */
	function dbInsert()
	{
		return xWidgetGroupDAO::insert($this);
	}
	
	/**
	 * 
	 */
	function dbUpdate()
	{
		return xWidgetGroupDAO::update($this);
	}
	
	/**
	 * Loads a widget group
	 */
	function load($name)
	{
		return reset(xWidgetGroupDAO::find($name));
	}
	
	/**
	 * Loads a widget group
	 */
	function find($name = NULL)
	{
		return xWidgetGroupDAO::find($name);
	}
	
	/**
	 * {@inheritdoc}
	 */
	function _preconditions()
	{
		$result_set = new xResultSet();
		foreach($this->m_widgets as $widget)
			$result_set->m_results[] = $widget->preconditions();
		
		if($result_set->containsErrors())
			return new xErrorGroup($result_set->getErrors());
		if($result_set->containsValue(true))
			return true;
			
		return false;
	}
	
	/**
	 * {@inheritdoc}
	 */
	function _process()
	{
		foreach($this->m_widgets as $widget)
			$widget->process();
			
		return true;
	}
	
	
	/**
	 * {@inheritdoc}
	 */
	function _filter()
	{
		foreach($this->m_widgets as $widget)
			$result_set = $widget->filter();
	}
	
	
	/**
	 * {@inheritdoc}
	 * 
	 * @todo sostituire con theme
	 */
	function _render(&$pre)
	{
		foreach($this->m_widgets as $widget)
			$pre .= $widget->render();
	}
}

//###########################################################################
//###########################################################################
//###########################################################################

/**
 * 
 */
class xPageManager extends xStaticWidget
{
	/**
	 * Creates an empty content. Call preconditions(),process() and filter() to fill out this object.
	 */
	function xPageManager()
	{
		$this->xStaticWidget();
	}
}



/**
 * Represent the main content of a web page
 */
class xContentManager extends xStaticWidget
{
	/**
	 * @var xContent
	 * @access public
	 */
	var $m_content;
	
	/**
	 * Creates an empty content. Call preconditions(),process() and filter() to fill out this object.
	 */
	function xContentManager()
	{
		$this->xStaticWidget();
		$path = xPath::getCurrent();
		$this->m_content = xModuleManager::invoke('xm_fetchContent',array($path));
		if($this->m_content === NULL)
			$this->m_content = new xContentPageNotFound($path);
	}

	/**
	 * 
	 */
	function load($name)
	{
		return new xContentManager();	
	}
	
	
	/**
	 * {@inheritdoc}
	 * <br> The xContent version fetch the content manager and check its preconditions
	 */
	function _process()
	{
		parent::_process();
		$this->m_content->process();
	}
	
	/**
	 * {@inheritdoc}
	 * <br> The xContent version fetch the content manager and check its preconditions
	 */
	function _filter()
	{
		parent::_filter();
		$this->m_content->filter();
	}
	
	/**
	 * {@inheritdoc}
	 * <br> The xContent version fetch the content manager and check its preconditions
	 */
	function _preconditions()
	{
		$res = parent::_preconditions();
		if(xError::isError($res))
			return $res;
			
		$res = $this->m_content->preconditions();
		if(xError::isError($res))
			echo "here error";
		
		return true;
	}
	
	
	/**
	 * {@inheritdoc}
	 */
	function _render(&$pre)
	{
		$pre .= $this->m_content->m_content;
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
		
		foreach($this->m_meta_keywords as $k => $v)
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
		
		var_dump();
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


/**
 * Represent the main content of a web page
 */
class xContentError extends xContent
{
	var $m_error;
	
	/**
	 * 
	 */
	function xContentError($path,$error)
	{
		$this->xContent($path);
		$this->m_error = $error;
	}
	
	/**
	 * 
	 */
	function _preconditions()
	{
		return true;
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