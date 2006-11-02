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
 * Provide methods to generate unique ids in relation to a table name.
 */
class xUTF8
{	
	function xUTF8()
	{	
		assert(FALSE);
	}
	
	
	/**
	 *
	 */
	function strlen($string)
	{
		if(function_exists('utf8_decode'))
		{
			return strlen(utf8_decode($string));
		}
		else
		{
			// Do not count UTF-8 continuation bytes.
		    return strlen(preg_replace("/[\x80-\xBF]/", '', $text));
		}
	}
	
	
	/**
	 * Checks if a string contains 7bit ASCII only
	 *
	 * @return bool
	 */
	function isASCII($str)
	{
		for($i = 0; $i < strlen($str); $i++)
		{
			if(ord($str[$i]) > 127)
				return false;
		}
		return true;
	}
	
	
	/**
	 * Simply check that string does not contain invalid utf sequences.
	 *
	 * @see http://www.dwheeler.com/secure-programs/Secure-Programs-HOWTO/character-encoding.html#UTF8-LEGAL-VALUES
	 */
	function isValid($str)
	{
		for($i = 0; $i < strlen($str); $i++) 
		{
			$b = ord($str[$i]);
			if ($b < 0x80) 					// 0xxxxxxx
				continue; 
			elseif(($b & 0xE0) == 0xC0) 	// 110xxxxx
				$n = 1; 
			elseif(($b & 0xF0) == 0xE0) 	// 1110xxxx
				$n = 2; 
			elseif(($b & 0xF8) == 0xF0) 	// 11110xxx
				$n = 3; 
			elseif(($b & 0xFC) == 0xF8) 	// 111110xx
				$n = 4; 
			elseif(($b & 0xFE) == 0xFC) 	// 1111110x
				$n = 5; 
			else							// Does not match any pattern
				return false; 
				
			for($j = 0; $j < $n;$j++) 		// pattern matching 10xxxxxx follow ?
			{ 
				if((++$i == strlen($str)) || ((ord($str[$i]) & 0xC0) != 0x80))
					return false;
			}
		}
		return true;
	}
	
	
	
	
};


?>