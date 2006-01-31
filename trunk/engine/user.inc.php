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


class xUser
{
	var $username;
	var $email;
	//no password for security reasons
	
	function xUser($username,$email)
	{
		$this->username = $username;
		$this->email = $email;
	}
	
	/**
	*
	*/
	function insert($password)
	{
		xanth_db_query("INSET INTO user (username,password,email) VALUES ('%s','%s','%s')",
			$this->username,xUser::password_hash($password),$this->email);
	}
	
	/**
	*
	*/
	function delete()
	{
		xanth_db_query("DELETE FROM user WHERE username= '%s'",$this->username);
	}

	/**
	*
	*/
	function password_hash($password)
	{
		return sha1($password);
	}
	
	/**
	*
	*/
	function update($password = NULL)
	{
		if(empty($password))
		{
			xanth_db_query("UPDATE user SET email = '%s' WHERE username = '%s'",$this->email,$this->username);
		}
		else
		{
			xanth_db_query("UPDATE user SET email = '%s',password= '%s' WHERE username = '%s'",
				$this->email,xUser::password_hash($password),$this->username);
		}
	}
	
	/**
	*
	*/
	function find_all()
	{
		$users = array();
		$result = xanth_db_query("SELECT * FROM user");
		while($row = xanth_db_fetch_array($result)
		{
			$users[] = new xUser($row['username'],$row['email']);
		}
		
		return $user;
	}
	
	/**
	*
	*/
	function authenticate($password)
	{
		$result = xanth_db_query("SELECT password FROM user WHERE username = '%s'",$this->username);
		if($row = db_fetch_array($result))
		{
			if(xUser::password_hash($password) == $row['password'])
			{
				return TRUE;
			}
		}
		return FALSE;
	}
	
	
	/**
	*
	*/
	function add_in_role($role_id)
	{
		xanth_db_query("INSET INTO user_to_role(username,roleId) VALUES ('%s',%d)",$this->username,$role_id);
	}
	
	/**
	*
	*/
	function del_from_role()
	{
		xanth_db_query("DELETE FROM user_to_role WHERE username = '%s' AND roleId = %d",$this->username,$role_id);
	}
	
	/**
	*
	*/
	function find_roles_id()
	{
		$roles = array();
		$result = xanth_db_query("SELECT * FROM user_to_role WHERE username = '%s'",$this->username);
		while($row = xanth_db_fetch_array($result))
		{
			$roles[] = $row['roleId'];
		}
		return $roles;
	}
}

?>