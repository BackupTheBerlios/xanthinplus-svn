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



class xBBCodeParser
{
	var $txt_len;
	var $curr_pos;
	var $text;
	var $debug;
	var $tag_stack;
	
	function xBBCodeParser($text,$debug = FALSE)
	{
		$this->text = $text;
		$this->txt_len = strlen($text);
		$this->curr_pos = 0;
		$this->debug = $debug;
		$tag_stack = array();
	}
	
	/**
	*
	*/
	function skip_spaces()
	{
		for($i = $curr_pos;$i < $txt_len && substr($text,$i,1) === ' ';$i++)
		{}
		
		$curr_pos = $i;
	}
	
	/**
	* The first char must be a '['
	*/
	function opening_tag()
	{
		$pos = $this->curr_pos;
		$tag_name = '';
		$tag_value = '';
		
		$curr_char = substr($this->text,$pos,1);
		if($curr_char !== '[')
		{
			return FALSE;
		}
		$pos++;
		
		$extract_value = FALSE;
		$curr_char = substr($this->text,$pos,1);
		while($curr_char !== ']')
		{
			if($this->debug)
				echo "Fun:" . __FUNCTION__ .",Pos:$pos,CurrChar:$curr_char <br> ";
				
			if($pos >= $this->txt_len)
			{
				return FALSE;
			}
			elseif($curr_char === '[')
			{
				return FALSE;
			}
			elseif($curr_char === '/')
			{
				return FALSE;
			}
			elseif($curr_char === '=')
			{
				$extract_value = TRUE;
			}
			elseif($extract_value)
			{
				if(empty($tag_name))
				{
					return FALSE;
				}
				
				$tag_value .= $curr_char;
			}
			else
			{
				//construct the tag name
				$tag_name .= $curr_char;
			}
			
			$pos++;
			$curr_char = substr($this->text,$pos,1);
		}
		
		$this->curr_pos = $pos;
		return $this->on_opening_tag($tag_name,$tag_value);
	}
	
	
	/**
	* The first char must be a '['
	*/
	function closing_tag()
	{
		$pos = $this->curr_pos;
		$tag_name = '';
		
		$curr_char = substr($this->text,$pos,2);
		if($curr_char !== '[/')
		{
			return FALSE;
		}
		$pos += 2;
		
		$curr_char = substr($this->text,$pos,1);
		while($curr_char !== ']')
		{
			if($this->debug)
				echo "Fun:" . __FUNCTION__ .",Pos:$pos,CurrChar:$curr_char <br> ";
				
			if($pos >= $this->txt_len)
			{
				return FALSE;
			}
			elseif($curr_char === '[')
			{
				return FALSE;
			}
			else
			{
				//construct the tag name
				$tag_name .= $curr_char;
			}
			
			$pos++;
			$curr_char = substr($this->text,$pos,1);
		}
		
		$this->curr_pos = $pos;
		return $this->on_closing_tag($tag_name);
	}
	
	
	function on_opening_tag($tag_name,$tag_value)
	{
		$this->tag_stack[] = $tag_name;
		return TRUE;
	}
	
	function on_closing_tag($tag_name)
	{
		if(strcasecmp(array_pop($this->tag_stack),$tag_name) != 0)
			return FALSE;
			
		return TRUE;
	}
	
	function parse()
	{
		
		for(;$this->curr_pos < $this->txt_len;$this->curr_pos++)
		{
			$curr_char = substr($this->text,$this->curr_pos,1);
			
			if($this->debug)
				echo "Fun:" . __FUNCTION__ .",Pos:".$this->curr_pos.",CurrChar:$curr_char <br> ";
				
			if($curr_char === ']')
			{
				return FALSE;
			}
			elseif($curr_char === '[')
			{
				if($this->opening_tag())
				{
					continue;
				}
				elseif($this->closing_tag())
				{
					continue;
				}
				else
				{
					return FALSE;
				}
			}
		}
		
		return TRUE;
	}
};



?>