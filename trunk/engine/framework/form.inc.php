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
 * An object for validate user input.
 */
class xInputValidator
{
	/**
	 * @var string
	 * @access public
	 */
	var $m_last_error = '';
	
	function xInputValidator()
	{}
	
	/**
	 * Validate the user input.
	 *
	 * @param mixed $input The input to validate
	 * @return bool
	 */
	function isValid($input)
	{
		return TRUE;
	}
}

/**
 * A simple validator that checks only for a valid text lenght.
 */
class xInputValidatorText extends xInputValidator
{
	/** 
	 * @var int
	 * @access public
	 */
	var $m_maxlength;
	
	/**
	 * Contructor.
	 *
	 * @param int $maxlenght The max lenght of the text to be considered valid.
	 */
	function xInputValidatorText($maxlength)
	{
		xInputValidator::xInputValidator();
		$this->m_maxlength = $maxlength;
	}
	
	
	// DOCS INHERITHED  ========================================================
	function isValid($input)
	{
		if(empty($input))
		{
			return TRUE;
		}
		
		if($this->m_maxlength > 0 && strlen($input) > $this->m_maxlength)
		{
			$this->m_last_error = 'Field contains too much characters (max is '.$this->m_maxlength.')';
			return FALSE;
		}

		return TRUE;
	}
}

/**
 * A simple validator that checks for valid BBcode.
 */
class xInputValidatorBBCode extends xInputValidator
{
	
	/**
	 * Contructor.
	 *
	 * * @param int $maxlenght The max lenght of the text to be considered valid.
	 */
	function xInputValidatorBBCode($maxlength)
	{
		xInputValidatorText::xInputValidatorText($maxlength);
	}
	
	
	// DOCS INHERITHED  ========================================================
	function isValid($input)
	{
		if(! xInputValidatorText::isValid($input))
		{
			return FALSE;
		}
		
		$bbparser = new xBBCodeParser($input);
		if($bbparser === FALSE)
		{
			$this->m_last_error = 'You have an error in your BBCode syntax: ' . $bbparser->m_last_error;
			return FALSE;
		}
		
		return TRUE;
	}
}


/**
 * A validator that checks if a text correspond to a given regular expression.
 */
class xInputValidatorTextRegex extends xInputValidatorText
{
	/**
	 * @var string
	 * @access public
	 */
	var $m_regex;
	
	/**
	 * Contructor.
	 *
	 * @param string $regex A regular expression for the validation
	 * @param int $maxlenght The max lenght of the text to be considered valid.
	 */
	function xInputValidatorTextRegex($regex,$maxlength)
	{
		xInputValidatorText::xInputValidatorText($maxlength);
		$this->m_regex = regex;
	}
	
	// DOCS INHERITHED  ========================================================
	function isValid($input)
	{
		if(! xInputValidatorText::isValid($input))
		{
			return FALSE;
		}
		
		if(!preg_match($this->m_regex,$input))
		{
			$this->m_last_error = 'Field does not contain a valid input';
			return FALSE;
		}
		
		return TRUE;
	}
}


/**
 * A validator that checks if the input is a valid email address.
 */
class xInputValidatorTextEmail extends xInputValidatorText
{
	
	function xInputValidatorTextEmail()
	{
		xInputValidatorText::xInputValidatorText('',0);
	}
	
	// DOCS INHERITHED  ========================================================
	function isValid($input)
	{
		if(!xInputValidatorText::isValid($input))
		{
			return FALSE;
		}
		
		if(!xanth_valid_email($input))
		{
			$this->m_last_error = 'Field does not contain a valid email address';
			return FALSE;
		}
		
		return TRUE;
	}
}


/**
 * A validator that checks if the input is a valid string id compatible with the xanthin+ batabase specifications.
 */
class xInputValidatorTextNameId extends xInputValidatorText
{
	/**
	 * Contructor.
	 *
	 * @param int $maxlenght The max lenght of the text to be considered valid.
	 */
	function xInputValidatorTextNameId($maxlength)
	{
		xInputValidatorText::xInputValidatorText($maxlength);
	}
	
	// DOCS INHERITHED  ========================================================
	function isValid($input)
	{
		if( ! xInputValidatorText::isValid($input))
		{
			return FALSE;
		}
		
		if(!preg_match('#^[A-Z][A-Z0-9_-]{2,'.$this->m_maxlength.'}$#i',$input))
		{
			$this->m_last_error = 'Field does not contain a valid name';
			return FALSE;
		}
		
		return TRUE;
	}
}


/**
 * A validator that checks if the input is a valid integer number.
 */
class xInputValidatorInteger extends xInputValidator
{	
	function xInputValidatorInteger()
	{
		xInputValidator::xInputValidator();
	}
	
	// DOCS INHERITHED  ========================================================
	function isValid($input)
	{
		if(empty($input))
		{
			return TRUE;
		}
		
		if(! is_numeric($input))
		{
			$this->m_last_error = 'Field must contain a valid number';
			return FALSE;
		}
		
		
		return TRUE;
	}
}


/**
 * The base class for all form elements.
 *
 * @abstract
 */
class xFormElement
{
	var $m_name;
	var $m_label;
	var $m_description;
	var $m_value;
	var $m_validator;
	var $m_mandatory;
	var $m_invalid = FALSE;
	var $m_enabled = TRUE;
	var $m_last_error = '';
	
	function xFormElement($name,$label,$description,$value,$mandatory,$validator)
	{
		$this->m_name = $name;
		$this->m_label = $label;
		$this->m_description = $description;
		$this->m_value = $value;
		$this->m_mandatory = $mandatory;
		
		if(empty($validator))
		{
			$this->m_validator = new xInputValidatorText(0);
		}
		else
		{
			$this->m_validator = $validator;
		}
	}
	
	/**
	 * Return the user input corresponding to this form element.
	 *
	 * @return string The user input on success, an empty string otherwise.
	 */
	function getInputValue($method)
	{
		return xFormElement::getInputValueByName($this->m_name,$method);
	}
	
	/**
	 * Return the user input corresponding to a from element name.
	 *
	 * @return string The user input on success, an empty string otherwise.
	 * @static
	 */
	function getInputValueByName($name,$method)
	{
		$names = array();
		//parse array structure
		if(preg_match('#^([A-Z0-9_-]+)((\[[A-Z0-9_-]*\])*)$#i',$name,$pieces))
		{
			$names[] = $pieces[1];
			if(!empty($pieces[2]))
			{
				if(preg_match_all('#\[([A-Z0-9_-]*)\]*#i',$pieces[2],$pieces))
				{
					array_unshift($pieces[1],$names[0]);
					$names = $pieces[1];
				}
				else
				{
					//invalid variable name
					assert(FALSE);
				}
			}
		}
		
		if(strcasecmp($method,'POST') == 0)
		{
			return xFormElement::_getInputValueFromRecurseArray($_POST,$names);
		}
		elseif(strcasecmp($method,'GET') == 0)
		{		
			return xFormElement::_getInputValueFromRecurseArray($_GET,$names);
		}
		else
		{
			assert(FALSE);
		}
		
		return NULL;
	}
	
	
	/**
	 * @access private
	 * @static
	 */
	function _getInputValueFromRecurseArray($array,$names)
	{
		$name = (string) $names[0];
		if(is_numeric($names[0]))
		{
			$name = (int) $names[0];
		}
		
		if(! isset($array[$name]))
		{
			return '';
		}
		elseif(! is_array($array[$name]))
		{
			return $array[$name];
		}
		elseif(isset($names[1]))
		{
			array_shift($names);
			return xFormElement::_getInputValueFromRecurseArray($array[$name],$names);
		}
		
		return $array[$name];
	}
	
	/**
	 * Validate the user input corresponding to this form element, using the provided validator.
	 *
	 * @param string $method POST or GET
	 * @return bool
	 */
	function isValid($method)
	{
		$posted_value = $this->getInputValue($method);
		if($posted_value === '')
		{
			if($this->m_mandatory)
			{
				$this->m_last_error = 'Field '.$this->m_label.' is mandatory';
				$this->m_invalid = TRUE;
				return FALSE;
			}
			
			return TRUE;
		}
		
		if(! $this->m_validator->isValid($posted_value))
		{
			$this->m_invalid = TRUE;
			$this->m_last_error = $this->m_label . ': ' .$this->m_validator->m_last_error;
			
			return FALSE;
		}

		return TRUE;
	}
	
	/**
	 * Render the current form element.
	 *
	 * @return string The renderized element.
	 * @abstract
	 */
	function render()
	{
		//virtual method.
		assert(FALSE);
	}
};


/**
 * Represent a text field.
 */
class xFormElementTextField extends xFormElement
{
	function xFormElementTextField($name,$label,$description,$value,$mandatory,$validator)
	{
		xFormElement::xFormElement($name,$label,$description,$value,$mandatory,$validator);
	}
	
	// DOCS INHERITHED  ========================================================
	function isValid($method)
	{
		$this->m_value = htmlspecialchars($this->getInputValue($method));
		return xFormElement::isValid($method);
	}
	
	// DOCS INHERITHED  ========================================================
	function render()
	{
		$output = '<div class="form-element" '.$this->m_invalid.'>'. "\n";
		$output .= '<label for="id-'.$this->m_name.'">'.$this->m_label;
		if($this->m_mandatory)
		{
			$output .= '<b>*</b>';
		}
		$output .= '</label>' . "\n";
		$output .= '<input';
		if($this->m_validator->m_maxlength > 0 )
		{
			$output .= ' maxlength="' . $this->m_validator->m_maxlength . '" ';
		}
		$output .= ' class="form-textfield';
		if($this->m_invalid)
		{
			$output .= ' form-element-invalid';
		}
		$output .= '" name="' . $this->m_name .'" '; 
		$output .= ' id="id-' . $this->m_name . '" value="'.$this->m_value.'" type="text"/>'."\n";
		$output .= '</div>'. "\n";
		return $output;
	}
};

/**
 * Represent a password fieldn.
 */
class xFormElementPassword extends xFormElement
{
	function xFormElementPassword($name,$label,$description,$mandatory,$validator)
	{
		xFormElement::xFormElement($name,$label,$description,'',$mandatory,$validator);
	}
	
	// DOCS INHERITHED  ========================================================
	function isValid($method)
	{
		return xFormElement::isValid($method);
	}
	
	// DOCS INHERITHED  ========================================================
	function render()
	{
		$output = '<div class="form-element" '.$this->m_invalid.'>'. "\n";
		$output .= '<label for="id-'.$this->m_name.'">'.$this->m_label;
		if($this->m_mandatory)
		{
			$output .= '<b>*</b>';
		}
		$output .= '</label>' . "\n";
		$output .= '<input';
		if($this->m_validator->m_maxlength > 0 )
		{
			$output .= ' maxlength="' . $this->m_validator->m_maxlength . '" ';
		}
		$output .= ' class="form-password';
		if($this->m_invalid)
		{
			$output .= ' form-element-invalid';
		}
		$output .= '" name="' . $this->m_name .'" '; 
		$output .= ' id="id-' . $this->m_name . '" value="'.$this->m_value.'" type="password"/>'."\n";
		$output .= '</div>'. "\n";
		
		return $output;
	}
};

/**
*
*/
class xFormElementTextArea extends xFormElement
{
	function xFormElementTextArea($name,$label,$description,$value,$mandatory,$validator)
	{
		xFormElement::xFormElement($name,$label,$description,$value,$mandatory,$validator);
	}
	
	// DOCS INHERITHED  ========================================================
	function isValid($method)
	{
		$this->m_value = htmlspecialchars($this->getInputValue($method));
		return xFormElement::isValid($method);
	}
	
	// DOCS INHERITHED  ========================================================
	function render()
	{
		$output = '<div class="form-element" '.$this->m_invalid.'>'. "\n";
		$output .= '<label for="id-'.$this->m_name.'">'.$this->m_label;
		if($this->m_mandatory)
		{
			$output .= '<b>*</b>';
		}
		$output .= '</label>' . "\n";
		$output .= '<textarea class="form-textarea';
		if($this->m_invalid)
		{
			$output .= ' form-element-invalid';
		}
		$output .= '" name="' . $this->m_name .'" '; 
		if($this->m_validator->m_maxlength > 0 )
		{
			$output .= ' maxlength="' . $this->m_validator->m_maxlength . '" ';
		}
		$output .= ' id="id-' . $this->m_name . '" rows="8" cols="50">'. $this->m_value . '</textarea>'."\n";
		$output .= '</div>'. "\n";
		return $output;
	}
};

/**
 * Represent an hidden form field.
 */
class xFormElementHidden extends xFormElement
{
	var $m_multiple;
	
	function xFormElementHidden($name,$label,$value,$multiple,$validator)
	{
		xFormElement::xFormElement($name,$label,NULL,$value,TRUE,$validator);
		$this->m_multiple = $multiple;
	}
	
	// DOCS INHERITHED  ========================================================
	function isValid($method)
	{
		$posted_value = $this->getInputValue($method);
		
		//check for mandatory
		if(! xFormElement::isValid($method))
		{
			return FALSE;
		}
		
		//array or sigle value
		if(is_array($posted_value))
		{
			foreach($posted_value as $element)
			{
				if(! $this->m_validator->isValid($element))
				{
					$this->m_invalid = TRUE;
					$this->m_last_error = $this->m_label . ': ' .$this->m_validator->m_last_error;
					
					return FALSE;
				}
			}
		}
		else
		{
			if(! $this->m_validator->isValid($element))
			{
				$this->m_invalid = TRUE;
				$this->m_last_error = $this->m_label . ': ' .$this->m_validator->m_last_error;
				
				return FALSE;
			}
		}
		
		return TRUE;
	}
	
	// DOCS INHERITHED  ========================================================
	function render()
	{
		$output = '<div class="form-element" '.$this->m_invalid.'>'. "\n";
		$output .= '<input class="form-hidden';
		$output .= '" name="' . $this->m_name;
		if($this->m_multiple)
		{
			$output .= '[]';
		}
		$output .= '" value="'.$this->m_value.'" '; 
		$output .= ' id="id-' . $this->m_name . '" type="hidden" />'."\n";
		$output .= '</div>'. "\n";
		return $output;
	}
};

/**
 * Represent a check box.
 */
class xFormElementCheckbox extends xFormElement
{
	var $m_checked;
	
	function xFormElementCheckbox($name,$label,$description,$value,$checked,$mandatory,$validator)
	{
		xFormElement::xFormElement($name,$label,$description,$value,$mandatory,$validator);
		$this->m_checked = $checked;
	}
	
	// DOCS INHERITHED  ========================================================
	function isValid($method)
	{
		if($this->getInputValue($method) === $this->m_value)
		{
			$this->m_checked = TRUE;
		}
		else
		{
			$this->m_checked = FALSE;
		}
		
		return xFormElement::isValid($method);
	}
	
	// DOCS INHERITHED  ========================================================
	function render()
	{
		$output = '<div class="form-element" '.$this->m_invalid.'>'. "\n";
		$output .= '<input';
		$output .= ' class="form-checkbox';
		if($this->m_invalid)
		{
			$output .= ' form-element-invalid';
		}
		$output .= '" name="' . $this->m_name .'" ';
		if(!empty($this->m_checked))
		{	
			$output .= ' checked="checked" ';
		}
		$output .= ' id="id-' . $this->m_name . '" value="'.$this->m_value.'" type="checkbox"/>'."\n";
		$output .= '<label class="checkbox-label" for="id-'.$this->m_name.'">'.$this->m_label;
		$output .= '</label>' . "\n";
		$output .= '</div>'. "\n";
		
		return $output;
	}
};


/**
 * Represent an option form element.
 */
class xFormElementOptions extends xFormElement
{
	var $m_multi_select;
	var $m_options;
	
	function xFormElementOptions($name,$label,$description,$value,$options,$multi_select,$mandatory,$validator)
	{
		xFormElement::xFormElement($name,$label,$description,$value,$mandatory,$validator);
		$this->m_options = $options;
		$this->m_multi_select = $multi_select;
	}
	
	
	// DOCS INHERITHED  ========================================================
	function isValid($method)
	{
		$posted_value = $this->getInputValue($method);
		
		//check for mandatory
		if($posted_value === '')
		{
			if($this->m_mandatory)
			{
				$this->m_last_error = 'Field '.$this->m_label.' is mandatory';
				$this->m_invalid = TRUE;
				return FALSE;
			}
		}
		
		
		$elements_posted = array();
		//array or sigle value
		if(is_array($posted_value))
		{
			if(!$this->m_multi_select)
			{
				$this->m_last_error = 'Cannot select multiple values for field '.$this->m_label;
				return FALSE;
			}
			else
			{
				$elements_posted = $posted_value;
			}
		}
		else
		{
			$elements_posted = array('' => $posted_value);
		}
		
		
		//check if all values corresponds to at least an option
		$elements_posted_checked = array();
		foreach($elements_posted as $ignore => $element_posted)
		{
			$found = FALSE;
			foreach($this->m_options as $opt_name => $opt_val)
			{
				//check validator
				if(! $this->m_validator->isValid($opt_val))
				{
					$this->m_invalid = TRUE;
					$this->m_last_error = $this->m_label . ': ' .$this->m_validator->m_last_error;
					
					return FALSE;
				}
				
				if($opt_val == $element_posted)
				{
					$found = TRUE;
					break;
				}
			}
			
			if(! $found)
			{
				$this->m_last_error = 'You have selected an invalid option for input "'.$this->m_label.'"';
				
				return FALSE;
			}
		}
		
		//save values as array
		$this->m_value = array();
		foreach($elements_posted_checked as $element)
		{
			$this->m_value[] = htmlspecialchars($element);
			
			if(! $this->m_validator->isValid($element))
			{
				$this->m_invalid = TRUE;
				$this->m_last_error = $this->m_label . ': ' .$this->m_validator->m_last_error;
				
				return FALSE;
			}
		}
		
		return TRUE;
	}
	
	// DOCS INHERITHED  ========================================================
	function render()
	{
		$output = '<div class="form-element" '.$this->m_invalid.'>'. "\n";
		$output .= '<label for="id-'.$this->m_name.'">'.$this->m_label;
		if($this->m_mandatory)
		{
			$output .= '<b>*</b>';
		}
		$output .= '</label>' . "\n";
		$output .= '<select class="form-options';
		if($this->m_invalid)
		{
			$output .= ' form-element-invalid';
		}
		$output .= '"';
		
		$name = $this->m_name;
		if($this->m_multi_select)
		{
			$name = $this->m_name.'[]';
			$output .= 'multiple="multiple" size=5';
		}
		$output .= '" name="' . $name .'" ';
		$output .= ' id="id-' . $this->m_name . '">'."\n";
		
		//extract options 
		foreach($this->m_options as $opt_name => $opt_val)
		{
			$output .= '<option value="'.$opt_val.'"';
			
			//check if is selected
			if(is_array($this->m_value))
			{
				if(in_array($opt_val, $this->m_value))
				{
					$output .= ' selected="selected"';
				}
			}
			elseif($this->m_value === $opt_val)
			{
				$output .= ' selected="selected"';
			}
			
			$output .= '>'.$opt_name.'</option>'."\n";
		}
		$output .= '</select></div>'. "\n";
		
		return $output;
	}
};


/**
 * Represent a radio from element.
 */
class xFormElementRadio extends xFormElement
{
	var $m_checked;
	
	function xFormElementRadio($name,$label,$description,$value,$checked,$mandatory,$validator)
	{
		xFormElement::xFormElement($name,$label,$description,$value,$mandatory,$validator);
		$this->m_checked = $checked;
	}
	
	// DOCS INHERITHED  ========================================================
	function isValid($method)
	{
		if($this->getInputValue($method) === $this->m_value)
		{
			$this->m_checked = TRUE;
		}
		else
		{
			$this->m_checked = FALSE;
		}
		
		return xFormElement::isValid($method);
	}
		
	// DOCS INHERITHED  ========================================================
	function render()
	{
		$output = '<div class="form-element" '.$this->m_invalid.'>'. "\n";
		$output .= '<input class="form-radio" name="' . $this->m_name .'" '; 
		$output .= ' id="id-' . $this->m_name . '" value="'.$this->m_value.'"';
		if(!empty($this->m_checked))
		{	
			$output .= ' checked="checked" ';
		}
		$output .= ' type="radio"/>'."\n";
		$output .= '<label class="radio-label" for="id-'.$this->m_name.'">'.$this->m_label.'</label>' . "\n";
		$output .= '</div>'. "\n";
		
		return $output;
	}
};

/**
 * Represent a submit form element.
 */
class xFormSubmit extends xFormElement
{
	// DOCS INHERITHED  ========================================================
	function xFormSubmit($name,$value)
	{
		$this->xFormElement($name,NULL,NULL,$value,TRUE,new xInputValidator());
	}
	
	// DOCS INHERITHED  ========================================================
	function render()
	{
		$output = '<div class="form-element">'. "\n";
		$output .= '<input class="form-submit" name="' . $this->m_name .'" value="'.$this->m_value.'" type="submit"/>'."\n";
		$output .= '</div>'. "\n";
		return $output;
	}
};


/**
 * An utility object for returning multiple validation results.
 */
class xValidationData
{
	var $m_valid_data = array();
	var $m_errors = array();
	
	function isEmpty()
	{
		return empty($this->m_valid_data) && empty($this->m_errors);
	}
}


/**
 * An object for grouping togheter various form elements.
 */
class xFormGroup
{
	var $m_elements;
	var $m_label;
	
	function xFormGroup($label,$elements = array())
	{
		$this->m_label = $label;
		$this->m_elements = $elements;
	}
	
	/**
	 * Validates all the elements in the group.
	 *
	 * @return xValidationData
	 */
	function validate($method)
	{
		$data = new xValidationData();
		
		foreach($this->m_elements as $element)
		{
			if(! $element->isValid($method))
			{
				$data->m_errors[] = $element->m_last_error;
			}
			else
			{
				$data->m_valid_data[$element->m_name] = $element->getInputValue($method);
			}
		}
		
		return $data;
	}
	
	/**
	 * Render all elements in the group
	 *
	 * @return string The renderized group.
	 */
	function render()
	{
		$output = "<fieldset class=\"form-fieldset\"> \n";
		$output .= "<legend>" . $this->m_label . "</legend> \n";
		
		foreach($this->m_elements as $element)
		{
			$output .= $element->render();
		}
		
		$output .= "</fieldset> \n";
		
		return $output;
	}
};





/**
 * A form group specific for radio elements.
 */
class xFormRadioGroup extends xFormGroup
{
	function xFormRadioGroup($label = NULL,$elements = array())
	{
		xFormGroup::xFormGroup($label,$elements);
	}
	
	// DOCS INHERITHED  ========================================================
	function validate($method)
	{
		$data = new xValidationData();
		
		//see if the value correspond to one of the radios
		$in_array_elem = NULL;
		foreach($this->m_elements as $element)
		{
			if($element->m_value === $element->getInputValue($method))
			{
				$in_array_elem = $element;
				break;
			}
		}
		
		if($in_array_elem === NULL)
		{
			$data->m_errors[] = 'You have selected an invalid option for input '.$this->m_label;
		}
		else
		{
			$in_array_elem->m_checked = TRUE;
			if(! $in_array_elem->isValid($method))
			{
				$data->m_errors[] = $in_array_elem->m_last_error;
			}
			else
			{
				$data->m_valid_data[$in_array_elem->m_name] = $in_array_elem->getInputValue($method);
			}
		}
		
		return $data;
	}
};


/**
 * Represent a form that accept user input through post method.
 */
class xForm
{
	/**
	 * @var xFormElement
	 * @access public
	 */
	var $m_elements;
	
	/**
	 * @var string
	 * @access public
	 */
	var $m_action;
	
	/**
	 * @var string
	 * @access public
	 */
	var $m_method;
	
	function xForm($action,$method = 'POST',$elements = array())
	{
		$this->m_method = $method;
		$this->m_action = $action;
		$this->m_elements = $elements;
	}
	
	/**
	 * @access private
	 */
	function _addFormToken()
	{
		$token = md5(uniqid(rand(), TRUE));
		$_SESSION['form_token'][$token] = time();
		return $token;
	}
	
	/**
	 * Valid tokens are cleared automatically on success
	 *
	 * @access private
	 */
	function _checkFormToken($method)
	{
		if(!isset($_SESSION['form_token']))
		{
			return FALSE;
		}
		
		if(strcasecmp($method,'POST') == 0)
		{
			if(!isset($_POST['form_token']))
			{
				return FALSE;
			}
			
			if(! isset($_SESSION['form_token'][$_POST['form_token']]))
			{
				return FALSE;
			}
			
			$token = $_POST['form_token'];
		}
		else
		{
			if(!isset($_GET['form_token']))
			{
				return FALSE;
			}
			
			if(! isset($_SESSION['form_token'][$_GET['form_token']]))
			{
				return FALSE;
			}
			
			$token = $_GET['form_token'];
		}
		
		$token_age = time() - $_SESSION['form_token'][$token];
		if($token_age > 3600) //1 Hour
		{
			xForm::_removeFormToken($token);
			return FALSE;
		}
		
		xForm::_removeFormToken($token);
		
		return TRUE;
	}
	
	/**
	 * @access private
	 */
	function _removeFormToken($token)
	{
		unset($_SESSION['form_token'][$token]);
	}
	
	/**
	 * @access protected
	 */
	function _renderFormHeader()
	{
		//set a token against "Cross-Site Request Forgeries" attacks
		$token = xForm::_addFormToken();
		
		$output = '<form action="'. $this->m_action . '" method="'.$this->m_method.'">
		<input type="hidden" name="form_token" value="'.$token.'" />';
		
		return $output;
	}
	
	
	/**
	 * Render the whole form included its elements.
	 *
	 * @return string The renderized form.
	 */
	function render()
	{
		$output = xForm::_renderFormHeader();
		
		foreach($this->m_elements as $element)
		{
			$output .= $element->render();
		}
		
		$output .= "</form> \n";
		return $output;
	}
	
	
	/**
	 * Validate the input of all form elements.
	 *
	 * @return xValidationData
	 */
	function validate()
	{
		$data = new xValidationData();
		
		//first validate the token to prevent "Cross-Site Request Forgeries" attacks
		if(! xForm::_checkFormToken($this->m_method))
		{
			return $data;
		}
		
		foreach($this->m_elements as $element)
		{
			if(xanth_instanceof($element,'xFormGroup')) //is a group
			{
				$ret = $element->validate($this->m_method);
				
				$data->m_errors = array_merge($ret->m_errors,$data->m_errors);
				$data->m_valid_data = array_merge($ret->m_valid_data,$data->m_valid_data);
			}
			else //simple form element
			{
				$ret = $element->isValid($this->m_method);
				
				if($ret === FALSE)
				{
					$data->m_errors[] = $element->m_last_error;
				}
				else
				{
					$data->m_valid_data[$element->m_name] = $element->getInputValue($this->m_method);
				}
			}
		}
		
		return $data;
	}
};





/**
 * A utility class for handling input variables sent as an array (ex. named in form "test[index]").
 */
class xFormArrayInputWalker
{
	var $m_valid_data;
	
	
	function xFormArrayInputWalker($valid_data)
	{
		reset($valid_data);
		$this->m_valid_data = $valid_data;
	}
	
	
	/**
	 * Return the next array input with a specified name.
	 * The return is an array containing in order
	 * - Var value
	 * - First index (The first string between [])
	 * - Second index
	 * - ...
	 * You can use this in conjunction with the function list().
	 * 
	 * @return array(mixed) An array representing the next input array, or FALSE if no input remains to read.
	 */
	function next($name)
	{
		while(($value = current($this->m_valid_data)) !== FALSE)
		{
			$curr = key($this->m_valid_data);
			if(preg_match('#^'.$name.'((\[[A-Z0-9_-]*\])*)$#i',$curr,$pieces))
			{
				if(isset($pieces[1]))
				{
					if(preg_match_all('#\[([A-Z0-9_-]*)\]*#i',$pieces[1],$pieces))
					{
						array_unshift($pieces[1],$value);
						
						next($this->m_valid_data);
						return $pieces[1];
					}
				}
		    }
			
			next($this->m_valid_data);
		}
		
		return FALSE;
	}

}


?>