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



define('X_CM_SUCCESS',1);
define('X_CM_BYPASS',3);

define('X_COMPONENT_NOT_INITIALIZED',0);
define('X_COMPONENT_BYPASS',1);
define('X_COMPONENT_INIT_FAILED',2);
define('X_COMPONENT_AUTHORIZE_FAILED',3);
define('X_COMPONENT_PROCESS_FAILED',4);

define('X_COMPONENT_INITIALIZED',10);
define('X_COMPONENT_AUTHORIZED',12);
define('X_COMPONENT_PROCESSED',13);



/**
 * Represent a component in a form ready to be rendered.
 */
class xComponentView extends xObject
{
	var $m_template_name = '';
	
	function __construct($template_name)
	{
		parent::__construct();
		$this->m_template_name = $template_name;
	}
	
	/**
	 * @return void
	 */
	function _filter()
	{
		if($this->_doFilter() === false)
		{
			$mod =& x_getModuleManager();
			$mod->invokeAll('xh_filterComponentView',array(&$this));
		}
	}
	
	/**
	 * Override this to feet your needs.
	 * @return bool Return false if fail
	 */
	function _doFilter()
	{
	}
	
	
	/**
	 * Execute a mirroring of the properties of the given compoenent view inside
	 * this object properties.
	 */
	function mirror($other_view)
	{
		$vars = get_object_vars($other_view);
		foreach($vars as $name => $var)
			$this->$name = $var;
	}
	
	
	/**
	 * Reset the contents of this view.
	 */
	function reset()
	{
		$vars = get_object_vars($this);
		foreach($vars as $name => $var)
		{
			if(is_array($this->$name))
				$this->$name = array();
			elseif(is_string($this->$name))
				$this->$name = '';
			elseif(is_int($this->$name))
				$this->$name = 0;
			elseif(is_object($this->$name))
				$this->$name = NULL;
		}
	}
	
	/**
	 * @return void
	 */
	function display()
	{
		$this->_filter();
		$this->_doDisplay();
	}
	
	/**
	 * Override this to feet your needs.
	 * @return void
	 */
	function _doDisplay()
	{
		$tpl = new xTemplate($this->m_template_name);
		$tpl->display($this);
	}
}


/**
 * A component is a part of a web document. A component is composed by one or more
 * extensions, the contents and behaviour of a component is defined by such extensions. 
 */
class xComponentController extends xObject
{	
	/**
	 * @var int
	 */
	var $m_state = X_COMPONENT_NOT_INITIALIZED;
	
	/**
	 * @var object
	 */
	var $m_component_view = NULL;
	
	
	/**
	 * {@inheritdoc}
	 */
	function __construct()
	{
		parent::__construct();
	}
	
	/**
	 * Init this component
	 * 
	 * @access private
	 * @return void
	 */
	function _init()
	{
		$this->m_state = X_COMPONENT_INITIALIZED;
		if($this->_doInit() === false)
			$this->m_state = X_COMPONENT_INIT_FAILED;
		else
		{
			$mod =& x_getModuleManager();
			$mod->invokeAll('xh_initComponentController',array(&$this));
		}
	}
	
	/**
	 * Init this component
	 * 
	 * @access private
	 * @return bool Return false if fail
	 */
	function _doInit()
	{}
	
	/**
	 * Check authorization for this components.
	 * 
	 * @access private
	 * @return void
	 */
	function _authorize()
	{
		if($this->m_state < X_COMPONENT_INITIALIZED)
			return;
		
		$this->m_state = X_COMPONENT_AUTHORIZED;
		if(!$this->_doAuthorize() === false)
			$this->m_state = X_COMPONENT_AUTHORIZE_FAILED;
		else
		{
			$mod =& x_getModuleManager();
			$mod->invokeAll('xh_authComponentController',array(&$this));
		}
	}
	
	/**
	 * Check authorization for this components. If authorization 
	 * 
	 * @access private
	 * @return bool Return false if fail
	 */
	function _doAuthorize()
	{}
	
	/**
	 * Crete and process contents for this component
	 * 
	 * @access private
	 * @return void
	 */
	function _process()
	{
		if($this->m_state < X_COMPONENT_AUTHORIZED)
			return;
		
		$this->m_state = X_COMPONENT_PROCESSED;
		if(!$this->_doProcess() === false)
			$this->m_state = X_COMPONENT_PROCESS_FAILED;
		else
		{
			$mod =& x_getModuleManager();
			$mod->invokeAll('xh_processComponentController',array(&$this));
		}
	}
	
	/**
	 * Create and process contents for this component. Thi method must create a component view
	 * and fill it with contents.
	 * 
	 * @access private
	 * @return bool Return false if fail
	 */
	function _doProcess()
	{}
	
	/**
	 * Executes _init(),_authorize(),_process().
	 * 
	 * @return void
	 */
	function process()
	{
		$this->_init();
		$this->_authorize();
		$this->_process();
	}
	
	
	/**
	 * Display this component
	 * 
	 * @return void
	 */
	function display()
	{

		if(($this->m_state === X_COMPONENT_PROCESSED || $this->m_state === X_COMPONENT_BYPASS)
			&& $this->m_component_view !== NULL)
		{
			$this->m_component_view->display();
		}
	}
	
	
	/**
	 * Renders this component
	 * 
	 * @return void
	 */
	function render()
	{
		ob_start();
		$this->display();
		return ob_get_clean();
	}
}

?>