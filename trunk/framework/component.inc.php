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
define('X_COMPONENT_INIT_FAILED',1);
define('X_COMPONENT_AUTHORIZE_FAILED',2);
define('X_COMPONENT_PROCESS_FAILED',3);
define('X_COMPONENT_FILTER_FAILED',4);

define('X_COMPONENT_INITIALIZED',10);
define('X_COMPONENT_AUTHORIZED',12);
define('X_COMPONENT_PROCESSED',13);
define('X_COMPONENT_FILTERED',14);



/**
 * Represent a component in a form ready to be rendered.
 */
class xComponentView extends xObject
{
	function __construct()
	{
		parent::__construct();
	}
	
	/**
	 * @return mixed True on success, an xError object on error
	 */
	function filter()
	{}
	
	
	/**
	 * 
	 */
	function display()
	{
	}
}


/**
 * A component is a part of a web document. A component is composed by one or more
 * extensions, the contents and behaviour of a component is defined by such extensions. 
 */
class xComponentController extends xObject
{	
	/**
	 * @var string
	 */
	var $m_state = X_COMPONENT_NOT_INITIALIZED;
	
	/**
	 * @var xVisualComponent
	 */
	var $m_visual_component = NULL;
	
	
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
	 * @return mixed True on success, an xError object on error
	 */
	function _init()
	{}
	
	
	/**
	 * Check authorization for this components
	 * 
	 * @access private
	 * @return bool True if the access is permitted
	 */
	function _authorize()
	{}
	
	
	/**
	 * Crete and process contents for this component
	 * 
	 * @access private
	 * @return mixed True on success, an xError object on error
	 */
	function _process()
	{}
	
	/**
	 * Executes _init(),_authorize(),_process().
	 * 
	 * @return mixed True on success, an xError object on error
	 */
	function process()
	{}
}



/**
 * 
 */
class xContentView extends xComponentController
{
	/**
	 * {@inheritdoc}
	 */
	function __construct()
	{
		parent::__construct();
	}
}



/**
 * Represent the controller for the main page content.
 */
class xContentController extends xComponentController
{	
	/**
	 * {@inheritdoc}
	 */
	function __construct()
	{
		parent::__construct();
	}
	
	
}


//###########################################################################
//###########################################################################
//###########################################################################


/**
 * Represent the whole document. Implements singletone pattern 
 */
class xDocument extends xComponentController
{
	var $m_used_components;
	
	/**
	 * {@inheritdoc}
	 */
	function __construct()
	{
		parent::__construct();
		
	}
}



?>