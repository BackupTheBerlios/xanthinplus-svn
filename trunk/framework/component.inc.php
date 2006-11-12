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


/**
 * A component is a part of a web document. A component is composed by one or more
 * managers, the contents and behaviour of a component is defined by such managers. 
 */
class xComponent extends xObject
{
	/**
	 * @var array
	 */
	var $m_managers = array();
	
	/**
	 * @var string
	 */
	var $m_type = 'default';
	
	
	/**
	 * {@inheritdoc}
	 */
	function __construct($type)
	{
		parent::__construct();
		$this->m_type = $type;
	}
	
	/**
	 * Insert this component into db.
	 * 
	 * @return bool
	 */
	function insert()
	{}
	
	/**
	 * Delete this component from db
	 * 
	 * @return bool
	 */
	function delete()
	{}
	
	/**
	 * Update this component into db
	 * 
	 * @return bool
	 */
	function update()
	{}
	
	/**
	 * Finds components
	 * 
	 * @param string $type The type of the component
	 * @param array $properties An array structured in this manner
	 * <code>
	 * array(
	 * [property name] => [property value]
	 * )
	 * </code>
	 * @return array An array containing the elements found
	 */
	function find($type,$properties)
	{}
	
	
	/**
	 * Preprocess this component
	 * 
	 * @return mixed True on success, an xError object on error
	 */
	function preprocess()
	{}
	
	
	/**
	 * Check permissions for this components
	 * 
	 * @return bool True if the access is permitted
	 */
	function permissions()
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
 * Represent the object that manage a component.
 */
class xComponentManager extends xObject
{
	/**
	 * @var int
	 */
	var $m_priority;
	
	
	/**
	 * {@inheritdoc}
	 */
	function __construct()
	{
		parent::__construct();
	}
	
	/**
	 * Insert given component into db.
	 * 
	 * @return bool
	 */
	function insert(&$component)
	{}
	
	/**
	 * Delete given component from db
	 * 
	 * @return bool
	 */
	function delete(&$component)
	{}
	
	/**
	 * Update given component into db
	 * 
	 * @return bool
	 */
	function update(&$component)
	{}
	
	/**
	 * Finds components
	 * 
	 * @param string $type The type of the component
	 * @param array $properties An array structured in this manner
	 * <code>
	 * array(
	 * [property name] => [property value]
	 * )
	 * </code>
	 * @return array An array containing the elements found
	 */
	function find(&$component,$properties)
	{}
	
	
	/**
	 * 
	 * @return NULL
	 */
	function componentFromRow(&$component,$row)
	{}
	
	
	/**
	 * Preprocess given component
	 * 
	 * @return mixed X_CM_SUCCESS on success,X_CM_BYPASS if this manager must bypass others, 
	 * an xError object on error
	 */
	function preprocess(&$component)
	{}
	
	
	/**
	 * Check permissions for given component
	 * 
	 * @return mixed X_CM_SUCCESS on success,X_CM_BYPASS if this manager must bypass others.
	 */
	function permissions(&$component)
	{}
	
	
	/**
	 * Crete and process contents for this component
	 * 
	 * @return mixed X_CM_SUCCESS on success,X_CM_BYPASS if this manager must bypass others, 
	 * an xError object on error
	 */
	function process(&$component)
	{}
	
	
	/**
	 * Create a copy of this object and filters its contents before rendering. 
	 * This function creates a copy of the object in $this->m_filtered, becouse after
	 * processing its contents, they are usually non consistent with the original object logic.
	 * 
	 * @return mixed X_CM_SUCCESS on success,X_CM_BYPASS if this manager must bypass others, 
	 * an xError object on error
	 */
	function filter(&$filtered_component)
	{}
}


//###########################################################################
//###########################################################################
//###########################################################################


/**
 * Represent the whole document. Implements singletone pattern 
 */
class xDocument extends xObject
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