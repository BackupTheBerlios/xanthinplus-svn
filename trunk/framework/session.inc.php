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


function on_session_start($save_path, $session_name) 
{
	return 1;
}

function on_session_end() 
{
	return 1;
}

function on_session_read($key) 
{
	$db =& xDB::getDB();
	
	$result = $db->query("SELECT session_data FROM sessions WHERE session_id ='%s'",$key);

	if($result)
	{
		$row =  $db->fetchArray($result);
		return($row['session_data']);
	}
	else
	{
		return '';
	}
}

function on_session_write($key, $val)
{
	$db =& xDB::getDB();
	
	$result =  $db->query("SELECT session_id FROM sessions WHERE session_id ='%s'",$key);
	if(! $db->fetchArray($result))
	{
		 $db->query("INSERT INTO sessions(session_id,session_data,session_timestamp) VALUES('%s','%s',NOW())",$key,$val);
	}
	else
	{
		 $db->query("UPDATE sessions SET session_data = '%s',session_timestamp = NOW() WHERE session_id = '%s'",$val,$key);
	}
	
	return TRUE;
}

function on_session_destroy($key) 
{
	$db =& xDB::getDB();
	
	 $db->query("DELETE FROM sessions WHERE session_id = '%s'",$key);
	 return TRUE;
}

function on_session_gc($max_lifetime) 
{
	$db =& xDB::getDB();
	
	 $db->query("DELETE FROM sessions WHERE session_timestamp < '%s'", 
		$db->encodeTimestamp(time() - $max_lifetime));
		
	return true;
}


?>
