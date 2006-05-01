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
* Module responsible of user management
*/
class xModuleUser extends xModule
{
	function xModuleUser()
	{
		$this->xModule('User','engine/cms/components/');
	}

	/**
	 * @see xDummyModule::renderBoxContent()
	 */
	function renderBoxContent($id)
	{
		if($id == 'Login')
		{
			return 'Test user';
		}
		
		return NULL;
	}
};


xModule::registerModule(new xModuleUser());
	
?>
