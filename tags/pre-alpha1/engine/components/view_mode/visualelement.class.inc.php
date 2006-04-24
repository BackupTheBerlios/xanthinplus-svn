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
class xVisualElement
{
	var $name;
	
	function xVisualElement($name)
	{
		$this->name = $name;
	}
	
	/**
	*
	*/
	function insert()
	{
		xanth_db_query("INSERT INTO visual_element(name) VALUES ('%s')",$this->name);
	}
	
	/**
	*
	*/
	
	function delete()
	{
		xanth_db_query("DELETE FROM visual_element WHERE name = '%s'",$this->name);
	}
	
	/**
	*
	*/
	function find_all()
	{
		$elems = array();
		$result = xanth_db_query("SELECT * FROM visual_element");
		while($row = xanth_db_fetch_object($result))
		{
			$elems[] = new xVisualElement($row->name);
		}
		return $elems;
	}
}

?>