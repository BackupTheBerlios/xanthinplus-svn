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
class xUniqueId
{	
	function xUniqueId()
	{	
		assert(FALSE);
	}
	
	/**
	 * Create a new association between a table and a unique id generator.
	 */
	function createNew($tablename)
	{
		xDB::getDB()->query("INSERT INTO uniqueid (tablename,currentid) VALUES ('%s',%d)",$tablename,0);
	}
	
	
	/**
	 * Generate a unique id in relation to a table name. For a correct use put this inside a transaction.
	 *
	 * @param string $tablename
	 * @return int
	 */
	function generate($tablename)
	{
		$ret = 0;
	
		$result = xDB::getDB()->query("SELECT currentid FROM uniqueid WHERE tablename = '%s'",$tablename);

		if($row = xDB::getDB()->fetchObject($result))
		{
			$ret = $row->currentid + 1;
			
			xDB::getDB()->query("UPDATE uniqueid SET currentid = %d WHERE tablename = '%s'",$ret,$tablename);
		}
		
		return $ret;
	}
};


?>