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
 * Represent a menu item.
 */
class xMenuItem extends xElement
{
	/**
	 * @var int
	 */
	var $m_id;
	
	/**
	 * @var string
	 */
	var $m_lang;
	
	/**
	 * @var string
	 * @access public
	 */
	var $m_label;
	
	/**
	 * @var string
	 * @access public
	 */
	var $m_link;
	
	/**
	 * @var int
	 * @access public
	 */
	var $m_weight;
	
	/**
	 * @var array(xMenuItem)
	 * @access public
	 */
	var $m_subitems;
	
	/**
	 * Contructor
	 *
	 */
	function xMenuItem($id,$label,$link,$weight,$lang,$subitems = array())
	{
		$this->m_id = $id;
		$this->m_label = $label;
		$this->m_link = $link;
		$this->m_weight = $weight;
		$this->m_subitems = $subitems;
		$this->m_lang = $lang;
	}
	
	// DOCS INHERITHED  ========================================================
	function render()
	{
		usort($this->m_subitems, "_objWeightCompare");
		$subitems = xTheme::render('renderMenuItems',array($this->m_subitems));

		$error = '';
		$label = xContentFilterController::applyFilter('notags', $this->m_label, $error);
		$link = xContentFilterController::applyFilter('notags', $this->m_link, $error);
		return xTheme::render('renderMenuItem',array($label,$link,$subitems));
	}
};



/**
 * Represent a simple link menu.
 */
class xMenu extends xBoxI18N
{	
	/**
	 * @var string
	 */
	var $lang;
	
	
	/**
	 * @var array(xMenuItem)
	 */
	var $m_items;
	
	/**
	* Contructor
	*
	* @param string $name
	* @param string $title
	* @param string $type
	* @param string $area
	*/
	function xMenu($name,$type,$weight,$show_filter,$title,$lang,$items = array())
	{
		$this->xBoxI18N($name,$type,$weight,$show_filter,$title,$lang);
		$this->m_items = $items;
	}
	
	// DOCS INHERITHED  ========================================================
	function render()
	{
		usort($this->m_items, "_objWeightCompare");
		$content = xTheme::render('renderMenuItems',array($this->m_items));
		
		return xTheme::render('renderBox',array($this->m_name,$this->m_title,$content));
	}
	
	/**
	 * Insert this object into db
	 *
	 * @return bool FALSE on error
	 */
	function insert()
	{
		return xMenuDAO::insert($this);
	}
	
	/**
	 * 
	 */
	function update()
	{
		return xMenuDAO::update($this);
	}
	
	/**
	 * @access private
	 */
	function _addItem($items,$item,$parent)
	{
		$ret = array();
		if(! empty($items))
		{
			foreach($items as $ite)
			{
				$ite->m_subitems = $this->_addItem($ite->m_subitems,$item,$parent);
				if($ite->m_id == $parent)
					$ite->m_subitems[] = $item;	
				$ret[] = $ite;
			}
		}	
		return $ret;
	}
	
	/**
	 *
	 */
	function addItem($item,$parent = NULL)
	{
		if(empty($parent))
			$this->m_items[] = $item;
		else
			$this->m_items = $this->_addItem($this->m_items,$item,$parent);
	}
	
	/**
	 * Insert this object into db
	 *
	 * @return bool FALSE on error
	 */
	function insertTranslation()
	{
		return xMenuDAO::insertTranslation($this);
	}
	
	/**
	 * Delete this menu translation from db.
	 *
	 * @return bool FALSE on error
	 */
	function deleteTranslation()
	{
		return xMenuDAO::deleteTranslation($this->m_name,$this->m_lang);
	}
	
	/**
	 * @return array(xOperation)
	 */
	function find($name = NULL,$type = 'menu',$lang = NULL,$flexible_lang = TRUE)
	{
		return xMenuDAO::find($name,$type,$lang,$flexible_lang);
	}
};
xBox::registerBoxTypeClass('menu','xMenu');

?>
