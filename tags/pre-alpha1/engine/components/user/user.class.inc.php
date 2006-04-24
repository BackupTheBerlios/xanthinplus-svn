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
	var $id;
	var $username;
	var $email;
	//no password for security reasons
	
	function xUser($id,$username,$email = NULL)
	{
		$this->id = $id;
		$this->username = $username;
		$this->email = $email;
	}
	
	/**
	*
	*/
	function insert($password)
	{
		xanth_db_query("INSERT INTO user (username,password,email,cookie_token) VALUES ('%s','%s','%s','%s')",
			$this->username,xUser::password_hash($password),$this->email,md5(uniqid(rand(),true)));
		$this->id = xanth_db_get_last_id();
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
			$users[] = new xUser($row['id'],$row['username'],$row['email']);
		}
		
		return $user;
	}

	/**
	*
	*/
	function add_in_role($role_name)
	{
		xanth_db_query("INSERT INTO user_to_role(userid,roleName) VALUES (%d,'%s')",$this->id,$role_name);
	}
	
	/**
	*
	*/
	function del_from_role()
	{
		xanth_db_query("DELETE FROM user_to_role WHERE userid = %d AND roleName = '%s'",$this->id,$role_name);
	}
	
	/**
	*
	*/
	function find_role_names()
	{
		$roles = array();
		$result = xanth_db_query("SELECT * FROM user_to_role WHERE userid = %d",$this->id);
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
	function check_current_user_access($access_rule)
	{
		if(xanth_conf_get('debug',FALSE)) //in debug mode check if access rule is effectively present
		{
			if(!xAccessRule::exists($access_rule))
			{
				xanth_log(LOG_LEVEL_DEBUG,'Access rule "'.$access_rule.'" does not exists','User');
			}
		}
		
		
		$userid = xUser::get_current_userid();
		if($userid !== NULL)
		{
			//if user has admin role bypass check
			$result = xanth_db_query("SELECT * FROM  user_to_role WHERE userid = %d AND roleName = '%s'",$userid,'administrator');
			if($row = xanth_db_fetch_array($result))
			{
				return TRUE;
			}
			
			//select other roles
			$result = xanth_db_query("SELECT role_access_rule.access_rule FROM user_to_role,role_access_rule WHERE 
				user_to_role.userid = %d AND (role_access_rule.roleName = user_to_role.roleName OR role_access_rule.roleName = '%s') 
				AND	role_access_rule.access_rule = '%s'",$userid,'authenticated',$access_rule);
		}
		else //anonymous user
		{
			$result = xanth_db_query("SELECT role_access_rule.access_rule FROM role_access_rule WHERE 
				role_access_rule.roleName = '%s' AND role_access_rule.access_rule = '%s'",'anonymous',$access_rule);
		}
		
		if($row = xanth_db_fetch_array($result))
		{
			return TRUE;
		}
		
		
		return FALSE;
	}
	
		
	/**
	*
	*/
	function login($password,$remember)
	{
		$result = xanth_db_query("SELECT password,id FROM user WHERE username = '%s'",$this->username);
		if($row = xanth_db_fetch_object($result))
		{
			if(xUser::password_hash($password) === $row->password)
			{
				//destroy old persistent data
				$this->destroy_persistent_login();
				
				//set the session and the cookie if necessary
				$this->_set_session($this->username,$row->id);
				if($remember)
				{
					$this->_update_persistent_login($this->username);
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
		if(isset($_COOKIE['xanth_login']))
		{
			list($username, $cookie_token) = @unserialize($_COOKIE['xanth_login']);
			if(!empty($username) && !empty($cookie_token))
			{
				$result = xanth_db_query("SELECT cookie_token,id FROM user WHERE username = '%s'",$username);
				if($row = xanth_db_fetch_object($result)) 
				{
					if($row->cookie_token === $cookie_token)
					{
						//ok regenerate token for additional security
						xUser::_update_persistent_login($username);
						
						//set new session
						xUser::_set_session($username,$row->id);
						
						return TRUE;
					}
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
		if(isset($_SESSION['xuser_logged']) && $_SESSION['xuser_secure_hash'] && $_SESSION['xuser_username'] && $_SESSION['xuser_userid'])
		{
			if($_SESSION['xuser_logged'] && 
				md5($_SERVER['HTTP_USER_AGENT'] . ':' . $_SERVER['REMOTE_ADDR']) === $_SESSION['xuser_secure_hash'])
			{
				return TRUE;
			}
		}
		
		return FALSE;
	} 
	
	/**
	*
	*/
	function check_user()
	{
		if(xUser::check_session())
		{
			return TRUE;
		}
		
		if(xUser::check_persistent_login())
		{
			return TRUE;
		}
		
		return FALSE;
	}
	
	/**
	* Return the current user id or NULL on failure
	*/
	function get_current_userid()
	{
		if(isset($_SESSION['xuser_logged']) && $_SESSION['xuser_secure_hash'] && $_SESSION['xuser_username'] && $_SESSION['xuser_userid'])
		{
			return $_SESSION['xuser_userid'];
		}
		
		return NULL;
	}
	
	/**
	* Return the current user name or NULL on failure
	*/
	function get_current_username()
	{
		if(isset($_SESSION['xuser_logged']) && $_SESSION['xuser_secure_hash'] && $_SESSION['xuser_username'])
		{
			return $_SESSION['xuser_username'];
		}
		
		return NULL;
	}
	
	
	function destroy_persistent_login()
	{
		//unset persistent login cookie
		if(isset($_COOKIE['xanth_login']))
		{
			setcookie('xanth_login', '', time() - 42000);
		}
	}
	
	
	function logout()
	{	
		xUser::destroy_persistent_login();
		
		//destroy session itself, not just data
		if(isset($_COOKIE[session_name()]))
		{
		    setcookie(session_name(), '', time() - 42000);
		}

		//unset session
		session_destroy();
	}
	
	/**
	*
	*/
	function _set_session($username,$userid) 
	{
		session_regenerate_id();
		$_SESSION['xuser_username'] = $username;
		$_SESSION['xuser_userid'] = $userid;
		$_SESSION['xuser_secure_hash'] = md5($_SERVER['HTTP_USER_AGENT'] . ':' . $_SERVER['REMOTE_ADDR']) ;
		$_SESSION['xuser_logged'] = true;
	}
	
	/**
	*
	*/
	function _update_persistent_login($username) 
	{
		//generate a new login_token
		$cookie_token = md5(uniqid(rand(),true));
		xanth_db_query("UPDATE user SET cookie_token = '%s' WHERE username = '%s'",$cookie_token,$username);
		
		$cookie = serialize(array($username, $cookie_token));
		setcookie('xanth_login', $cookie, time() + 31104000);
	}
}

?>