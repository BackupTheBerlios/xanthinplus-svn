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

/**
*
*/
class xFormValidator
{
	var $mandatory;
	
	function xFormValidator($mandatory)
	{
		$this->namdatory = $mandatory;
	}
	
	/**
	 *
	 */
	function get_input_validate($element)
	{
		//if the variable is not defined
		if(!isset($_POST[$element->name]))
		{
			//if the field is mandatory
			if($this->mandatory)
			{
				$element->invalid = TRUE;
				$element->value = 'Field '.$this->groups[i]->elements[j]->name.' is mandatory';
			}
			else
			{
				$outdata->valid_data[$this->groups[i]->elements[j]->name] = '';
			}
		}
	}
}

/**
*
*/
class xFormValidatorText extends xFormValidator
{
	var $maxlength;
	
	function xFormValidatorText($maxlength,$mandatory)
	{
		xFormValidator::xFormValidator($mandatory);
		$this->maxlength = $maxlength;
	}
	
}


/**
*
*/
class xFormValidatorTextNoTags extends xFormValidatorText
{
	function xFormValidatorText($maxlength,$mandatory)
	{
		xFormValidatorText::xFormValidatorText($maxlength,$mandatory);
	}
}

/**
*
*/
class xFormValidatorTextRegex extends xFormValidatorText
{
	var $regex;
	
	function xFormValidatorTextRegex($regex,$maxlength,$mandatory)
	{
		xFormValidatorText::xFormValidatorText($maxlength,$mandatory);
		$this->regex = regex;
	}
}

/**
*
*/
class xFormValidatorInteger extends xFormValidator
{
	function xFormValidatorInteger($min_val,$max_val,$mandatory)
	{
		xFormValidator::xFormValidator($mandatory);
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
	
	function xFormElement($name,$label,$description,$value,$validator)
	{
		$this->name = $name;
		$this->label = $label;
		$this->description = $description;
		$this->validator = $validator;
		$this->value = $value;
	}
};


/**
*
*/
class xFormTextField extends xFormElement
{
	function xFormTextField($name,$label,$description,$value,$validator)
	{
		xFormElement::xFormElement($name,$label,$description,$value,$validator);
	}
	
	function render()
	{
		$output = '<input maxlength="' . $this->validator->maxlength . '" ';
		$output .= ' name="' . $this->name .'" '; 
		$output .= ' id="id-' . $this->name . '" value="'.$this->value.'" type="text">'."\n";
		return $output;
	}
};

/**
*
*/
class xFormTextArea extends xFormElement
{
	function xFormTextArea($name,$label,$description,$value,$validator)
	{
		xFormElement::xFormElement($name,$label,$description,$value,$validator);
	}
	
	function render()
	{
		$output = '<textarea name="' . $this->name .'" '; 
		$output .= ' id="id-' . $this->name . '">'. $this->value . '</textarea>'."\n";
		return $output;
	}
};

/**
*
*/
class xFormComboBox extends xFormElement
{
	var $values;
	
	function xFormTextField($name,$label,$description,$values,$value,$validator)
	{
		xFormElement::xFormElement($name,$label,$description,$value,$validator);
		$this->values = $values;
	}
};

/**
*
*/
class xFormSubmit extends xFormElement
{
	function xFormSubmit($name,$value)
	{
		$this->xFormElement($name,NULL,NULL,$value,NULL);
	}
	
	function render()
	{
		$output = '<input name="' . $this->name .'" value="'.$this->value.'" type="submit">'."\n";
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
	
	function xFormGroup($elements,$label = NULL)
	{
		$this->label = $label;
		$this->elements = $elements;
	}
	
	function render()
	{
		
	}
};

/**
*
*/
class xForm
{
	var $elements;
	var $action;
	
	function xForm($action,$groups)
	{
		$this->action = $action;
		$this->groups = $groups;
	}
	
	/**
	 *
	*/
	function render()
	{
		$output = "<form action=\"". $this->action . "\" method=\"post\"> \n";

		foreach($this->groups as $group)
		{
			if(!empty($group->label))
			{
				$output .= "<fieldset> \n";
				$output .= "<legend>" . $group->label . "</legend> \n";
			}
			
			foreach($group->elements as $element)
			{
				if($element->invalid)
					$invalid = 'invalid';
				else
					$invalid = '';
					
				$output .= '<div class="form-element" '.$invalid.'>'. "\n";
				
				if(!empty($element->label))
				{
					$output .= '<label for="id-'.$element->name.'">'.$element->label.'</label>' . "\n";
				}
				
				$output .= $element->render();
				
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
	function xanth_form_validate_data()
	{
		$outdata = new xFormData(array(),array());

		for($i = 0;$i < count($this->groups);$i++)
		{
			for($j = 0;$j < count($this->groups[i]->elements); $j++)
			{	
				//if the variable is not defined
				if(!isset($_POST[$this->groups[i]->elements[j]->name]))
				{
					//if the field is mandatory
					if($this->groups[i]->elements[j]->mandatory)
					{
						$this->groups[i]->elements[j]->invalid = TRUE;
						$outdata->invalid_data[$form->groups[i]->elements[j]->name] = 
							'Field '.$this->groups[i]->elements[j]->name.' is mandatory';
					}
					else
					{
						$outdata->valid_data[$this->groups[i]->elements[j]->name] = '';
					}
				}
				else //variable is defined
				{
					$elem_value = $_POST[$this->groups[i]->elements[j]->name];
					//if no validator defined
					if(empty($validator))
					{
						$outdata[$this->groups[i]->elements[j]->name] = $elem_value;
					}
					else
					{
						switch($this->groups[i]->elements[j]->validator->data_type)
						{
						case FORM_DATA_TYPE_TEXT_NO_TAGS:
							$elem_value = strip_tags($elem_value);
						case FORM_DATA_TYPE_TEXT_WITH_TAGS:
							if(!empty($this->groups[i]->elements[j]->validator->maxlength) && 
								strlen($elem_value) > $this->groups[i]->elements[j]->validator->maxlength)
							{
								$this->groups[i]->elements[j]->invalid = TRUE;
								$outdata->invalid_data[$form->groups[i]->elements[j]->name] = 
									'Field '.$this->groups[i]->elements[j]->name.' contains too much characters (max is '
									.$this->groups[i]->elements[j]->validator->maxlength.')';
							}
							else
							{
								$outdata->valid_data[$this->groups[i]->elements[j]->name] = $elem_value;
							}
							break;
						case FORM_DATA_TYPE_INTEGER:
							if(!is_numeric($elem_value))
							{
								$this->groups[i]->elements[j]->invalid = TRUE;
								$outdata->invalid_data[$form->groups[i]->elements[j]->name] = 
									'Field '.$this->groups[i]->elements[j]->name.' must be an integer value';
							}
							else
							{
								$outdata->valid_data[$this->groups[i]->elements[j]->name] = (int) $elem_value;
							}
							break;
						default:
						}
					} //else validator defined
				} //else variable defined
			}//for elements
		} //for groups
		
		return $outdata;
	}
	
	
};


/**
*
*/



?>