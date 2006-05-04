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
* Represent a simple link menu.
*/
class xMenu extends xBox
{
	/**
	 * @var array(xMenuItem)
	 */
	var $m_items; 
	
	/**
	* Contructor
	*
	* @param string $id
	* @param string $title
	* @param bool $is_dynamic
	* @param string $content
	* @param string $content_format
	* @param string $area
	*/
	function xMenu($id,$title,$is_dynamic,$content,$content_format,$area = NULL,$items = array())
	{
		$this->xBox($id,$title,$is_dynamic,$content,$content_format,$area);
		$this->m_items = $items;
	}
	
	// DOCS INHERITHED  ========================================================
	function render()
	{
		if($this->m_is_dynamic)
		{
			$content = '';
			$modules = xModule::getModules();
			foreach($modules as $module)
			{
				if(method_exists($module,'renderBoxContent'))
				{
					$content = $module->renderBoxContent($this->m_id);
					if($content != NULL)
					{
						return xTheme::getActive()->renderBox($this->m_title,$content);
					}
				}
			}
		}
		else
		{
			//for now
			assert(FALSE);
		}
	}
	
	/**
	* Insert a this box element into database.
	*/
	function dbInsert()
	{
		xBoxDAO::insert($this);
	}
};


/**
 * Represent a menu item.
 */
class xMenuItem extends xElement
{
	
};



?>
