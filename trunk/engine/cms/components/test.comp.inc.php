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
* A module for tests
*/
class xModuleTest extends xModule
{
	function xModuleTest()
	{
		$this->xModule('Test','engine/cms/components/');
	}

	// DOCS INHERITHED  ========================================================
	function getContent($path)
	{
		if($path->m_base_path == 'test')
		{
			xAccessFilterSetDAO::load(1);
			
			return new xContentSimple("Test",'','','');
		}
		
		return NULL;
	}
};

xModule::registerModule(new xModuleTest());
	
?>