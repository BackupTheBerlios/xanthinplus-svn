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
* Represent a path in xanthin engine.
*/
class xPath
{
	/**
	 * @var string
	 */
	var $m_full_path;
	
	/**
	 * @var string
	 */
	var $m_resource;
	
	/**
	 * @var string
	 */
	var $m_action;
	
	/**
	 * @var string
	 */
	var $m_type;
	
	/**
	 * @var mixed
	 */
	var $m_id;
	
	/**
	 * @var int
	 */
	var $m_page;
	
	/**
	 * @var string
	 */
	var $m_lang;
	
	/**
	 * @var array(array([name] => value))
	 */
	var $m_params;
	
	
	function xPath($lang,$resource,$action,$type = NULL,$id = NULL,$page = NULL,$params = array(),$full_path = NULL)
	{
		$this->m_resource = $resource;
		$this->m_action = $action;
		$this->m_type = $type;
		$this->m_id = $id;
		$this->m_page = $page;
		$this->m_lang = $lang;
		$this->m_params = $params;
		
		if($full_path === NULL) //generate the path dinamically
		{
			$this->m_full_path = $lang . '/' . $resource . '/' . $action;
			
			if($type !== NULL)
			{
				$this->m_full_path .= '/'. $type;
				
				if($id !== NULL)
				{
					$this->m_full_path .= '/'. $id;
					
					if($page !== NULL)
						$this->m_full_path .= '/'. $page;
				}
			}
		}
		else
			$this->m_full_path = $full_path;
	}
	
	/**
	 * Return a valid xXanthPath object on success, NULL on parsing error.
	 *
	 * @return xPath
	 * @static
	 */
	function &getCurrent()
	{
		static $s_xanth_current_path;
		if(! isset($s_xanth_current_path))
			$s_xanth_current_path = xPath::_getCurrent();

		return $s_xanth_current_path;
	}
	
	
	/**
	 * 
	 */
	function _getCurrent()
	{
		if(isset($_GET['p']))
		{
			$p = $_GET['p'];
			$path = xPath::_parse($p);
			if($path !== NULL)
			{
				$path->m_params = $_GET;
				unset($path->m_params['p']);
				return $path;
			}
			else
				xLog::log('Framework',LOG_LEVEL_WARNING,'Invalid path',__FILE__,__LINE__);
		}

		$path = new xPath(NULL,NULL,NULL);
		return $path;
	}

	/**
	* Return NULL if fails to parse, otherwise a xXanthPath object
	*
	* @return xPath
	* @static
	* @private
	*/
	function _parse($path) 
	{
	    if(!preg_match('#^[A-Z][A-Z0-9_-]*(/[A-Z0-9_-]+)*$#i',$path,$pieces))
		{
	        return NULL;
	    }
		else
		{
			$path = new xPath(NULL,NULL,NULL);
			$path->m_full_path = $pieces[0];
			$exploded = explode('/',$pieces[0]);
			
			$i = 0;
			if(! isset($exploded[$i]))
				return $path;
			
			if(! preg_match('#^[A-Z]{2}$#i',$exploded[$i]))
				return NULL;
			$path->m_lang = $exploded[$i++];
			
			if(! isset($exploded[$i]))
				return $path;
			$path->m_resource = $exploded[$i++];
			
			if(! isset($exploded[$i]))
				return $path;
			$path->m_action .= $exploded[$i++];
			
			if(! isset($exploded[$i]))
				return $path;
			$path->m_type = $exploded[$i++];
			
			if(! isset($exploded[$i]))
				return $path;
			$path->m_id = $exploded[$i++];
			
			if(! isset($exploded[$i]))
				return $path;
			$path->m_page = $exploded[$i];
			
			return $path;
	    }
	}
	
	/**
	 * Outputs a link based on this path.
	 * 
	 * @param array() $forward_params A list of params to forward, if NULL all params are forwarded.
	 * @return string
	 */
	function getLink($forward_params = NULL)
	{
		$query = array();
		if($forward_params !== NULL) //forward all specified params
		{
			foreach($forward_params as $param)
				if(isset($this->m_params[$param]))
					$query[] =  $param .'='. urlencode($this->m_params[$param]);
					
			$query = implode('&',$query);
		}
		else //forward all params
		{
			foreach($this->m_params as $key => $value)
				$query[] = $key .'='. urlencode($value);
				
			$query = implode('&',$query);
		}
		
		if(!empty($query))
			$query = '&'.$query;
			
		//todo
		return '?p='.$this->m_full_path.$query;
	}
	
	
	/**
	 * Outputs a link based on given params.
	 *
	 * @return string
	 * @static
	 */
	function renderLink($lang,$resource,$action,$type = NULL,$id = NULL,$page = NULL,$params = array(),
		$forward_params = NULL)
	{
		$path = new xPath($lang,$resource,$action,$type,$id,$page,$params);
		return $path->getLink($forward_params);
	}
};

?>