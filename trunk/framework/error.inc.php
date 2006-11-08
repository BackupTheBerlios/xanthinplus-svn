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
class xError
{
	/**
	 * @var int
	 */
	var $m_error;
	
	/**
	 * @var string
	 */
	var $m_description;
	
	
	function xError($error,$description = NULL)
	{
		$this->m_error = $error;
		$this->m_description = $description;
	}
	
	
	/**
	 * Returns true if the given object represent an error.
	 */
	function isError(&$obj)
	{
		return xanth_instanceof($obj,'xError');
	}
}

/**
 * 
 */
class xErrorGroup extends xError
{
	var $m_errors;
	
	/**
	 * 
	 */
	function xErrorGroup($errors = array(),$description = NULL)
	{
		$this->xError('Multiple errors',$description);
		$this->m_errors = $errors;
	}
}


/**
 * 
 */
class xErrorPageNotFound extends xError
{

	/**
	 * 
	 */
	function xErrorPageNotFound($description = NULL)
	{
		$this->xError('Page not found',$description);
	}
}



?>