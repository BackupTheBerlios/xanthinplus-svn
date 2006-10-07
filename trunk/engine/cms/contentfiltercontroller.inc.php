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
 * A helper class for content filtering
 */
class xContentFilterController
{
	function xContentFilterController()
	{
		assert(FALSE);
	}
	
	/**
	 *
	 * @return string
	 */
	function applyFilter($filtername,$input,&$error)
	{
		switch($filtername)
		{
			case 'html':
				$filter = new xContentFilterBypass();
				break;
				
			case 'php':
				$filter = new xContentFilterPhp();
				break;
				
			case 'bbcode':
				$filter = new xContentFilterBBCode();
				break;
				
			case 'notags':
				$filter = new xContentFilterNoTags();
				break;
				
			default:
				xLog::log(LOG_LEVEL_WARNING,'Invalid content filter name: ' . $filtername);
				return '';
		}
		
		$ret = $filter->filter($input);
		if($ret === NULL)
		{
			$error = 'Error while filtering content: ' . $filter->m_last_error;
			return NULL;
		}
		
		return $ret;
	}
	
	
	/**
	 * Return all available filters.
	 *
	 * @return array(array(name -> string,description-> string))
	 * @static
	 */
	function getAllFilters()
	{
		$filters = array();
		$filters[] = array('name' => 'html','description' => 'Full HTML');
		$filters[] = array('name' => 'php','description' => 'PHP code');
		$filters[] = array('name' => 'bbcode','description' => 'BBCode');
		$filters[] = array('name' => 'notags','description' => 'Tags are stripped');
		
		return $filters;
	}
	
	/**
	 * Return all filters usable by the current user.
	 *
	 * @return array(array(name -> string,description-> string))
	 * @static
	 */
	function getCurrentUserAvailableFilters()
	{
		$filters = xContentFilterController::getAllFilters();
		
		$avail_filters = array();
		foreach($filters as $filter)
		{
			if(xAccessPermission::checkCurrentUserPermission('filter',$filter['name'],NULL,'use'))
				$avail_filters[] = $filter;
		}
		
		return $avail_filters;
	}
}


/**
 * A validator that checks dinamically the content, by providing a dinamyc "post" or "get" variable
 * representing a content filter.
 */
class xDynamicInputValidatorApplyContentFilter extends xDynamicInputValidator
{
	var $m_variable_name;
	
	var $m_maxlength;
	/**
	 * Contructor.
	 *
	 * @param int $maxlenght The max lenght of the text to be considered valid.
	 */
	function xDynamicInputValidatorApplyContentFilter($maxlength,$variable_name,$method = 'POST')
	{
		xDynamicInputValidator::xDynamicInputValidator($method);
		
		$this->m_variable_name = $variable_name;
		$this->m_maxlength = $maxlength;
	}
	
	// DOCS INHERITHED  ========================================================
	function isValid($input)
	{
		$text_validator = new xInputValidatorText($this->m_maxlength );
		if( ! $text_validator->isValid($input))
		{
			$this->m_last_error = $text_validator->m_last_error;
			return FALSE;
		}
		
		$error = '';
		if(xContentFilterController::applyFilter(xFormElement::getInputValueByName($this->m_variable_name,$this->m_method),
			$input,$error) === NULL)
		{
			$this->m_last_error = $error;
			return FALSE;
		}
		
		return TRUE;
	}
}



/**
 * A validator that checks if filter is valid and usable by current user
 */
class xInputValidatorContentFilter extends xInputValidatorTextNameId
{
	/**
	 * Contructor.
	 *
	 * @param int $maxlenght The max lenght of the text to be considered valid.
	 */
	function xInputValidatorDynamicContentFilter($maxlength)
	{
		xInputValidatorTextNameId::xInputValidatorTextNameId($method);
	}
	
	// DOCS INHERITHED  ========================================================
	function isValid($input)
	{
		if( ! xInputValidatorTextNameId::isValid($input))
			return FALSE;
		
		if(! xAccessPermission::checkCurrentUserPermission('filter',$input,NULL,'use'))
		{
			$this->m_last_error = 'You are not authorized to use such filter';
			return false;
		}
		
		return true;
	}
}



?>