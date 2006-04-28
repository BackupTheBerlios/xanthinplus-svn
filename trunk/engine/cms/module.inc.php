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
* Tha base class for modules.
*/
class xModule
{
	function xModule()
	{
	}
	
	/**
	* This method should executes all sql queries needed to install a module in a mysql db.
	*
	* @return Nothing.
	*/
	function installDBMySql()
	{}
	
	//----------------STATIC FUNCTIONS----------------------------------------------
	
	/**
	* Register a module.
	*
	* @param $module (xModule) the module to register.
	* @return Nothing
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
	* @return (array(xModule)) all registered modules.
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


?>
