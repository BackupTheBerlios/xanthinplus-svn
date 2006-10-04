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
	 * @return int The new userid or FALSE on error
	 * @static
	 */
	function insert($user,$password,$transaction = true)
	{
		if($transaction)
			xDB::getDB()->startTransaction();
			
		$id = xUniqueId::generate('user');
		xDB::getDB()->query("INSERT INTO user (id,username,password,email,cookie_token) VALUES (%d,'%s','%s','%s','%s')",
			$id,$user->m_username,xUserDAO::_passwordHash($password),$user->m_email,md5(uniqid(rand(),true)));
		
		if($transaction)
			xDB::getDB()->commit();
			
		return $id;
	}
	
	/**
	 * Deletes a user. Based on username.
	 *
	 * @param string $username
	 * @return bool FALSE on error
	 * @static
	 */
	function delete($username)
	{
		return xDB::getDB()->query("DELETE FROM user WHERE username= '%s'",$username);
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
	 * @return bool FALSE on error
	 * @static
	 */
	function update($user,$password = NULL)
	{
		if(empty($password))
		{
			return xDB::getDB()->query("UPDATE user SET email = '%s' WHERE username = '%s'",$user->m_email,$user->m_username);
		}
		else
		{
			return xDB::getDB()->query("UPDATE user SET email = '%s',password= '%s' WHERE username = '%s'",
				$user->m_email,xUserDAO::_passwordHash($password),$user->m_username);
		}
	}
	
	
	/**
	 * @access private
	 */
	function _userFromRow($row)
	{
		return new xUser($row->id,$row->username,$row->email);
	}
	
	
	/**
	 * Load a user by id
	 *
	 * @return xUser
	 * @static
	 */
	function loadByUid($uid)
	{
		$result = xDB::getDB()->query("SELECT * FROM user WHERE id = '%d'",$uid);
		if($row = xDB::getDB()->fetchObject($result))
		{
			return xUserDAO::_userFromRow($row);
		}
		
		return NULL;
	}
	
	/**
	 * Load a user by username
	 *
	 * @return xUser
	 * @static
	 */
	function loadByUsername($username)
	{
		$result = xDB::getDB()->query("SELECT * FROM user WHERE username = '%s'",$username);
		if($row = xDB::getDB()->fetchObject($result))
		{
			return xUserDAO::_userFromRow($row);
		}
		
		return NULL;
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
	 * @return bool FALSE on error
	 * @static
	 */
	function giveRole($userid,$rolename)
	{
		return xDB::getDB()->query("INSERT INTO user_to_role(userid,roleName) VALUES (%d,'%s')",$userid,$rolename);
	}
	
	
	/**
	 * Return all user's roles
	 *
	 * @return array(xRole)
	 * @static
	 */
	function getRoles($userid)
	{
		$result = xDB::getDB()->query("SELECT role.name,role.description FROM role,user_to_role WHERE user_to_role.userid = %d 
			AND role.name = user_to_role.roleName",$userid);
		
		$roles = array();
		while($row = xDB::getDB()->fetchObject($result))
		{
			$roles[] = xRoleDAO::_roleFromRow($row);
		}
		
		return $roles;
	}
	
	/**
	 * Remove a user from a role. Based on user id and role name.
	 *
	 * @param int $userid
	 * @param string $rolename
	 * @return bool FALSE on error
	 * @static
	 */
	function removeFromRole($userid,$rolename)
	{
		return xDB::getDB()->query("DELETE FROM user_to_role WHERE userid = %d AND roleName = '%s'",$userid,$rolename);
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