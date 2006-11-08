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
	 */
	function xm_install($db_name)
	{
	}
	
	/**
	 * Renders a renderable object
	 * 
	 * @return string Returns the rendered element or a xError object on error.
	 */
	function xm_render(&$renderable_obj)
	{
	}
	
	/**
	 * Pre-process the contents of a renderable object.
	 * 
	 * @return NULL 
	 */
	function xm_preprocess(&$renderable_obj)
	{
	}
	
	
	/**
	 * Post-process the contents of a renderable object.
	 * 
	 * @return NULL
	 */
	function xm_postprocess(&$renderable_obj)
	{
	}
	
	
	/**
	 * Pre-filter the contents of a renderable object.
	 * 
	 * @return NULL 
	 */
	function xm_prefilter(&$renderable_obj)
	{
	}
	
	
	/**
	 * Post-filter the contents of a renderable object.
	 * 
	 * @return NULL
	 */
	function xm_postfilter(&$renderable_obj)
	{
	}
	
	/**
	 * Called when the page creation occur. Use this method to do all the stuff befor a the page is created
	 */
	function xm_onPageCreation()
	{
	}
	
	/**
	 * Called after framework initialization but before page fetching.
	 * 
	 */
	function xm_onInit()
	{
	}
}



?>
