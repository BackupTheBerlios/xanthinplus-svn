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
* Represent an operation on a node or a content.
*/
class xOperation
{
	/**
	 * @var string
	 */
	var $m_action;
	
	/**
	 * @var string
	 */
	var $m_resource;
	
	/**
	* @var string
	*/
	var $m_name;
	
	/**
	* @var string
	*/
	var $m_description;
	

	function xOperation($resource,$action,$name,$description)
	{
		$this->m_resource = $resource;
		$this->m_action = $action;
		$this->m_name = $name;
		$this->m_description = $description;
	}
	
	
	/**
	 * Outputs the link corresponding to this operation.
	 */
	function getLink($resource_type,$resource_id,$lang)
	{
		return xPath::renderLink($lang,$this->m_resource,$this->m_action,$resource_type,$resource_id);
	}
};

?>