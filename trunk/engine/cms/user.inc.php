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


define("XANTH_LOGIN_COOKIE_NAME",'xanth_login');

/**
* Represent a user.
*/
class xUser
{
	/**
	* @var int
	* @access public
	*/
	var $m_id;
	
	/**
	* @var string
	* @access public
	*/
	var $m_username;
	
	/**
	* @var string
	* @access public
	*/
	var $m_email;
	
	/**
	 * 
	 */
	function xUser($id,$username,$email)
	{
		$this->m_id = $id;
		$this->m_username = $username;
		$this->m_email = $email;
	}

	/**
	 * Insert this user in database
	 *
	 * @param string $password
	 * @return bool FALSE on error
	 */
	function dbInsert($password)
	{
		$this->m_id = xUserDAO::insert($this,$password);
		
		return $this->m_id;
	}
	
	/**
	 * Delete this user from db. (based on username)
	 *
	 * @return bool FALSE on error
	 */
	function dbDelete()
	{
		return xUserDAO::delete($this->m_username);
	}
	
	/**
	 * Update data of this user
	 *
	 * @param string $password
	 * @return bool FALSE on error
	 */
	function dbUpdate($password)
	{
		 return xUserDAO::update($this,$password);
	}
	
	
	/**
	 * Load a user from db by providing uid or username
	 *
	 * @param mixed $usr
	 * @return bool FALSE on error
	 */
	function dbLoad($usr)
	{
		if(is_int($usr))
		{
			return xUserDAO::loadByUid($usr);
		}
		
		return xUserDAO::loadByUsername($usr);
	}
	
	/**
	 * Check and executes a user login.
	 *
	 * @param string $username
	 * @param string $password
	 * @param bool $remember
	 * @return xUser If the login was valid returns a xUser object created from login data, NULL otherwise.
	 * @static
	 */
	function login($username,$password,$remember)
	{
		$user = xUserDAO::checkLogin($username,$password);
		if($user != NULL)
		{
			//destroy old persistent data
			xUser::_destroyPersistentLogin();
			
			//set the session...
			xUser::_setSession($user->m_username,$user->m_id);
			
			//...and the cookie if necessary
			if($remember)
			{
				xUser::_updatePersistentLogin($username);
			}
			
			return $user;
		}
		
		xUser::logout();
		return NULL;
	}
	
	/**
	 * Logout a user.
	 *
	 * @static
	 */
	function logout()
	{
		xUser::_destroyPersistentLogin();
		
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
	 * @access private
	 * @static
	 */
	function _setSession($username,$userid)
	{
		//prevent attack based on (I dont remember the name).
		session_regenerate_id();
		
		$_SESSION['xuser_username'] = $username;
		$_SESSION['xuser_userid'] = $userid;
		$_SESSION['xuser_secure_hash'] = md5($_SERVER['HTTP_USER_AGENT'] . ':' . $_SERVER['REMOTE_ADDR']) ;
		$_SESSION['xuser_logged'] = true;
	}
	
	/**
	 * 
	 *
	 * @access private
	 * @static
	 */
	function _destroyPersistentLogin()
	{
		//unset persistent login cookie
		if(isset($_COOKIE[XANTH_LOGIN_COOKIE_NAME]))
		{
			setcookie(XANTH_LOGIN_COOKIE_NAME, '', time() - 42000);
		}
	}
	
		
	/**
	 *
	 * @access private
	 * @static
	 */
	function _updatePersistentLogin($username) 
	{
		//generate a new login_token
		$cookie_token = xUserDAO::updateCookieToken($username);
		
		$cookie = serialize(array($username, $cookie_token));
		setcookie(XANTH_LOGIN_COOKIE_NAME, $cookie, time() + 31104000);
	}
	
	
	/**
	 * Check for a preesistent persisten login, and update its data conseguently.
	 *
	 * @return xUser If the login was valid returns a xUser object created from login data, NULL otherwise.
	 * @static
	 */
	function checkPersistentLogin()
	{
		if(isset($_COOKIE[XANTH_LOGIN_COOKIE_NAME]))
		{
			list($username, $cookie_token) = unserialize($_COOKIE[XANTH_LOGIN_COOKIE_NAME]);
			if(!empty($username) && !empty($cookie_token))
			{
				$user = xUserDAO::checkCookieToken($username,$cookie_token);
				if($user != NULL) 
				{
					//ok regenerate token for additional security
					xUser::_updatePersistentLogin($username);
					
					//set new session
					xUser::_setSession($username,$user->m_id);
					
					return $user;
				}
			}
		}
		
		return NULL;
	}
	
	
	/**
	 * Check for an active login.
	 *
	 * @return bool
	 * @static
	 */
	function checkSession()
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
	 * Check for a preesistent login.
	 *
	 * @return bool
	 * @static
	 */
	function checkUserLogin()
	{
		if(xUser::checkSession())
		{
			return TRUE;
		}
		
		if(xUser::checkPersistentLogin() !== NULL)
		{
			return TRUE;
		}
		
		return FALSE;
	}
	
	/**
	 * 
	 * @return array(xRole)
	 * @static
	 */
	function getRoles($uid)
	{
		return xUserDAO::getRoles($uid);
	}
	
	
	/**
	 * Returns the userid of the current logged in user.
	 *
	 * @return int The userid of the current logged in user, 0 on errors.
	 * @static
	 */
	function getLoggedinUserid()
	{
		if(isset($_SESSION['xuser_logged']) && $_SESSION['xuser_secure_hash'] && $_SESSION['xuser_username'] && $_SESSION['xuser_userid'])
		{
			return $_SESSION['xuser_userid'];
		}
		
		return 0;
	}

	
	/**
	 * Returns the username of the current logged in user.
	 *
	 * @return string The username of the current logged in user, NULL on error.
	 * @static
	 */
	function getLoggedinUsername()
	{
		if(isset($_SESSION['xuser_logged']) && $_SESSION['xuser_secure_hash'] && $_SESSION['xuser_username'])
		{
			return $_SESSION['xuser_username'];
		}
		
		return NULL;
	}
	
	
	/**
	 * Give a role to a user. Based on user id and role name.
	 *
	 * @param string $rolename
	 * @return bool FALSE on error
	 */
	function giveRole($rolename)
	{
		return xUserDAO::giveRole($this->m_id,$rolename);
	}
	
	
	/**
	 * 
	 *
	 * @param string $rolename
	 * @return bool
	 */
	function haveRole($rolename)
	{
		return xUserDAO::haveRole($this->m_id,$rolename);
	}
	
	
	/**
	 * 
	 *
	 * @param string $rolename
	 * @return bool
	 */
	function haveRoleByUid($uid,$rolename)
	{
		return xUserDAO::haveRole($uid,$rolename);
	}
	
	/**
	 * 
	 * @param string $rolename
	 * @return bool
	 * @static
	 */
	function currentHaveRole($rolename)
	{
		return xUserDAO::haveRole(xUser::getLoggedinUserid(),$rolename);
	}
	
	/**
	 * Remove a user from a role. Based on user id and role name.
	 *
	 * @param string $rolename
	 * @return bool FALSE on error
	 */
	function removeFromRole($rolename)
	{
		return xUserDAO::removeFromRole($this->m_id,$rolename);
	}
	
	/**
	 * Check if the current logged in user have a specified role
	 *
	 * @param string $role_name
	 * @return bool
	 * @static
	 */
	function checkCurrentUserRole($role_name)
	{
		$userid = xUser::getLoggedinUserid();
		
		if(($userid !== NULL && xUserDAO::haveRole($userid,$role_name)) || ($userid === NULL && $role_name == 'anonymous'))
		{
			return TRUE;
		}

		return FALSE;
	}
	
};


?>
