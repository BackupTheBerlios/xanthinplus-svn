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
 * An object that excecutes trnasformations on text inputs.
 * @abstract
 */
class xContentFilter
{	
	/**
	 * @var string
	 * @access public
	 */
	var $m_last_error;
	
	function xContentFilter()
	{	
		$this->m_last_error = '';
	}
	
	/**
	 * Executes a filtering
	 *
	 * @return string The filtered text of NULL on error (an error description is placed in the member $m_last_error).
	 * @abstract
	 */
	function filter($input)
	{
		//virtual funciton
		assert(FALSE);
	}
};


/**
 * A dummy filter object
 */
class xContentFilterBypass extends xContentFilter
{	
	function xContentFilterBypass()
	{	
		xContentFilter::xContentFilter();
	}
	
	// DOCS INHERITHED  ========================================================
	function filter($input)
	{
		return $input;
	}
};


/**
 * This filter evalutate the input as php code and return the result.
 */
class xContentFilterPhp extends xContentFilter
{
	function xContentFilterPhp()
	{	
		xContentFilter::xContentFilter();
	}
	
	// DOCS INHERITHED  ========================================================
	function filter($input)
	{
		$ret = @eval($input);
		
		if($ret === FALSE)
		{
			$this->m_last_error = 'There was an error on an evalutate php code';
			return NULL;
		}
		
		return $ret;
	}
};



/**
 * Convert a bbcode input into XHTL code
 */
class xContentFilterBBCode extends xContentFilter
{	
	function xContentFilterBBCode()
	{	
		xContentFilter::xContentFilter();
	}
	
	// DOCS INHERITHED  ========================================================
	function filter($input)
	{
		$bbparser = new xBBCodeParser($input);
		$res = $bbparser->parse();
		if($res === FALSE)
		{
			$this->m_last_error = 'There was an error in BBCode parsing: ' . $bbparser->m_last_error;
			return NULL;
		}
		
		return $bbparser->m_htmltext;
	}
};



/**
 * Clear the input text from all tags
 */
class xContentFilterNoTags extends xContentFilter
{	
	function xContentFilterNoTags()
	{	
		xContentFilter::xContentFilter();
	}
	
	// DOCS INHERITHED  ========================================================
	function filter($input)
	{
		return htmlspecialchars($input);
	}
};


?>