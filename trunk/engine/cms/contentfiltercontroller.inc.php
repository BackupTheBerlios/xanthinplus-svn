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
	 * Return a form element for asking for contentn filter chooser
	 *
	 * @param string $var_name The name of the form element
	 * @param string $value
	 * @param bool $mandatory True if this input is manadtory
	 * @return xFormElement
	 * @static
	 */
	function getFormContentFilterChooser($var_name,$value,$mandatory)
	{
		$content_filter_radio_group = new xFormRadioGroup('Content filter');
		
		$filters = array();
		$filters[] = array('name' => 'html','description' => 'Full HTML');
		$filters[] = array('name' => 'php','description' => 'PHP code');
		$filters[] = array('name' => 'bbcode','description' => 'BBCode');
		$filters[] = array('name' => 'notags','description' => 'Tags are stripped');
		
		foreach($filters as $filter)
		{
			$checked = FALSE;
			if($value === $filter['name'])
			{
				$checked = TRUE;
			}
			
			$content_filter_radio_group->m_elements[] = new xFormElementRadio($var_name,$filter['name'],
				$filter['description'],$filter['name'],$checked,$mandatory,	new xInputValidatorTextNameId(64));
		}
		
		return $content_filter_radio_group;
	}
}


/**
 * A validator that checks dinamically the content, by providing a dinamyc "post" or "get" variable
 * representing a content filter.
 */
class xInputValidatorDynamicContentFilter extends xInputValidatorText
{
	var $m_variable_name;
	var $m_method;
	
	/**
	 * Contructor.
	 *
	 * @param int $maxlenght The max lenght of the text to be considered valid.
	 */
	function xInputValidatorDynamicContentFilter($maxlength,$variable_name,$method = 'POST')
	{
		xInputValidatorText::xInputValidatorText($maxlength);
		
		$this->m_variable_name = $variable_name;
		$this->m_method = $method;
	}
	
	// DOCS INHERITHED  ========================================================
	function isValid($input)
	{
		if( ! xInputValidatorText::isValid($input))
		{
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



?>