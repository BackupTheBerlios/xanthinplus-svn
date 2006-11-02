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
 * Equivalent to current() php function, exept this return a reference instad of a value.
 */
function &current_by_ref(&$arr)
{
	return $arr[key($arr)];
}


function array_diff_no_strict($array1,$array2)
{
	$ret = array();
	foreach($array1 as $el1)
	{
		foreach($array2 as $el2)
		{
			if($el1 != $el2)
				$ret[] = $el1;
		}
	}

	return $ret;
}

?>