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


function xm_load_module_document()
{
	return new xDocumentComponent();
}


/**
 * The component to manage web documents.
 */
class xDocumentComponent extends xModule
{
	/**
	 * 
	 */
	function __construct()
	{
		parent::__construct('Manage web documents','Mario Casciaro','alpha');
	}
	
	
	/**
	 * 
	 */
	function registerHooks(&$mod_man)
	{
		$mod_man->registerHook($this,'xh_fetchDAO','xm_fetchDAO');
		$mod_man->registerHook($this,'xh_templateMapping','xm_templateMapping');
		$mod_man->registerHook($this,'xh_createDocument','xm_createDocument');
	}
	
	/**
	 * {@inheritdoc}
	 */
	function xm_fetchDAO($db_type,$name)
	{
	}
	
	/**
	 * 
	 */
	function xm_createDocument(&$path)
	{
		echo "HI!";
	}
	
	/**
	 * {@inheritdoc}
	 */
	function xm_templateMapping($name)
	{
		if($name === 'document')
			return dirname(__FILE__).'/templates/document.tpl.php';
	}
}


/**
 * 
 */
class xDocumentView extends xComponentController
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
		parent::__construct();
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
	
	/**
	 * 
	 */
	function _doDisplay()
	{
		$tpl = new xTemplate('document');
		$tpl->display($this);	
	}
}


/**
 * Represent the whole document. Implements singletone pattern.
 */
class xDocument extends xComponentController
{
	var $m_http_headers = array();
	var $m_components;
	
	/**
	 * Singleton object,call getInstance()
	 */
	function __construct()
	{
		parent::__construct();
	}
	
	
	/**
	 * 
	 */
	function _doProcess()
	{
		$this->m_component_view = new xDocumentView();
		$this->m_component_view->m_title = $this->m_title;
		
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
}


?>