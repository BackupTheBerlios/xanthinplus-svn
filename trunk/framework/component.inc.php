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
define('X_COMPONENT_PREPROCESS_FAILED',2);
define('X_COMPONENT_AUTHORIZE_FAILED',3);
define('X_COMPONENT_PROCESS_FAILED',4);
define('X_COMPONENT_FILTER_FAILED',5);

define('X_COMPONENT_INITIALIZED',10);
define('X_COMPONENT_PREPROCESSED',11);
define('X_COMPONENT_AUTHORIZED',12);
define('X_COMPONENT_PROCESSED',13);
define('X_COMPONENT_FILTERED',14);



/**
 * Represent a component in a form ready to be rendered.
 */
class xVisualComponent extends xObject
{
	function __construct()
	{
		parent::__construct();
	}
	
	/**
	 * 
	 */
	function render()
	{
	}
}


/**
 * A component is a part of a web document. A component is composed by one or more
 * extensions, the contents and behaviour of a component is defined by such extensions. 
 */
class xComponent extends xObject
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
	function __construct($type)
	{
		parent::__construct();
		$this->m_type = $type;
	}
	
	
	/**
	 * Insert this component into db. All operations are wrapped in a transaction.
	 * 
	 * @return bool
	 */
	function insert()
	{
	}
	
	
	/**
	 * Delete this component from db
	 * 
	 * @return bool
	 */
	function delete()
	{
	}
	
	/**
	 * Update this component into db
	 * 
	 * @return bool
	 */
	function update()
	{

	}
	
	
	/**
	 * Finds components
	 * @return array An array containing the elements found
	 */
	function find()
	{

	}
	
	/**
	 * Init this component
	 *
	 * @return mixed True on success, an xError object on error
	 */
	function init()
	{
	}
	
	
	/**
	 * Check authorization for this components
	 * 
	 * @return bool True if the access is permitted
	 */
	function authorize()
	{}
	
	
	/**
	 * Crete and process contents for this component
	 * 
	 * @return mixed True on success, an xError object on error
	 */
	function process()
	{}
	
	
	/**
	 * Create a copy of this object and filters its contents before rendering. 
	 * This function creates a copy of the object in $this->m_filtered, becouse after
	 * processing its contents, they are usually non consistent with the original object logic.
	 * 
	 * @return mixed True on success, an xError object on error
	 */
	function filter()
	{}
	
	
	/**
	 * Preprocess this component
	 * 
	 * @return string
	 */
	function render()
	{}
	
	
	/**
	 * Executes preprocess(),permissions(), process(),filter().
	 * 
	 * @return mixed True on success, an xError object on error
	 */
	function autoProcess()
	{}
}


//###########################################################################
//###########################################################################
//###########################################################################


/**
 * Represent the whole document. Implements singletone pattern 
 */
class xDocument extends xComponent
{
	/**
	 * {@inheritdoc}
	 */
	function __construct()
	{
		parent::__construct();
	}
}



?>