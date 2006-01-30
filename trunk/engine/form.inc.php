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

define('FORM_DATA_TYPE_TEXT_NO_TAGS','form_data_type_text_no_tags');
define('FORM_DATA_TYPE_TEXT_WITH_TAGS','form_data_type_text_with_tags');
define('FORM_DATA_TYPE_INTEGER','form_data_type_integer');


class xanthFormValidator
{
	var $data_type;
	var $mandatory;
	var $maxlength;
	
	function xanthFormValidator($data_type,$mandatory,$maxlength)
	{
		$this->data_type = $data_type;
		$this->mandatory = $mandatory;
		$this->maxlength) = $maxlength;
	}
}

/**
*
*/
class xanthFormElement
{
	var $name;
	var $label;
	var $description;
	var $xanthFormValidator;
	
	function xanthFormElement($name,$label,$description,$validator)
	{
		$this->name = $name;
		$this->label = $label;
		$this->description = $description;
		$this->validator = $validator;
	}
};

/**
*
*/
class xanthFormTextField extends xanthFormElement
{
	var $default_value;
	
	function xanthFormTextField($name,$label,$description,$default_value,$validator)
	{
		$this->xanthFormElement($name,$label,$description,$validator);
		$this->default_value = $default_value;
	}
};

class xanthFormTextArea extends xanthFormElement
{
	var $default_value;
	
	function xanthFormTextArea($name,$label,$description,$default_value,$validator)
	{
		$this->xanthFormElement($name,$label,$description,$validator);
		$this->default_value = $default_value;
	}
};

/**
*
*/
class xanthFormComboBox extends xanthFormElement
{
	var $values;
	var $default_value;
	
	function xanthFormTextField($name,$label,$description,$values,$default_value,$validator)
	{
		$this->xanthFormElement($name,$label,$description,$validator);
		$this->default_value = $default_value;
		$this->values = $value;
	}
};

/**
*
*/
class xanthFormSubmit extends xanthFormElement
{
	var $value;
	
	function xanthFormSubmit($name,$value,$label = NULL,$description = NULL)
	{
		$this->xanthFormElement($name,$label,$description,NULL);
		$this->value = $value;
	}
};

/**
*
*/
class xanthFormGroup
{
	var $elements;
	var $label;
	
	function xanthFormGroup($elements,$label = NULL)
	{
		$this->label = $label;
		$this->elements = $elements;
	}
};

/**
*
*/
class xanthForm
{
	var $groups;
	var $action;
	
	function xanthForm($action,$groups)
	{
		$this->action = $action;
		$this->groups = $groups;
	}
};

/**
*
*/
class xanthFormData
{
	var $valid_data;
	var $invalid_data;
	
	function xanthFormData($valid_data,$invalid_data)
	{
		$this->valid_data = $valid_data;
		$this->invalid_data = $invalid_data;
	}
};

/**
*
*/
function xanth_form_to_html($form)
{
	$output = "<form action=\"". $form->action . "\" method=\"post\"> \n";

	foreach($form->groups as $group)
	{
		if(!empty($group->label))
		{
			$output .= "<fieldset> \n";
			$output .= "<legend>" . $group->label . "</legend> \n";
		}
		
		foreach($group->elements as $element)
		{
			$output .= '<div class="form-element">'. "\n";
			
			if(!empty($element->label))
			{
				$output .= '<label for="id-'.$element->name.'">'.$element->label.'</label>' . "\n";
			}
			
			if(xanth_instanceof($element,'xanthFormTextField'))
			{
				$output .= '<input maxlength="' . $element->validator->maxlength . '" ';
				$output .= ' name="' . $element->name .'" '; 
				$output .= ' id="id-' . $element->name . '" value="'.$element->default_value.'" type="text">'."\n";
			}
			elseif(xanth_instanceof($element,'xanthFormTextArea'))
			{
				$output .= '<textarea name="' . $element->name .'" '; 
				$output .= ' id="id-' . $element->name . '">'. $element->default_value . '</textarea>'."\n";
			}
			elseif(xanth_instanceof($element,'xanthFormSubmit'))
			{
				$output .= '<input name="' . $element->name .'" value="'.$element->value.'" type="submit">'."\n";
			}
			
			$output .= '</div>'. "\n";
		}
		
		if(!empty($group->label))
		{
			$output .= "<\fieldset> \n";
		}
	}
	$output .= "</form> \n";
	
	return $output;
}


/**
*
*/
function xanth_form_validate_data($form)
{
	
}


?>