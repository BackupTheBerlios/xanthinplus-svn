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


class xSettings
{
	/**
	*
	*/
	function save()
	{
		global $xanth_settings;
		
		//dynamically construct the query
		$val_array = array();
		
		$query = "UPDATE settings SET ";
		foreach($xanth_settings as $sett_name => $sett_value) 
		{
			$query .= $sett_name . " = '%s' ,";
			$val_array[] = $sett_value;
		}
		//remove last ','
		$query = substr_replace($query,'',strlen($query) - 1,0);
		
		xanth_db_query($query,$val_array);
	}
	
	/**
	*
	*/
	function load()
	{
		global $xanth_settings;
		
		$result = xanth_db_query("SELECT * FROM settings");
		$xanth_settings = xanth_db_fetch_array($result);
	}
}

?>