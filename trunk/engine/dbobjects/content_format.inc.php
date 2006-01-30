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

class xanthContentFormat
{
	var $name;
	var $stripped_html;
	var $php_source;
	var $new_line_to_line_break;
	
	function xanthContentFormat($name,$stripped_html,$php_source,$new_line_to_line_break)
	{
		$this->name = $name;
		$this->stripped_html = $stripped_html;
		$this->php_source = $php_source;
		$this->new_line_to_line_break = $new_line_to_line_break;
	}
	
	
	/**
	*
	*/
	function apply_content_format($content)
	{
		if($this->php_source)
		{
			ob_start();
			eval($content);
			return ob_get_clean();
		}
		elseif($this->stripped_html)
		{
			$cont = strip_tags($content,'<strong>','<ul>','<li>','<br>');
			
			if($this->new_line_to_line_break)
				$cont = nl2br($cont);
			
			return $cont;
		}
		else //full html
		{
			if($this->new_line_to_line_break)
				$cont = nl2br($content);
			
			return $cont;
		}
	}
}




?>