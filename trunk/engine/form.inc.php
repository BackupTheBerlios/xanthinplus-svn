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
class xInputValidator
{
	var $last_error = '';
	
	function xInputValidator()
	{}
	
	function validate($input)
	{
		return $input;
	}
}

/**
*
*/
class xInputValidatorText extends xInputValidator
{
	var $maxlength;
	
	function xInputValidatorText($maxlength)
	{
		xInputValidator::xInputValidator();
		$this->maxlength = $maxlength;
	}
	
	/**
	 * Validates the element by filtering the input and returns true on success.
	 */
	function validate($input)
	{
		if(empty($input))
		{
			return $input;
		}
		
		if($this->maxlength > 0 && strlen($input) > $this->maxlength)
		{
			$this->last_error = 'Field contains too much characters (max is '.$this->maxlength.')';
			return NULL;
		}

		return $input;
	}
}


/**
*
*/
class xInputValidatorTextNoTags extends xInputValidatorText
{
	function xInputValidatorText($maxlength)
	{
		xInputValidatorText::xInputValidatorText($maxlength);
	}
	
	/**
	  * Validates the element by filtering the input and returns true on success.
	 */
	function validate($input)
	{
		$input = xInputValidatorText::validate($input);
		if(empty($input))
		{
			return $input;
		}

		$input = htmlspecialchars($input);
		return $input;
	}
}


/**
*
*/
class xInputValidatorTextRegex extends xInputValidatorText
{
	var $regex;
	
	function xInputValidatorTextRegex($regex,$maxlength)
	{
		xInputValidatorText::xInputValidatorText($maxlength);
		$this->regex = regex;
	}
	
	/**
	  * Validates the element by filtering the input and returns true on success.
	 */
	function validate($input)
	{
		$input = xInputValidatorText::validate($input);
		if(empty($input))
		{
			return $input;
		}
		
		if(!preg_match($this->regex,$input))
		{
			$this->last_error = 'Field does not contain a valid input';
			return NULL;
		}
		
		return $input;
	}
}


/**
*
*/
class xInputValidatorTextEmail extends xInputValidatorText
{
	function xInputValidatorTextEmail()
	{
		xInputValidatorText::xInputValidatorText('',0);
	}
	
	/**
	  * Validates the element by filtering the input and returns true on success.
	 */
	function validate($input)
	{
		$input = xInputValidatorText::validate($input);
		if(empty($input))
		{
			return $input;
		}
		
		if(!xanth_valid_email($input))
		{
			$this->last_error = 'Field does not contain a valid email address';
			return NULL;
		}
		
		return $input;
	}
}


/**
*
*/
class xInputValidatorTextNameId extends xInputValidatorText
{
	function xInputValidatorTextNameId($maxlength)
	{
		xInputValidatorText::xInputValidatorText($maxlength);
	}
	
	/**
	  * Validates the element by filtering the input and returns true on success.
	 */
	function validate($input)
	{
		$input = xInputValidatorText::validate($input);
		if(empty($input))
		{
			return $input;
		}
		
		if(!preg_match('#^[A-Z][A-Z0-9_-]{2,'.$this->maxlength.'}$#i',$input))
		{
			$this->last_error = 'Field does not contain a valid name';
			return NULL;
		}
		
		return $input;
	}
}


/**
*
*/
class xInputValidatorInteger extends xInputValidator
{	
	function xInputValidatorInteger()
	{
		xInputValidator::xInputValidator();
	}
	
	/**
	  * Validates the element by filtering the input and returns true on success.
	 */
	function validate($input)
	{
		if(empty($input))
		{
			return $input;
		}
		
		if(!is_numeric($input))
		{
			$this->last_error = 'Field '.$element->label.' must contain a valid number';
			return NULL;
		}
		
		
		return (int)$input;
	}
}

/**
*
*/
class xFormElement
{
	var $name;
	var $label;
	var $description;
	var $value;
	var $validator;
	var $mandatory;
	var $invalid = FALSE;
	var $enabled = TRUE;
	var $last_error = '';
	
	function xFormElement($name,$label,$description,$value,$mandatory,$validator)
	{
		$this->name = $name;
		$this->label = $label;
		$this->description = $description;
		$this->value = $value;
		$this->mandatory = $mandatory;
		
		if(empty($validator))
		{
			$this->validator = new xInputValidatorText(0);
		}
		else
		{
			$this->validator = $validator;
		}
	}
	
	function get_posted_value()
	{
		if(isset($_POST[$this->name]))
		{
			return $_POST[$this->name];
		}
		
		return '';
	}
	
	function get_posted_value_by_name($name)
	{
		if(isset($_POST[$name]))
		{
			return $_POST[$name];
		}
		
		return '';
	}
	
	/**
	 *
	*/
	function validate()
	{
		$posted_value = $this->get_posted_value();
		if($posted_value === '')
		{
			if($this->mandatory)
			{
				$this->last_error = 'Field '.$this->label.' is mandatory';
				$this->invalid = TRUE;
				return NULL;
			}
			return '';
		}
		
		$ret = $this->validator->validate($posted_value);
		
		if($ret === NULL)
		{
			$this->invalid = TRUE;
			$this->last_error = $this->label . ': ' .$this->validator->last_error;
		}

		return $ret;
	}
	
	/**
	 *
	*/
	function render()
	{}
};


/**
*
*/
class xFormElementTextField extends xFormElement
{
	function xFormElementTextField($name,$label,$description,$value,$mandatory,$validator)
	{
		xFormElement::xFormElement($name,$label,$description,$value,$mandatory,$validator);
	}
	
	function validate()
	{
		$this->value = htmlspecialchars($this->get_posted_value());
		return xFormElement::validate();
	}
	
	
	function render()
	{
		$output = '<div class="form-element" '.$this->invalid.'>'. "\n";
		$output .= '<label for="id-'.$this->name.'">'.$this->label;
		if($this->mandatory)
		{
			$output .= '<b>*</b>';
		}
		$output .= '</label>' . "\n";
		$output .= '<input';
		if($this->validator->maxlength > 0 )
		{
			$output .= ' maxlength="' . $this->validator->maxlength . '" ';
		}
		$output .= ' class="form-textfield';
		if($this->invalid)
		{
			$output .= ' form-element-invalid';
		}
		$output .= '" name="' . $this->name .'" '; 
		$output .= ' id="id-' . $this->name . '" value="'.$this->value.'" type="text"/>'."\n";
		$output .= '</div>'. "\n";
		return $output;
	}
};

/**
*
*/
class xFormElementPassword extends xFormElement
{
	function xFormElementPassword($name,$label,$description,$mandatory,$validator)
	{
		xFormElement::xFormElement($name,$label,$description,'',$mandatory,$validator);
	}
	
	function validate()
	{
		return xFormElement::validate();
	}
	
	
	function render()
	{
		$output = '<div class="form-element" '.$this->invalid.'>'. "\n";
		$output .= '<label for="id-'.$this->name.'">'.$this->label;
		if($this->mandatory)
		{
			$output .= '<b>*</b>';
		}
		$output .= '</label>' . "\n";
		$output .= '<input';
		if($this->validator->maxlength > 0 )
		{
			$output .= ' maxlength="' . $this->validator->maxlength . '" ';
		}
		$output .= ' class="form-password';
		if($this->invalid)
		{
			$output .= ' form-element-invalid';
		}
		$output .= '" name="' . $this->name .'" '; 
		$output .= ' id="id-' . $this->name . '" value="'.$this->value.'" type="password"/>'."\n";
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
	
	function validate()
	{
		$this->value = htmlspecialchars($this->get_posted_value());
		return xFormElement::validate();
	}
	
	function render()
	{
		$output = '<div class="form-element" '.$this->invalid.'>'. "\n";
		$output .= '<label for="id-'.$this->name.'">'.$this->label;
		if($this->mandatory)
		{
			$output .= '<b>*</b>';
		}
		$output .= '</label>' . "\n";
		$output .= '<textarea class="form-textarea';
		if($this->invalid)
		{
			$output .= ' form-element-invalid';
		}
		$output .= '" name="' . $this->name .'" '; 
		if($this->validator->maxlength > 0 )
		{
			$output .= ' maxlength="' . $this->validator->maxlength . '" ';
		}
		$output .= ' id="id-' . $this->name . '" rows="8" cols="50">'. $this->value . '</textarea>'."\n";
		$output .= '</div>'. "\n";
		return $output;
	}
};

/**
*
*/
class xFormElementHidden extends xFormElement
{
	var $multiple;
	
	function xFormElementHidden($name,$label,$value,$multiple,$validator)
	{
		xFormElement::xFormElement($name,$label,NULL,$value,TRUE,$validator);
		$this->multiple = $multiple;
	}
	
	function validate()
	{
		//check for mandatory
		$posted_value = $this->get_posted_value();
		if($posted_value === '')
		{
			if($this->mandatory)
			{
				$this->last_error = 'Field '. $this->label.' is mandatory';
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
				$r = $this->validator->validate($element);
				if($r === NULL)
				{
					$this->invalid = TRUE;
					$this->last_error = $this->label . ': ' .$this->validator->last_error;
					return NULL;
				}
				$ret[] = $r;
			}
			return $ret;
		}
		else
		{
			$ret = $this->validator->validate($posted_value);
		
			if($ret === NULL)
			{
				$this->invalid = TRUE;
				$this->last_error = $this->label . ': ' .$this->validator->last_error;
			}

			return $ret;
		}
		
		return NULL;
	}
	
	function render()
	{
		$output = '<div class="form-element" '.$this->invalid.'>'. "\n";
		$output .= '<input class="form-hidden';
		$output .= '" name="' . $this->name;
		if($this->multiple)
		{
			$output .= '[]';
		}
		$output .= '" value="'.$this->value.'" '; 
		$output .= ' id="id-' . $this->name . '" type="hidden" />'."\n";
		$output .= '</div>'. "\n";
		return $output;
	}
};

/**
*
*/
class xFormElementCheckbox extends xFormElement
{
	var $checked;
	
	function xFormElementCheckbox($name,$label,$description,$value,$checked,$mandatory,$validator)
	{
		xFormElement::xFormElement($name,$label,$description,$value,$mandatory,$validator);
		$this->checked = $checked;
	}
	
	function validate()
	{
		if($this->get_posted_value() === $this->value)
		{
			$this->checked = TRUE;
		}
		else
		{
			$this->checked = FALSE;
		}
		
		return xFormElement::validate();
	}
	
	function render()
	{
		$output = '<div class="form-element" '.$this->invalid.'>'. "\n";
		$output .= '<input';
		$output .= ' class="form-checkbox';
		if($this->invalid)
		{
			$output .= ' form-element-invalid';
		}
		$output .= '" name="' . $this->name .'" ';
		if(!empty($this->checked))
		{	
			$output .= ' checked="checked" ';
		}
		$output .= ' id="id-' . $this->name . '" value="'.$this->value.'" type="checkbox"/>'."\n";
		$output .= '<label class="checkbox-label" for="id-'.$this->name.'">'.$this->label;
		$output .= '</label>' . "\n";
		$output .= '</div>'. "\n";
		
		return $output;
	}
};


/**
*
*/
class xFormElementOptions extends xFormElement
{
	var $multi_select;
	var $options;
	
	function xFormElementOptions($name,$label,$description,$value,$options,$multi_select,$mandatory,$validator)
	{
		xFormElement::xFormElement($name,$label,$description,$value,$mandatory,$validator);
		$this->options = $options;
		$this->multi_select = $multi_select;
	}
	
	/**
	*
	*/
	function validate()
	{
		//check for mandatory
		$posted_value = $this->get_posted_value();
		if($posted_value === '')
		{
			if($this->mandatory)
			{
				$this->last_error = 'Field '.$this->label.' is mandatory';
				return NULL;
			}
			return '';
		}
		
		$elements_posted = array();
		//array or sigle value
		if(is_array($posted_value))
		{
			if(!$this->multi_select)
			{
				$this->last_error = 'Cannot select multiple values for field '.$this->label;
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
			foreach($this->options as $opt_name => $opt_val)
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
			$this->last_error = 'You have selected an invalid option for input '.$this->label;
		}
		else
		{
			//save values as array and validate input
			$this->value = array();
			$ret = array();
			foreach($elements_posted_checked as $element)
			{
				$this->value[] = htmlspecialchars($element);
				
				$r = $this->validator->validate($element);
				if($r === NULL)
				{
					$this->invalid = TRUE;
					$this->last_error = $this->label . ': ' .$this->validator->last_error;
					return NULL;
				}
				$ret[] = $r;
			}
			
			//return array
			if($this->multi_select)
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
	
	/**
	*
	*/
	function render()
	{
		$output = '<div class="form-element" '.$this->invalid.'>'. "\n";
		$output .= '<label for="id-'.$this->name.'">'.$this->label;
		$output .= '</label>' . "\n";
		$output .= '<select class="form-options';
		if($this->invalid)
		{
			$output .= ' form-element-invalid';
		}
		
		$name = $this->name;
		if($this->multi_select)
		{
			$name = $this->name.'[]';
			$output .= 'multiple="multiple" size=5';
		}
		$output .= '" name="' . $name .'" ';
		$output .= ' id="id-' . $this->name . '">'."\n";
		
		//extract options 
		foreach($this->options as $opt_name => $opt_val)
		{
			$output .= '<option value="'.$opt_val.'"';
			
			//check if is selected
			if(is_array($this->value))
			{
				if(in_array($opt_val, $this->value))
				{
					$output .= ' selected="selected"';
				}
			}
			elseif($this->value === $opt_val)
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
*
*/
class xFormElementRadio extends xFormElement
{
	var $checked;
	
	function xFormElementRadio($name,$label,$description,$value,$checked,$mandatory,$validator)
	{
		xFormElement::xFormElement($name,$label,$description,$value,$mandatory,$validator);
		$this->checked = $checked;
	}
	
	function validate()
	{
		if($this->get_posted_value() === $this->value)
		{
			$this->checked = TRUE;
		}
		else
		{
			$this->checked = FALSE;
		}
		
		return xFormElement::validate();
	}
		
		
	function render()
	{
		$output = '<div class="form-element" '.$this->invalid.'>'. "\n";
		$output .= '<input class="form-radio" name="' . $this->name .'" '; 
		$output .= ' id="id-' . $this->name . '" value="'.$this->value.'"';
		if(!empty($this->checked))
		{	
			$output .= ' checked="checked" ';
		}
		$output .= ' type="radio"/>'."\n";
		$output .= '<label class="radio-label" for="id-'.$this->name.'">'.$this->label.'</label>' . "\n";
		$output .= '</div>'. "\n";
		return $output;
	}
};

/**
*
*/
class xFormSubmit extends xFormElement
{
	function xFormSubmit($name,$value)
	{
		$this->xFormElement($name,NULL,NULL,$value,TRUE,new xInputValidator());
	}
	
	function render()
	{
		$output = '<div class="form-element">'. "\n";
		$output .= '<input class="form-submit" name="' . $this->name .'" value="'.$this->value.'" type="submit"/>'."\n";
		$output .= '</div>'. "\n";
		return $output;
	}
};

/**
*
*/
class xFormGroup
{
	var $elements;
	var $label;
	
	function xFormGroup($elements = array(),$label = NULL)
	{
		$this->label = $label;
		$this->elements = $elements;
	}
	
	function validate()
	{
		$data = new xValidationData();
		
		foreach($this->elements as $element)
		{
			$ret = $element->validate();
			if($ret === NULL)
			{
				$data->errors[] = $element->last_error;
			}
			else
			{
				$data->valid_data[$element->name] = $ret;
			}
		}
		return $data;
	}
	
	function render()
	{
		$output = "<fieldset class=\"form-fieldset\"> \n";
		$output .= "<legend>" . $this->label . "</legend> \n";
		
		foreach($this->elements as $element)
		{
			$output .= $element->render();
		}
		
		$output .= "</fieldset> \n";
		
		return $output;
	}
};

/**
*
*/
class xValidationData
{
	var $valid_data = array();
	var $errors = array();
}



/**
*
*/
class xFormRadioGroup extends xFormGroup
{
	function xFormRadioGroup($elements = array(),$label = NULL)
	{
		xFormGroup::xFormGroup($elements,$label);
	}
	
	function validate()
	{
		$data = new xValidationData();
		
		//see if the value correspond to one of the radios
		$in_array_elem = NULL;
		foreach($this->elements as $element)
		{
			if($element->value === $element->get_posted_value())
			{
				$in_array_elem = $element;
				break;
			}
		}
		
		if($in_array_elem === NULL)
		{
			$data->errors[] = 'You have selected an invalid option for input '.$this->label;
		}
		else
		{
			$in_array_elem->checked = TRUE;
			$ret = $in_array_elem->validate();
			if($ret === NULL)
			{
				$data->errors[] = $in_array_elem->last_error;
			}
			else
			{
				$data->valid_data[$in_array_elem->name] = $ret;
			}
		}
		
		return $data;
	}
};


/**
*
*/
class xForm
{
	var $elements;
	var $action;
	
	function xForm($action,$elements = array())
	{
		$this->action = $action;
		$this->elements = $elements;
	}
	
	/**
	 *
	*/
	function render()
	{
		//set a token against Cross-Site Request Forgeries attacks
		$token = md5(uniqid(rand(), TRUE));
		$_SESSION['form_token'] = $token;
		$_SESSION['form_token_time'] = time();
		$output = "<form action=\"". $this->action . "\" method=\"post\"> \n";
		$output .= "<input type=\"hidden\" name=\"form_token\" value=\"$token\" />";
		
		foreach($this->elements as $element)
		{
			$output .= $element->render();
		}
		
		$output .= "</form> \n";
		return $output;
	}
	
	
	/**
	 * Return an xValidationData object
	 */
	function validate_input()
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
		
		foreach($this->elements as $element)
		{
			$ret = $element->validate();
			
			if(xanth_instanceof($element,'xFormGroup')) //is a group
			{
				$data->errors = array_merge($ret->errors,$data->errors);
				$data->valid_data = array_merge($ret->valid_data,$data->valid_data);
			}
			else //simple form element
			{
				if($ret === NULL)
				{
					$data->errors[] = $element->last_error;
				}
				else
				{
					$data->valid_data[$element->name] = $ret;
				}
			}
		}
		
		return $data;
	}
	
	
};


/**
*
*/



?>