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
 * A dummy module class for documentation purpose only.
 */
class xDummyModule extends xModule
{
	/**
	 * This method should executes all sql queries needed to install a module.
	 * 
	 * @return NULL
	 */
	function xm_install($db_name)
	{
	}
	
	
	/**
	 * This method is called to create the full web page.
	 * <br> Called with xModuleManager::invoke();
	 * 
	 * @return NULL
	 */
	function xm_createPage(&$path)
	{
	}
	
	/**
	 * This method is called after module load.
	 * <br> Called with xModuleManager::invokeAll();
	 * 
	 * @return NULL
	 */
	function xm_initModules()
	{
	}
	
	
	/**
	 * This method is called after module load and init.
	 * <br> Called with xModuleManager::invokeAll();
	 * 
	 * @return NULL
	 */
	function xm_initUtilities()
	{
	}
	
	
	/**
	 * This method is called to finalize all modules.
	 * <br> Called with xModuleManager::invokeAll();
	 * 
	 * @return NULL
	 */
	function xm_finalModules()
	{
	}
	
	
	/**
	 * This method is called to finalize all utilities.
	 * <br> Called with xModuleManager::invokeAll();
	 * 
	 * @return NULL
	 */
	function xm_finalUtilities()
	{
	}
	
	
	/**
	 * Sets daos for a db. $db->m_daos['[dao name]'] = new DAO();
	 */
	function xm_DAO(&$db)
	{
	}
}



?>