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


define('XBB_RET_ERROR',-1);
define('XBB_RET_FALSE',0);
define('XBB_RET_TRUE',1);

/**
*
*/
class xBBTag
{
	var $name;
	var $value;
	var $html_start_position;
	
	function xBBTag($name,$value,$html_start_position)
	{
		$this->name = $name;
		$this->value = $value;
		$this->html_start_position = $html_start_position;
	}
}

/**
*
*/
class xBBCodeParser
{
	var $txt_len;
	var $curr_pos;
	var $bbtext;
	var $htmltext;
	var $tag_stack;
	var $last_tag_content;
	var $last_error;
	
	function xBBCodeParser($bbtext)
	{
		$this->bbtext = $bbtext;
		$this->htmltext = '';
		$this->txt_len = 0;
		$this->curr_pos = 0;
		$tag_stack = array();
		$this->last_tag_content = '';
		$last_error = '';
	}
	
	function print_debug_info($function_name,$local_pos,$current_char,$other = '')
	{
		echo "Func: $function_name, Pos: $local_pos, CurrChar: $current_char, Other: $other ,HtmlOut: " 
		. htmlspecialchars($this->htmltext) . "<br>";
	}
	
	/**
	*
	*/
	function validate_url($url)
	{
		//$text = preg_replace('#(script|about|applet|activex|chrome):#is', "&#058;", $text);
		
		return preg_match("#^(http|ftp)://[\w\#$%&~/.\-;:=,?@\[\]+]*$#is", $url);
	}
	
	/**
	*
	*/
	function validate_img($img)
	{
		//$text = preg_replace('#(script|about|applet|activex|chrome):#is', "&#058;", $text);
		
		return preg_match("#^((http|ftp|https|ftps)://)([^ \?&=\#\"\n\r\t<]*?(\.(jpg|jpeg|gif|png)))$#is", $img);
	}
	
	/**
	*
	*/
	function escaped_bracket()
	{
		$pos = $this->curr_pos;
		
		$curr_char = substr($this->bbtext,$pos,2);
				
		if($curr_char === '[[')
		{
			$this->htmltext .= '[';
		}
		elseif($curr_char === ']]')
		{
			$this->htmltext .= ']';
		}
		else
		{
			return XBB_RET_FALSE;
		}
		
		$this->curr_pos = $pos + 1;
		return XBB_RET_TRUE;
	}
	
	/**
	* The first char must be a '['
	*/
	function opening_tag()
	{
		$pos = $this->curr_pos;
		$tag_name = '';
		$tag_value = '';
		
		$curr_char = substr($this->bbtext,$pos,1);
		if($curr_char !== '[')
		{
			return XBB_RET_FALSE;
		}
		$pos++;
		
		$extract_value = FALSE;
		$first_char_found = FALSE;
		$curr_char = substr($this->bbtext,$pos,1);
		while($curr_char !== ']')
		{
			//$this->print_debug_info(__FUNCTION__,$pos,$curr_char);
			
			if($pos >= $this->txt_len)
			{
				return XBB_RET_FALSE;
			}
			elseif($curr_char === '[')
			{
				return XBB_RET_FALSE;
			}
			elseif($curr_char === '/' && !$first_char_found)
			{
				return XBB_RET_FALSE;
			}
			elseif($curr_char === '=')
			{
				$first_char_found = TRUE;
				$extract_value = TRUE;
			}
			elseif($extract_value)
			{
				if(empty($tag_name))
				{
					$this->last_error = 'Tag name not found';
					return XBB_RET_ERROR;
				}
				
				$tag_value .= $curr_char;
			}
			else
			{
				$first_char_found = TRUE;
				//construct the tag name
				$tag_name .= $curr_char;
			}
			
			$pos++;
			$curr_char = substr($this->bbtext,$pos,1);
		}
		
		$res = $this->on_opening_tag($tag_name,$tag_value);
		if($res == XBB_RET_TRUE)
			$this->curr_pos = $pos;
		return $res;
	}
	
	
	/**
	* The first char must be a '['
	*/
	function closing_tag()
	{
		$pos = $this->curr_pos;
		$tag_name = '';
		
		$curr_char = substr($this->bbtext,$pos,2);
		if($curr_char !== '[/')
		{
			return XBB_RET_FALSE;
		}
		$pos += 2;
		
		$curr_char = substr($this->bbtext,$pos,1);
		while($curr_char !== ']')
		{
			//$this->print_debug_info(__FUNCTION__,$pos,$curr_char);
			
			if($pos >= $this->txt_len)
			{
				return XBB_RET_FALSE;
			}
			elseif($curr_char === '[')
			{
				return XBB_RET_FALSE;
			}
			else
			{
				//construct the tag name
				$tag_name .= $curr_char;
			}
			
			$pos++;
			$curr_char = substr($this->bbtext,$pos,1);
		}
		
		$res = $this->on_closing_tag($tag_name);
		if($res == XBB_RET_TRUE)
			$this->curr_pos = $pos;
		return $res;
	}

	
	/**
	*
	*/
	function on_opening_tag($tag_name,$tag_value)
	{
		//$this->print_debug_info(__FUNCTION__,'',$tag_name,$tag_value);
		
		if(!empty($this->tag_stack))
		{
			$last_tag = end($this->tag_stack);
		}
		else
		{
			$last_tag = new xBBTag('','',-1);
		}
		
		
		//no tags inside url if no url value was specified
		if(strcasecmp($last_tag->name,'url') == 0 && empty($last_tag->value))
		{
			$this->last_error = 'You must provide an url value if younest tags inside url tags';
			return XBB_RET_ERROR;
		}
		elseif(strcasecmp($last_tag->name,'img') == 0)
		{
			$this->last_error = 'No nesting inside img tag';
			return XBB_RET_ERROR;
		}
		
		
		//start tag parsing and transform
		if(strcasecmp($tag_name,'b') == 0)
		{
			$this->htmltext .= $this->last_tag_content . '<span style="font-weight: bold">';
		}
		elseif(strcasecmp($tag_name,'i') == 0)
		{
			$this->htmltext .= $this->last_tag_content . '<span style="font-style: italic">';
		}
		elseif(strcasecmp($tag_name,'u') == 0)
		{
			$this->htmltext .= $this->last_tag_content . '<span style="text-decoration: underline">';
		}
		elseif(strcasecmp($tag_name,'color') == 0)
		{
			if(!preg_match('#^\#[A-F0-9]{3,6}$#i',$tag_value))
			{	
				$this->last_error = 'Not valid color value, you must use only a valid exadecimal value';
				return XBB_RET_ERROR;
			}
			
			$this->htmltext .= $this->last_tag_content . '<span style="color:'.$tag_value.';">';
		}
		elseif(strcasecmp($tag_name,'size') == 0)
		{
			if(!preg_match('#^\d+$#i',$tag_value))
			{	
				$this->last_error = 'Not valid size value';
				return XBB_RET_ERROR;
			}
			$this->htmltext .= $this->last_tag_content . '<span style="font-size: '.$tag_value.'px; line-height: normal">';
		}
		elseif(strcasecmp($tag_name,'code') == 0)
		{
			$this->htmltext .= $this->last_tag_content . 
				'<table width="90%" cellspacing="1" cellpadding="3" border="0" align="center">'.
				'<tr>'.
				'<td class="code">';
		}
		elseif(strcasecmp($tag_name,'list') == 0)
		{
			if(empty($tag_value))
			{
				$this->htmltext .= $this->last_tag_content . '<ul>';
			}
			elseif(preg_match('#^(disc|circle|square|decimal|decimal-leading-zero|lower-roman|upper-roman|lower-alpha|upper-alpha)$#i',$tag_value))
			{	
				$this->htmltext .= $this->last_tag_content . '<ul style="list-style-type:'.$tag_value.';">';
			}
			else
			{
				$this->last_error = 'Not valid list type';
				return XBB_RET_ERROR;
			}
		}
		elseif(strcasecmp($tag_name,'li') == 0)
		{
			//see if last element is a list
			if(strcasecmp($last_tag->name,'list') != 0)
			{
				$this->last_error = '[li] tag used outside list tags';
				return XBB_RET_ERROR;
			}
			$this->htmltext .= $this->last_tag_content . '<li>';
		}
		elseif(strcasecmp($tag_name,'url') == 0)
		{
			if(empty($tag_value))
			{
				$this->htmltext .= $this->last_tag_content;
			}
			elseif($this->validate_url($tag_value))
			{
				$this->htmltext .= $this->last_tag_content . '<a href="'.$tag_value.'" target="_blank">';
			}
			else
			{
				$this->last_error = 'Not valid url';
				return XBB_RET_ERROR;
			}
		}
		elseif(strcasecmp($tag_name,'img') == 0)
		{
			$this->htmltext .= $this->last_tag_content;
		}
		
		else
		{
			$this->last_error = 'Not valid BBCode tag';
			return XBB_RET_ERROR;
		}
		
		$this->tag_stack[] = new xBBTag($tag_name,$tag_value,strlen($this->htmltext));
		$this->last_tag_content = '';
		return XBB_RET_TRUE;
	}
	
	/**
	*
	*/
	function on_closing_tag($tag_name)
	{
		if(!empty($this->tag_stack))
		{
			$last_tag = array_pop($this->tag_stack);
		}
		else
		{
			$last_tag = new xBBTag('','');
		}
		
		if(strcasecmp($last_tag->name,$tag_name) != 0)
		{
			$this->last_error = 'Tags does not match';
			return XBB_RET_ERROR;
		}
		
		//$this->print_debug_info(__FUNCTION__,'',$tag_name);
		
		
		if(preg_match('#^(b|u|i|color|size)$#i',$tag_name))
		{
			$this->htmltext .= $this->last_tag_content . '</span>';
		}
		elseif(strcasecmp($tag_name,'code') == 0)
		{
			$this->htmltext .= $this->last_tag_content . '</span></td></tr></table>';
		}
		elseif(strcasecmp($tag_name,'list') == 0)
		{
			$this->htmltext .= $this->last_tag_content . '</ul>';
		}
		elseif(strcasecmp($tag_name,'li') == 0)
		{
			$this->htmltext .= $this->last_tag_content . '</li>';
		}
		elseif(strcasecmp($tag_name,'url') == 0)
		{
			//case [url]xxx://yyy[/url]
			if(empty($last_tag->value))
			{
				if($this->validate_url($this->last_tag_content))
				{
					$rep = '<a href="'.$this->last_tag_content.'" target="_blank">';
					$this->htmltext = substr_replace($this->htmltext,$rep,$last_tag->html_start_position,0);
					$this->htmltext .= $this->last_tag_content . '</a>';
				}
				else
				{
					$this->last_error = 'Not valid url';
					return XBB_RET_ERROR;
				}
			}
			//case [url=xxx://yyy]sdsd[/url]
			else
			{
				$this->htmltext .= $this->last_tag_content . '</a>';
			}
		}
		elseif(strcasecmp($tag_name,'img') == 0)
		{
			//case [img]xxx://yyy[/img]

			if($this->validate_img($this->last_tag_content))
			{
				$this->htmltext .= '<img  src="'.$this->last_tag_content . '"/>';
			}
			else
			{
				$this->last_error = 'Not valid image source';
				return XBB_RET_ERROR;
			}
		}
		else
		{
			return XBB_RET_ERROR;
		}
		
		$this->last_tag_content = '';
		return XBB_RET_TRUE;
	}
	
	/**
	*
	*/
	function _parse()
	{
		for(;$this->curr_pos < $this->txt_len;$this->curr_pos++)
		{
			$curr_char = substr($this->bbtext,$this->curr_pos,1);
			
			//$this->print_debug_info(__FUNCTION__,$this->curr_pos,$curr_char);
				
			if($curr_char === ']' && !$this->escaped_bracket())
			{
				$this->last_error = 'Invalid caracter found &quot;]&quot;';
				return XBB_RET_ERROR;
			}
			elseif($curr_char === '[')
			{
				if($this->escaped_bracket())
				{
					continue;
				}
				
				$res = $this->opening_tag();
				if($res == XBB_RET_ERROR)
				{
					return $res;
				}
				if($res == XBB_RET_TRUE)
				{
					continue;
				}
				
				$res = $this->closing_tag();
				if($res == XBB_RET_ERROR)
				{
					return $res;
				}
				if($res == XBB_RET_TRUE)
				{
					continue;
				}
				
				$this->last_error = 'Invalid caracter found &quot;[&quot;';
				return XBB_RET_ERROR;
			}
			
			//no tag opened, just copy the text
			if(empty($this->tag_stack))
			{
				$this->htmltext .= $curr_char;
			}
			else
			{
				$this->last_tag_content .= $curr_char;
			}
		}
		
		if(!empty($this->tag_stack))
		{
			$this->last_error = 'Not all tags has been closed,['. end($this->tag_stack)->name.']';
			return XBB_RET_ERROR;
		}
		return XBB_RET_TRUE;
	}
	
	/**
	*
	*/
	function parse()
	{
		$this->error = '';
		
		//do some pre-processing
		$this->bbtext = htmlspecialchars($this->bbtext);
		$this->bbtext = nl2br($this->bbtext);
		$this->txt_len = strlen($this->bbtext);
		
		$res = $this->_parse();
		if($res == XBB_RET_ERROR)
		{
			$this->last_error = "BBCode parsing error: ".$this->last_error. ",while parsing \"".
				htmlspecialchars(substr($this->bbtext,$this->curr_pos,30))." \"";
			$this->htmltext = 'Text not available due to parsing error, plese edit and fix them';
			return FALSE;
		}
		return TRUE;
	}
};



?>