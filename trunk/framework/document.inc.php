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
 * 
 */
class xDocumentView extends xComponentView
{
	var $m_title = '';
	var $m_keywords = array();
	var $m_description = '';
	var $m_stylesheets = array();
	var $m_language = '';
	var $components = array();
	
	/**
	 * {@inheritdoc}
	 */
	function __construct()
	{
		parent::__construct('document');
	}
	
	/**
	 * 
	 */
	function _doFilter()
	{
		$this->m_title = htmlspecialchars($this->m_title,ENT_QUOTES,'UTF-8');
		foreach($this->m_keywords as $k => $v)
			$this->m_keywords[$k] = htmlspecialchars($this->m_keywords[$k],ENT_QUOTES,'UTF-8');
		$this->m_description = htmlspecialchars($this->m_description,ENT_QUOTES,'UTF-8');
		foreach($this->m_stylesheets as $k => $v)
			$this->m_stylesheets[$k] = htmlspecialchars($this->m_stylesheets[$k],ENT_QUOTES,'UTF-8');
		$this->m_language = htmlspecialchars($this->m_language,ENT_QUOTES,'UTF-8');
		return true;
	}
}


//###########################################################################
//###########################################################################
//###########################################################################


/**
 * Represent the whole document. Implements singletone pattern.
 */
class xDocument extends xComponentController
{
	var $m_http_headers = array();
	var $m_components;
	var $m_path;
	
	
	/**
	 * Singleton object,call getInstance()
	 */
	function __construct()
	{
		parent::__construct();
		$this->m_path =& xPath::getCurrent();
		$mod =& x_getModuleManager();
		$components = $mod->invokeAll('xh_documentComponents',array(&$this->m_path));
		$this->m_components = $components->getValidValues(true);
		
		$this->m_component_view = new xDocumentView();
	}
	
	/**
	 * Singleton
	 */
	function &getInstance()
	{
		static $s_document;
		if(!isset($s_document))
			$s_document = new xDocument();
			
		return $s_document;
	}
	
	
	/**
	 * 
	 */
	function _doInit()
	{
		foreach($this->m_components as $k => $v)
			$this->m_components[$k]->_init();
			
		return true;
	}
	
	
	/**
	 * 
	 */
	function _doAuthorize()
	{
		foreach($this->m_components as $k => $v)
			$this->m_components[$k]->_authorize();
			
		return true;
	}
	
	
	/**
	 * 
	 */
	function _doProcess()
	{
		$mod =& x_getModuleManager();
		$ss = $mod->invokeAll('xh_documentStylesheets',array(&$this->m_path));
		$this->m_component_view->m_stylesheets = $ss->getValidValues(true);
		
		$this->m_component_view->m_language = $this->m_path->m_lang;
		foreach($this->m_components as $k => $v)
		{
			$this->m_components[$k]->_process();
			
			if($this->m_components[$k]->isReadyToDisplay())
			{
				$this->m_component_view->m_components[$k] =& $this->m_components[$k]->m_component_view; 
				
				if(xanth_instanceof($this->m_components[$k],'xContentController'))
				{
					$this->m_component_view->m_title = $this->m_components[$k]->m_component_view->m_title;
					$this->m_component_view->m_keywords = $this->m_components[$k]->m_component_view->m_keywords;
					$this->m_component_view->m_description = $this->m_components[$k]->m_component_view->m_description;
				}
			}
		}
		return true;
	}
}


?>