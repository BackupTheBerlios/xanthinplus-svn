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
	 * @return mixed The validated input, NULL on error. 
	 * An explanation of the error is available in the member m_last_error.
	 */
	function validate($input)
	{
		return $input;
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
	function validate($input)
	{
		if(empty($input))
		{
			return $input;
		}
		
		if($this->m_maxlength > 0 && strlen($input) > $this->m_maxlength)
		{
			$this->m_last_error = 'Field contains too much characters (max is '.$this->m_maxlength.')';
			return NULL;
		}

		return $input;
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
	function validate($input)
	{
		$input = xInputValidatorText::validate($input);
		if(empty($input))
		{
			return $input;
		}
		
		if(!preg_match($this->m_regex,$input))
		{
			$this->m_last_error = 'Field does not contain a valid input';
			return NULL;
		}
		
		return $input;
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
	function validate($input)
	{
		$input = xInputValidatorText::validate($input);
		if(empty($input))
		{
			return $input;
		}
		
		if(!xanth_valid_email($input))
		{
			$this->m_last_error = 'Field does not contain a valid email address';
			return NULL;
		}
		
		return $input;
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
	function validate($input)
	{
		$input = xInputValidatorText::validate($input);
		if(empty($input))
		{
			return $input;
		}
		
		if(!preg_match('#^[A-Z][A-Z0-9_-]{2,'.$this->m_maxlength.'}$#i',$input))
		{
			$this->m_last_error = 'Field does not contain a valid name';
			return NULL;
		}
		
		return $input;
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
	function validate($input)
	{
		if(empty($input))
		{
			return $input;
		}
		
		if(!is_numeric($input))
		{
			$this->m_last_error = 'Field must contain a valid number';
			return NULL;
		}
		
		
		return (int)$input;
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
	function getPostedValue()
	{
		if(isset($_POST[$this->m_name]))
		{
			return $_POST[$this->m_name];
		}
		
		return '';
	}
	
	/**
	 * Return the user input corresponding to a from element name.
	 *
	 * @return string The user input on success, an empty string otherwise.
	 * @static
	 */
	function getPostedValueByName($name)
	{
		if(isset($_POST[$name]))
		{
			return $_POST[$name];
		}
		
		return '';
	}
	
	/**
	 * Validate the user input corresponding to this form element, using the provided validator.
	 *
	 * @return mixed The validated inpout on success, NULL on error. 
	 * An explanation of the error is given in the member m_last_error.
	 */
	function validate()
	{
		$posted_value = $this->getPostedValue();
		if($posted_value === '')
		{
			if($this->m_mandatory)
			{
				$this->m_last_error = 'Field '.$this->m_label.' is mandatory';
				$this->m_invalid = TRUE;
				return NULL;
			}
			return '';
		}
		
		$ret = $this->m_validator->validate($posted_value);
		
		if($ret === NULL)
		{
			$this->m_invalid = TRUE;
			$this->m_last_error = $this->m_label . ': ' .$this->validator->m_last_error;
		}

		return $ret;
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
	function validate()
	{
		$this->m_value = htmlspecialchars($this->getPostedValue());
		return xFormElement::validate();
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
	function validate()
	{
		return xFormElement::validate();
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
	function validate()
	{
		$this->m_value = htmlspecialchars($this->getPostedValue());
		return xFormElement::validate();
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
	function validate()
	{
		//check for mandatory
		$posted_value = $this->getPostedValue();
		if($posted_value === '')
		{
			if($this->m_mandatory)
			{
				$this->m_last_error = 'Field '. $this->m_label.' is mandatory';
				return NULL;
			}
			return '';
		}
		
		//array or sigle value
		if(is_array($posted_value))
		{
			$ret = array();
			foreach($posted_value as $element)
			{
				$r = $this->m_validator->validate($element);
				if($r === NULL)
				{
					$this->m_invalid = TRUE;
					$this->m_last_error = $this->m_label . ': ' .$this->m_validator->m_last_error;
					return NULL;
				}
				$ret[] = $r;
			}
			return $ret;
		}
		else
		{
			$ret = $this->m_validator->validate($posted_value);
		
			if($ret === NULL)
			{
				$this->m_invalid = TRUE;
				$this->m_last_error = $this->m_label . ': ' .$this->m_validator->m_last_error;
			}

			return $ret;
		}
		
		return NULL;
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
	function validate()
	{
		if($this->getPostedValue() === $this->m_value)
		{
			$this->m_checked = TRUE;
		}
		else
		{
			$this->m_checked = FALSE;
		}
		
		return xFormElement::validate();
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
	function validate()
	{
		//check for mandatory
		$posted_value = $this->getPostedValue();
		if($posted_value === '')
		{
			if($this->m_mandatory)
			{
				$this->m_last_error = 'Field '.$this->m_label.' is mandatory';
				return NULL;
			}
			return '';
		}
		
		$elements_posted = array();
		//array or sigle value
		if(is_array($posted_value))
		{
			if(!$this->m_multi_select)
			{
				$this->m_last_error = 'Cannot select multiple values for field '.$this->m_label;
				return NULL;
			}
			else
			{
				$elements_posted = $posted_value;
			}
		}
		else
		{
			$elements_posted = array($posted_value);
		}
		
		//check if all values corresponds to at least an option
		$elements_posted_checked = array();
		foreach($elements_posted as $element_posted)
		{
			foreach($this->m_options as $opt_name => $opt_val)
			{
				if($opt_val === $element_posted)
				{
					$elements_posted_checked[] = $opt_val;
					break;
				}
			}
		}
		
		//now check
		if(count($elements_posted_checked) != count($elements_posted))
		{
			$this->m_last_error = 'You have selected an invalid option for input '.$this->m_label;
		}
		else
		{
			//save values as array and validate input
			$this->m_value = array();
			$ret = array();
			foreach($elements_posted_checked as $element)
			{
				$this->m_value[] = htmlspecialchars($element);
				
				$r = $this->m_validator->validate($element);
				if($r === NULL)
				{
					$this->m_invalid = TRUE;
					$this->m_last_error = $this->m_label . ': ' .$this->m_validator->m_last_error;
					return NULL;
				}
				$ret[] = $r;
			}
			
			//return array
			if($this->m_multi_select)
			{
				return $ret;
			}
			else //return as single value
			{
				return reset($ret);
			}
		}
		
		return NULL;
	}
	
	// DOCS INHERITHED  ========================================================
	function render()
	{
		$output = '<div class="form-element" '.$this->m_invalid.'>'. "\n";
		$output .= '<label for="id-'.$this->m_name.'">'.$this->m_label;
		$output .= '</label>' . "\n";
		$output .= '<select class="form-options';
		if($this->m_invalid)
		{
			$output .= ' form-element-invalid';
		}
		
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
	function validate()
	{
		if($this->getPostedValue() === $this->m_value)
		{
			$this->m_checked = TRUE;
		}
		else
		{
			$this->m_checked = FALSE;
		}
		
		return xFormElement::validate();
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
}


/**
 * An object for grouping togheter various form elements.
 */
class xFormGroup
{
	var $m_elements;
	var $m_label;
	
	function xFormGroup($elements = array(),$label = NULL)
	{
		$this->m_label = $label;
		$this->m_elements = $elements;
	}
	
	/**
	 * Validates all the elements in the group.
	 *
	 * @return xValidationData
	 */
	function validate()
	{
		$data = new xValidationData();
		
		foreach($this->m_elements as $element)
		{
			$ret = $element->validate();
			if($ret === NULL)
			{
				$data->m_errors[] = $element->m_last_error;
			}
			else
			{
				$data->m_valid_data[$element->m_name] = $ret;
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
	function xFormRadioGroup($elements = array(),$label = NULL)
	{
		xFormGroup::xFormGroup($elements,$label);
	}
	
	// DOCS INHERITHED  ========================================================
	function validate()
	{
		$data = new xValidationData();
		
		//see if the value correspond to one of the radios
		$in_array_elem = NULL;
		foreach($this->m_elements as $element)
		{
			if($element->m_value === $element->getPostedValue())
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
			$ret = $in_array_elem->validate();
			if($ret === NULL)
			{
				$data->m_errors[] = $in_array_elem->m_last_error;
			}
			else
			{
				$data->m_valid_data[$in_array_elem->m_name] = $ret;
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
	
	function xForm($action,$elements = array())
	{
		$this->m_action = $action;
		$this->m_elements = $elements;
	}
	
	/**
	 * Render the whole form included its elements.
	 *
	 * @return string The renderized form.
	 */
	function render()
	{
		//set a token against "Cross-Site Request Forgeries" attacks
		$token = md5(uniqid(rand(), TRUE));
		$_SESSION['form_token'] = $token;
		$_SESSION['form_token_time'] = time();
		$output = "<form action=\"". $this->m_action . "\" method=\"post\"> \n";
		$output .= "<input type=\"hidden\" name=\"form_token\" value=\"$token\" />";
		
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
		//first validate the token
		if(!(isset($_SESSION['form_token']) && isset($_POST['form_token']) && $_POST['form_token'] == $_SESSION['form_token']))
		{
			return $data;
		}
		
		$token_age = time() - $_SESSION['form_token_time'];
		if($token_age > 300)
		{
			return $data;
		}	
		
		foreach($this->m_elements as $element)
		{
			$ret = $element->validate();
			
			if(xanth_instanceof($element,'xFormGroup')) //is a group
			{
				$data->errors = array_merge($ret->m_errors,$data->m_errors);
				$data->valid_data = array_merge($ret->m_valid_data,$data->m_valid_data);
			}
			else //simple form element
			{
				if($ret === NULL)
				{
					$data->m_errors[] = $element->m_last_error;
				}
				else
				{
					$data->m_valid_data[$element->m_name] = $ret;
				}
			}
		}
		
		return $data;
	}
	
	
};

?>