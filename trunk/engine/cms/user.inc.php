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
	 */
	function dbInsert()
	{
		$this->m_id = xUserDAO::insert($this);
	}
	
	/**
	 * Delete this user from db
	 */
	function dbDelete()
	{
		xUserDAO::delete($this);
	}
	
	/**
	 * Update data of this user
	 */
	function dbUpdate()
	{
		 xUserDAO::update($this);
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
		$user = xUserDAO::checkLogin();
		if($user != NULL)
		{
			//destroy old persistent data
			xUser::_destroyPersistentLogin();
			
			//set the session...
			xUser::_setSession($user->m_username,$user->m_id);
			
			//...and the cookie if necessary
			if($remember)
			{
				xUser::_updatePersistentLogin($this->username);
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
			list($username, $cookie_token) = @unserialize($_COOKIE[XANTH_LOGIN_COOKIE_NAME]);
			if(!empty($username) && !empty($cookie_token))
			{
				$user = checkCookieToken($username,$cookie_token);
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
	function checkUser()
	{
		if(xUser::checkSession())
		{
			return TRUE;
		}
		
		if(xUser::checkPersistentLogin() != NULL)
		{
			return TRUE;
		}
		
		return FALSE;
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
	 * @param xRole $role
	 */
	function giveRole($role)
	{
		xUserDAO::giveRole($this,$role);
	}
	
	/**
	 * Remove a user from a role. Based on user id and role name.
	 *
	 * @param xRole $role
	 */
	function removeFromRole($role)
	{
		xUserDAO::removeFromRole($this,$role)
	}
	
	/**
	 * Check if the current active user have an access role.
	 *
	 * @return bool
	 */
	function checkUserAccess($access_rule)
	{
		$userid = xUser::getLoggedinUsername();
		if($userid !== NULL)
		{
			//if user has admin role bypass check
			if(xUserDAO::haveRole($this,new xRole('administrator','')))
			{
				return TRUE;
			}
			
			//check for authenticated user
			if(xRoleDAO::haveAccess(new xRole('authenticated',''),$access_rule))
			{
				return TRUE;
			}
			
			//check for other roles
			if(xUserDAO::haveAccessRule($this,$access_rule))
			{
				return TRUE;
			}
		}
		else //anonymous user
		{
			if(xRoleDAO::haveAccess(new xRole('anonymous',''),$access_rule))
			{
				return TRUE;
			}
		}
		
		return FALSE;
	}
};


?>