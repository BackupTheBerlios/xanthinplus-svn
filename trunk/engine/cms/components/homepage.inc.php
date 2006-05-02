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
* The module responsible of homepage.
*/
class xModuleHomepage extends xModule
{
	function xModuleHomepage()
	{
		$this->xModule('Homepage','engine/cms/components/');
	}

	/**
	 * @see xDummyModule::getContent()
	 */
	function getContent($path)
	{
		if($path->m_base_path == '')
		{
			return new xContentHomepage();
		}
		
		return NULL;
	}
};

/**
*
*/
class xContentHomepage extends xContent
{
	
	function xContentHomepage()
	{
		$this->xContent('Homepage','Home','description','keywords');
	}

	// DOCS INHERITHED  ========================================================
	function render()
	{
		return 'Xanthin homepage test';
	}
};


xModule::registerModule(new xModuleHomepage());
	
?>
