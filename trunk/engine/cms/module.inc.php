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
* The base class for modules.
* 
* See xDummyModule for a list of methods you can implement to respond to various events/request.
*/
class xModule
{
	/**
	* @var string
	* @access public
	*/
	var $m_name;
	
	/**
	* Relative path to the xanthine directory
	*
	* @var string
	* @access public
	*/
	var $m_path;
	
	/**
	*
	* @param string $name
	* @param string $path Relative path to the xanthine directory
	*/
	function xModule($name,$path)
	{
		$this->m_name = $name;
		$this->m_path = $path;
	}
	
	//----------------STATIC FUNCTIONS----------------------------------------------
	
	/**
	* Register a module.
	*
	* @param xModule $module The module to register.
	* @static
	*/
	function registerModule($module)
	{
		global $g_modules;
		if(!isset($g_modules))
		{
			$g_modules = array();
		}
		
		$g_modules[] = $module;
	}
	
	
	/**
	* Retrieve all registered modules as an array.
	*
	* @return array(xModule) all registered modules.
	* @static
	*/
	function getModules()
	{
		global $g_modules;
		if(!isset($g_modules))
		{
			$g_modules = array();
		}
		
		return $g_modules;
	}
	
};



/**
* A Dummy module that elecates all method you can implement in your module
*/
class xDummyModule extends xModule
	{

	function xDummyModule()
	{
		//cannot instantiate this class
		assert(FALSE);
	}

	/**
	* This method should executes all sql queries needed to install a module in a mysql db.
	*/
	function installDBMySql()
	{}
	
	/**
	* Returns a valid xContent for the passed path
	*
	* @param xXanthPath $path
	* @return xContent A valid xContent object if your module is the responsable of the given path, NULL otherwise.
	*/
	function getContent($path)
	{}
};




?>
