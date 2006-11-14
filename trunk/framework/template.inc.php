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
 * 
 */
class xTemplate extends xObject
{
	/**
	 * 
	 */
	var $m_name;
	
	/**
	 * 
	 */
	var $m_template_file;
	
	
	/**
	 * 
	 */
	function __construct($name)
	{
		$app =& xApplication::getInstance();
		$theme =& $app->getThemeManager();
		$this->m_template_file = $theme->invoke('xt_templateMapping',$name);
		if($this->m_template_file === NULL)
			xLog::log('Template',LOG_LEVEL_ERROR,'Template mapping does not exists',__FILE__,__LINE__);
	}
	
	/**
	 * Echoes the data resulting from this template.
	 * 
	 * @param mixed $data Data to be passed to template
	 */
	function display($data)
	{
		include_once($this->m_template_file);
	}
}
?>