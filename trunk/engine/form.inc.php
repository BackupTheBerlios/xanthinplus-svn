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
	var $mandatory;
	var $last_error = '';
	
	function xInputValidator($mandatory)
	{
		$this->mandatory = $mandatory;
	}
	
	/**
	 * Validates the element by filtering the input and returns the input or NULL otherwise
	 */
	function validate($element)
	{
		$this->last_error = '';
		
		//if the variable is not defined
		if(empty($_POST[$element->name]))
		{
			//if the field is mandatory
			if($this->mandatory)
			{
				$this->last_error = 'Field '.$element->label.' is mandatory';
				return NULL;
			}
			return '';
		}
		
		return $_POST[$element->name];
	}
}

/**
*
*/
class xInputValidatorText extends xInputValidator
{
	var $maxlength;
	
	function xInputValidatorText($maxlength,$mandatory)
	{
		xInputValidator::xInputValidator($mandatory);
		$this->maxlength = $maxlength;
	}
	
	/**
	 * Validates the element by filtering the input and returns true on success.
	 */
	function validate($element)
	{
		$input = xInputValidator::validate($element);
		if($input === NULL)
		{
			return NULL;
		}
		
		if($this->maxlength > 0 && strlen($input) > $this->maxlength)
		{
			$this->last_error = 'Field '.$element->label.' contains too much characters (max is '.$this->maxlength.')';
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
	function xInputValidatorText($maxlength,$mandatory)
	{
		xInputValidatorText::xInputValidatorText($maxlength,$mandatory);
	}
	
	/**
	  * Validates the element by filtering the input and returns true on success.
	 */
	function validate($element)
	{
		$input = xInputValidatorText::validate($element);
		if($input === NULL)
		{
			return NULL;
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
	
	function xInputValidatorTextRegex($regex,$maxlength,$mandatory)
	{
		xInputValidatorText::xInputValidatorText($maxlength,$mandatory);
		$this->regex = regex;
	}
	
	/**
	  * Validates the element by filtering the input and returns true on success.
	 */
	function validate($element)
	{
		$input = xInputValidatorText::validate($element);
		if($input === NULL)
		{
			return NULL;
		}
		
		if(!preg_match($this->regex,$input))
		{
			$this->last_error = 'Field '.$element->label.' does not contain a valid input';
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
	var $regex;
	
	function xInputValidatorTextEmail($mandatory)
	{
		xInputValidatorText::xInputValidatorText('',0,$mandatory);
		$this->regex = regex;
	}
	
	/**
	  * Validates the element by filtering the input and returns true on success.
	 */
	function validate($element)
	{
		$input = xInputValidatorText::validate($element);
		if($input === NULL)
		{
			return NULL;
		}
		
		if(!xanth_valid_email($input))
		{
			$this->last_error = 'Field '.$element->label.' does not contain a valid email address';
			return NULL;
		}
		
		return $input;
	}
}


/**
*
*/
class xInputValidatorTextUsermame extends xInputValidatorText
{
	var $regex;
	
	function xInputValidatorTextUsermame($mandatory)
	{
		xInputValidatorText::xInputValidatorText($maxlength,$mandatory);
		$this->regex = regex;
	}
	
	/**
	  * Validates the element by filtering the input and returns true on success.
	 */
	function validate($element)
	{
		$input = xInputValidatorText::validate($element);
		if($input === NULL)
		{
			return NULL;
		}
		
		if(!preg_match('#^[A-Z][A-Z0-9_-]{2,'.$this->maxlenght.'}$#i',$input))
		{
			$this->last_error = 'Field '.$element->label.' does not contain a valid username';
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
	function xInputValidatorInteger($mandatory)
	{
		xInputValidator::xInputValidator($mandatory);
	}
	
		/**
	  * Validates the element by filtering the input and returns true on success.
	 */
	function validate($element)
	{
		$input = xInputValidator::validate($element);
		if($input === NULL)
		{
			return NULL;
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
	var $invalid = FALSE;
	var $enabled = TRUE;
	
	function xFormElement($name,$label,$description,$value,$validator)
	{
		$this->name = $name;
		$this->label = $label;
		$this->description = $description;
		$this->value = $value;
		
		if(empty($validator))
		{
			$this->validator = new xInputValidator(FALSE);
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
	
	/**
	 *
	*/
	function validate()
	{
		$ret = $this->validator->validate($this);
		if($ret === NULL)
		{
			$this->invalid = TRUE;
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
	function xFormElementTextField($name,$label,$description,$value,$validator)
	{
		xFormElement::xFormElement($name,$label,$description,$value,$validator);
	}
	
	function validate()
	{
		$this->value = htmlspecialchars($this->get_posted_value());
		return xFormElement::validate();
	}
	
	
	function render()
	{
		$output = '<div class="form-element" '.$this->invalid.'>'. "\n";
		$output .= '<label for="id-'.$this->name.'">'.$this->label.':</label>' . "\n";
		$output .= '<input';
		if(isset($this->validator->maxlength))
		{
			$output .= ' maxlength="' . $this->validator->maxlength . '" ';
		}
		$output .= ' name="' . $this->name .'" '; 
		$output .= ' id="id-' . $this->name . '" value="'.$this->value.'" type="text">'."\n";
		$output .= '</div>'. "\n";
		return $output;
	}
};

/**
*
*/
class xFormElementPassword extends xFormElement
{
	function xFormElementPassword($name,$label,$description,$validator)
	{
		xFormElement::xFormElement($name,$label,$description,'',$validator);
	}
	
	function validate()
	{
		return xFormElement::validate();
	}
	
	
	function render()
	{
		$output = '<div class="form-element" '.$this->invalid.'>'. "\n";
		$output .= '<label for="id-'.$this->name.'">'.$this->label.':</label>' . "\n";
		$output .= '<input';
		if(isset($this->validator->maxlength))
		{
			$output .= ' maxlength="' . $this->validator->maxlength . '" ';
		}
		$output .= ' name="' . $this->name .'" '; 
		$output .= ' id="id-' . $this->name . '" value="'.$this->value.'" type="password">'."\n";
		$output .= '</div>'. "\n";
		
		return $output;
	}
};

/**
*
*/
class xFormElementTextArea extends xFormElement
{
	function xFormElementTextArea($name,$label,$description,$value,$validator)
	{
		xFormElement::xFormElement($name,$label,$description,$value,$validator);
	}
	
	function validate()
	{
		$this->value = htmlspecialchars($this->get_posted_value());
		return xFormElement::validate();
	}
	
	function render()
	{
		$output = '<div class="form-element" '.$this->invalid.'>'. "\n";
		$output .= '<label for="id-'.$this->name.'">'.$this->label.':</label>' . "\n";
		$output .= '<textarea name="' . $this->name .'" '; 
		$output .= ' id="id-' . $this->name . '">'. $this->value . '</textarea>'."\n";
		$output .= '</div>'. "\n";
		return $output;
	}
};

/**
*
*/
class xFormElementRadio extends xFormElement
{
	var $checked;
	
	function xFormElementRadio($name,$label,$description,$value,$checked,$validator)
	{
		xFormElement::xFormElement($name,$label,$description,$value,$validator);
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
		$output .= '<label for="id-'.$this->name.'">'.$this->label.':</label>' . "\n";
		$output .= '<input name="' . $this->name .'" '; 
		$output .= ' id="id-' . $this->name . '" value="'.$this->value.'"';
		if(!empty($this->checked))
		{	
			$output .= ' checked="checked" ';
		}
		$output .= ' type="radio">'."\n";
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
		$this->xFormElement($name,NULL,NULL,$value,new xInputValidator(TRUE));
	}
	
	function render()
	{
		$output = '<div class="form-element">'. "\n";
		$output .= '<input name="' . $this->name .'" value="'.$this->value.'" type="submit">'."\n";
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
		
	}
	
	function render()
	{
		$output = "<fieldset> \n";
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
				$data->errors[] = $$in_array_elem->validator->last_error;
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
					$data->errors[] = $element->validator->last_error;
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