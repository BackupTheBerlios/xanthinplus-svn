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
 * A module for box
 */
class xModuleMenu extends xModule
{
	function xModuleMenu()
	{
		xModule::xModule();
	}


	/**
	 * @see xDummyModule::xm_fetchContent()
	 */ 
	function xm_fetchContent($path)
	{
	}
	
	/**
	 * @see xDummyModule::xm_fetchPermissionDescriptors()
	 */ 
	function xm_fetchPermissionDescriptors()
	{
	}
	
	/**
	 * @see xDummyModule::xm_fetchBox()
	 */ 
	function xm_fetchBox($box_name,$box_type)
	{
		switch($box_type)
		{
			case 'menu':
				return xMenu::dbLoad($box_name);
				break;
		}
		
		return NULL;
	}
	
};

xModule::registerDefaultModule(new xModuleMenu());

	
?>