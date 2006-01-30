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
class xanthFormElement
{
	var $name;
	var $label;
	var $description;
	var $mandatory;
	
	function xanthFormElement($name,$label,$description,$mandatory = FALSE)
	{
		$this->set_name($name);
		$this->set_label($label);
		$this->set_description($description);
		$this->set_mandatory($mandatory);
	}
	
	function set_name($name)
	{$this->name = strip_tags($name);}
	
	function set_label($label)
	{$this->label = strip_tags($label);}
	
	function set_description($description)
	{$this->description = strip_tags($description);}
	
	function set_mandatory($mandatory)
	{$this->mandatory = $mandatory;}
	
	function get_name()
	{return $this->name;}
	
	function get_label()
	{return $this->label;}
	
	function get_description()
	{return $this->description;}
	
	function get_mandatory()
	{return $this->mandatory;}
};

/**
*
*/
class xanthFormTextField extends xanthFormElement
{
	var $default_value;
	var $maxlength;
	
	function xanthFormTextField($name,$label,$description,$default_value,$maxlength = NULL,$mandatory = FALSE)
	{
		$this->xanthFormElement($name,$label,$description,$mandatory);
		$this->set_default_value($default_value);
		$this->set_maxlength($maxlength);
	}
	
	function set_default_value($default_value)
	{$this->default_value = strip_tags($default_value);}
	
	function set_maxlength($maxlength)
	{$this->maxlength = $maxlength;}
	
	function get_default_value()
	{return $this->default_value;}
	
	function get_maxlength()
	{return $this->maxlength;}
};

class xanthFormTextArea extends xanthFormElement
{
	var $default_value;
	var $maxlength;
	
	function xanthFormTextArea($name,$label,$description,$default_value,$maxlength = NULL,$mandatory = FALSE)
	{
		$this->xanthFormElement($name,$label,$description,$mandatory);
		$this->set_default_value($default_value);
		$this->set_maxlength($maxlength);
	}
	
	function set_default_value($default_value)
	{$this->default_value = strip_tags($default_value);}
	
	function set_maxlength($maxlength)
	{$this->maxlength = $maxlength;}
	
	function get_default_value()
	{return $this->default_value;}
	
	function get_maxlength()
	{return $this->maxlength;}
};

/**
*
*/
class xanthFormComboBox extends xanthFormElement
{
	var $values;
	var $default_value;
	
	function xanthFormTextField($name,$label,$description,$values,$default_value)
	{
		$this->xanthFormElement($name,$label,$description,FALSE);
		$this->set_default_value($default_value);
		$this->set_values($value);
	}
	
	function set_default_value($default_value)
	{$this->default_value = strip_tags($default_value);}
	
	function set_values($values)
	{$this->values = $values;}
	
	function get_default_value()
	{return $this->default_value;}
	
	function get_values()
	{return $this->values;}
};

/**
*
*/
class xanthFormSubmit extends xanthFormElement
{
	var $value;
	
	function xanthFormSubmit($name,$value,$label = NULL,$description = NULL)
	{
		$this->xanthFormElement($name,$label,$description,FALSE);
		$this->set_value($value);
	}
	
	function set_value($value)
	{$this->value = $value;}
	
	function get_value()
	{return $this->value;}
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
		$this->set_label($label);
		$this->set_elements($elements);
	}
	
	function set_label($label)
	{$this->label = strip_tags($label);}
	
	function set_elements($elements)
	{$this->elements = $elements;}
	
	function get_label()
	{return $this->label;}
	
	function get_elements()
	{return $this->elements;}
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
		$this->set_action($action);
		$this->set_groups($groups);
	}
	
	function set_groups($groups)
	{$this->groups = $groups;}
	
	function set_action($action)
	{$this->action = $action;}
	
	function get_groups()
	{return $this->groups;}
	
	function get_action()
	{return $this->action;}
};

/**
*
*/
function xanth_form_to_html($form)
{
	$output = "<form action=\"". $form->get_action() . "\" method=\"post\"> \n";
	$groups = $form->get_groups();

	foreach($groups as $group)
	{
		$group_label = $group->get_label();
		if(!empty($group_label))
		{
			$output .= "<fieldset> \n";
			$output .= "<legend>" . $group->get_label() . "</legend> \n";
		}
		
		$elements = $group->get_elements();
		foreach($elements as $element)
		{
			$label = $element->get_label();
			if(!empty($label))
			{
				$output .= '<label for="id-'.$element->get_name().'">'.$label.'</label>' . "\n";
			}
			
			if(xanth_instanceof($element,'xanthFormTextField'))
			{
				$output .= '<input maxlength="' . $element->get_maxlength() . '" ';
				$output .= ' name="' . $element->get_name() .'" '; 
				$output .= ' id="id-' . $element->get_name() . '" value="'.$element->get_default_value().'" type="text">'."\n";
			}
			elseif(xanth_instanceof($element,'xanthFormTextArea'))
			{
				$output .= '<textarea name="' . $element->get_name() .'" '; 
				$output .= ' id="id-' . $element->get_name() . '">'. $element->get_default_value() . '</textarea>'."\n";
			}
			elseif(xanth_instanceof($element,'xanthFormSubmit'))
			{
				$output .= '<input name="' . $element->get_name() .'" value="'.$element->get_value().'" type="submit">'."\n";
			}
		}
		
		if(!empty($group_label))
		{
			$output .= "<\fieldset> \n";
		}
	}
	$output .= "</form> \n";
	
	return $output;
}




?>