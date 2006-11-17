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
class xContentView extends xComponentView
{
	var $m_title = '';
	var $m_keywords = array();
	var $m_description = '';
	
	/**
	 * {@inheritdoc}
	 */
	function __construct($template_name)
	{
		parent::__construct($template_name);
	}
}

//###########################################################################
//###########################################################################
//###########################################################################

/**
 * Represent the controller for the main page content.
 */
class xContentController extends xComponentController
{
	var $m_path;
	
	/**
	 * {@inheritdoc}
	 */
	function __construct(&$path)
	{
		parent::__construct();
		$this->m_path =& $path;
	}
	
	
	/**
	 * 
	 */
	function fetchContent(&$path)
	{
		$mod =& x_getModuleManager();
		$content = $mod->invoke('xh_fetchContent',array(&$this->m_path));
		if($content === NULL)
			$content = new xStaticContentController($this->m_path,new xPageNotFoundContentView());
			
		return $content;
	}
}

//###########################################################################
//###########################################################################
//###########################################################################

/**
 * 
 */
class xPageNotFoundContentView extends xContentView
{
	/**
	 * {@inheritdoc}
	 */
	function __construct()
	{
		parent::__construct('page_not_found');
		$this->m_title = 'Page not found';
	}
}


//###########################################################################
//###########################################################################
//###########################################################################


/**
 * Represent a static controller.
 */
class xStaticContentController extends xContentController
{
	/**
	 * {@inheritdoc}
	 */
	function __construct(&$path,$content_view)
	{
		parent::__construct($path);
		$this->m_component_view = $content_view;
	}
	
	/**
	 * {@inheritdoc}
	 */
	function _doInit()
	{
		//deny all other processing
		return false;	
	}
}


?>