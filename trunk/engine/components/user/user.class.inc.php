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
		while($row = xanth_db_fetch_array($result))
		{
			$users[] = new xUser($row['username'],$row['email']);
		}
		
		return $user;
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
	function find_role_names()
	{
		$roles = array();
		$result = xanth_db_query("SELECT * FROM user_to_role WHERE username = '%s'",$this->username);
		while($row = xanth_db_fetch_array($result))
		{
			$roles[] = $row['roleName'];
		}
		return $roles;
	}
	
	/**
	*
	*/
	function have_role($role_name)
	{
		return in_array($role_name,find_role_names());
	}
	
	
	
	/**
	*
	*/
	function login($password,$remember)
	{
		$result = xanth_db_query("SELECT password FROM user WHERE username = '%s'",$this->username);
		if($row = db_fetch_object($result))
		{
			if(xUser::password_hash($password) === $row->password)
			{
				//destroy old data
				$this->logout();
				
				//set the session and the cookie if necessary
				$this->_set_session(); 
				if($remember)
				{
					$this->_update_persistent_login();
				}
				
				return TRUE;
			}
		}
		$this->logout();
		return FALSE;
	}
	
	/**
	*
	*/
	function check_persistent_login() 
	{
		list($username, $cookie_token) = @unserialize($_COOKIE('xanth_login'));
		if(!empty($username) && !empty($cookie_token))
		{
			$result = xanth_db_query("SELECT cookie_token FROM user WHERE username = '%s'",$this->username);
			if($row = xanth_db_fetch_object($result)) 
			{
				if($row->cookie_token === $cookie_token)
				{
					//ok regenerate token for additional security
					_update_persistent_login();
					return TRUE;
				}
			}
		}
		return FALSE;
	}
	
	/**
	*
	*/
	function check_session() 
	{
		if(isset($_SESSION['xuser_logged']) && $_SESSION['xuser_secure_hash'] && $_SESSION['xuser_username'])
		{
			if($_SESSION['xuser_logged'] && 
				md5($_SERVER['HTTP_USER_AGENT'] . ':' . $_SERVER['REMOTE_ADDR']) === $_SESSION['xuser_secure_hash'])
			{
				return TRUE;
			}
		}
		
		$this->logout();
		return FALSE;
	} 
	
	function logout()
	{
		//unset cookie
		setcookie('xanth_login', '', time() - 1000);
		
		//unset session
		session_destroy();
	}
	
	/**
	*
	*/
	function _set_session() 
	{
		session_regenerate_id();
		$_SESSION['xuser_username'] = $this->username;
		$_SESSION['xuser_secure_hash'] = md5($_SERVER['HTTP_USER_AGENT'] . ':' . $_SERVER['REMOTE_ADDR']) ;
		$_SESSION['xuser_logged'] = true;
	}
	
	/**
	*
	*/
	function _update_persistent_login() 
	{
		//generate a new login_token
		$cookie_token = md5(uniqid(rand(),true);
		xanth_db_query("UPDATE user SET cookie_token = '%s' WHERE username = '%s'",$cookie_token,$this->username);
		
		$cookie = serialize(array($_SESSION['xuser_username'], $cookie_token));
		setcookie('xanth_login', $cookie, time() + 31104000);
	}
}

?>