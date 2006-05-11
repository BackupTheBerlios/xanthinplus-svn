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


class xUserDAO
{
	function xUser()
	{
		//no instatiable
		assert(FALSE);
	}
	
	/**
	 * Inserts a new user.
	 *
	 * @param xUser $user
	 * @param string $password
	 * @return int The new userid
	 * @static
	 */
	function insert($user,$password)
	{
		xDB::getDB()->query("INSERT INTO user (username,password,email,cookie_token) VALUES ('%s','%s','%s','%s')",
			$user->m_username,xUserDAO::_passwordHash($password),$user->m_email,md5(uniqid(rand(),true)));
		
		return xDB::getDB()->getLastId();
	}
	
	/**
	 * Deletes a user. Based on username.
	 *
	 * @param string $username
	 * @static
	 */
	function delete($username)
	{
		xDB::getDB()->query("DELETE FROM user WHERE username= '%s'",$username);
	}

	/**
	 * Exexcutes an hashing on a password
	 *
	 * @static
	 * @access private
	 * @return int
	 */
	function _passwordHash($password)
	{
		return sha1($password);
	}
	
	/**
	 * Updates a user. Based on username.
	 *
	 * @param xUser $user
	 * @param string $password If NULL password will not be updated.
	 * @static
	 */
	function update($user,$password = NULL)
	{
		if(empty($password))
		{
			xDB::getDB()->query("UPDATE user SET email = '%s' WHERE username = '%s'",$user->m_email,$user->m_username);
		}
		else
		{
			xDB::getDB()->query("UPDATE user SET email = '%s',password= '%s' WHERE username = '%s'",
				$user->m_email,xUserDAO::_passwordHash($password),$user->m_username);
		}
	}
	
	/**
	 * Retrieves all users
	 *
	 * @return array(xUser)
	 * @static
	 */
	function findAll()
	{
		$users = array();
		$result = xDB::getDB()->query("SELECT * FROM user");
		while($row = xDB::getDB()->fetchArray($result))
		{
			$users[] = new xUser($row['id'],$row['username'],$row['email']);
		}
		
		return $user;
	}

	/**
	 * Give a role to a user. Based on user id and role name.
	 *
	 * @param int $userid
	 * @param string $rolename
	 * @static
	 */
	function giveRole($userid,$rolename)
	{
		xDB::getDB()->query("INSERT INTO user_to_role(userid,roleName) VALUES (%d,'%s')",$userid,$rolename);
	}
	
	/**
	 * Remove a user from a role. Based on user id and role name.
	 *
	 * @param int $userid
	 * @param string $rolename
	 * @static
	 */
	function removeFromRole($userid,$rolename)
	{
		xDB::getDB()->query("DELETE FROM user_to_role WHERE userid = %d AND roleName = '%s'",$userid,$rolename);
	}
	
	/**
	 * Retrieve all role names that belongs to a users. Based on id.
	 *
	 * @param int $userid
	 * @return array(string)
	 * @static
	 */
	function findUserRoleNames($userid)
	{
		$roles = array();
		$result = xDB::getDB()->query("SELECT * FROM user_to_role WHERE userid = %d",$userid);
		while($row = xDB::getDB()->fetchArray($result))
		{
			$roles[] = $row['roleName'];
		}
		return $roles;
	}
	
	/**
	 * Check if an user have a specified role. Based on user id and role name.
	 *
	 * @param int $userid
	 * @param string $rolename
	 * @return bool
	 * @static
	 */
	function haveRole($userid,$rolename)
	{
		$result = xDB::getDB()->query("SELECT * FROM user_to_role WHERE userid = %d AND roleName = '%s'",
			$userid,$rolename);
		
		if($row = xDB::getDB()->fetchArray($result))
		{
			return true;
		}
		
		return FALSE;
	}
	
	
	/**
	 * Check if an user have a specified access rule. Based on user id.
	 *
	 * @param int $userid
	 * @param string $access_rule
	 * @return bool
	 * @static
	 */
	function haveAccessRule($userid,$access_rule)
	{
		$result = xDB::getDB()->query("SELECT role_access_rule.access_rule FROM user_to_role,role_access_rule WHERE 
			user_to_role.userid = %d AND role_access_rule.roleName = user_to_role.roleName AND role_access_rule.access_rule = '%s'",
			$userid,$access_rule);
		
		if($row = xDB::getDB()->fetchArray($result))
		{
			return TRUE;
		}
		
		return FALSE;
	}
	
		
	/**
	 * Check for a valid login.
	 *
	 * @param string $username
	 * @param string $password
	 * @return xUser If the login was valid returns a xUser object created from login data, NULL otherwise.
	 * @static
	 */
	function checkLogin($username,$password)
	{
		$result = xDB::getDB()->query("SELECT * FROM user WHERE username = '%s'",$username);
		if($row = xDB::getDB()->fetchObject($result))
		{
			if(xUserDAO::_passwordHash($password) === $row->password)
			{
				return new xUser($row->id,$row->username,$row->email);
			}
		}
		
		return NULL;
	}

	/**
	 * Check if the passed cookie token is valid fot a user.
	 *
	 * @param string $username
	 * @param string $cookie_token
	 * @return xUser If the login was valid returns a xUser object created from login data, NULL otherwise.
	 * @static
	 */
	function checkCookieToken($username,$cookie_token) 
	{
		$result = xDB::getDB()->query("SELECT * FROM user WHERE username = '%s'",$username);
		if($row = xDB::getDB()->fetchObject($result)) 
		{
			if($row->cookie_token === $cookie_token)
			{
				return new xUser($row->id,$row->username,$row->email);
			}
		}
		return NULL;
	}
	
	/**
	 * Updates the cookie_token of a user with a random generated one.
	 *
	 * @param string $username
	 * @return string The new cookie token.
	 * @static
	 */
	function updateCookieToken($username) 
	{
		//generate a new login_token
		$cookie_token = md5(uniqid(rand(),true));
		xDB::getDB()->query("UPDATE user SET cookie_token = '%s' WHERE username = '%s'",$cookie_token,$username);
		
		return $cookie_token;
	}
}

?>