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
class xEngineDummyModule extends xModule
{
	/**
	 * Return a content component.
	 * Called with invoke().
	 * 
	 * @return NULL
	 */
	function xh_fetchContent(&$path)
	{
	}
	
	
	
	/**
	 * Return a list of raw components to be included in the current document.
	 * Called with invokeAll().
	 * 
	 * @return mixed A named array of xComponent objects.
	 */
	function xh_documentComponents(&$path)
	{
	}
	
	/**
	 * Return a list of string representing the relative path to a css file.
	 * Called with invokeAll().
	 * 
	 * @return mixed A named array of xComponent objects.
	 */
	function xh_documentStylesheets(&$path)
	{
	}
}

?>