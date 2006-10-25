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

$g_xanth_cathegory_managers = array();


/**
 * A cathegory.
 */
class xCathegory extends xElement
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
	var $m_type;
	
	/**
	 * @var int
	 * @access public
	 */
	var $m_parent_cathegory;
	
	/**
	 *
	 */
	function xCathegory($id,$type,$parent_cathegory)
	{
		$this->xElement();
		
		$this->m_id = (int) $id;
		$this->m_type = $type;
		$this->m_parent_cathegory = $parent_cathegory;
	}
	
	/** 
	 * Delete this cathegory from db
	 *
	 * @return bool FALSE on error
	 */
	function dbDelete()
	{
		return xCathegoryDAO::delete($this->m_id);
	}
	
	/** 
	 * Delete a cathegory from db using its id
	 *
	 * @param int $catid
	 * @return bool FALSE on error
	 * @static
	 */
	function dbDeleteById($catid)
	{
		return xCathegoryDAO::delete($catid);
	}
	
	/**
	 * Retrieve a specific cathegory from db
	 *
	 * @return xCathegory
	 * @static
	 */
	function dbLoad($id)
	{
		return xCathegoryDAO::load($id);
	}
	
	/**
	 *
	 */
	function fetchCathegory($cat_id,$cat_type)
	{
		return xModule::callWithSingleResult2('xm_fetchCathegory',$cat_id,$cat_type);
	}
	
	
	/**
	 * Retrieves generic xCathegory objects by different search parameters
	 *
	 * @return array(xCathegory)
	 * @static
	 */
	function find($type = NULL,$parent_cathegory = NULL)
	{
		return xCathegoryDAO::find($type,$parent_cathegory);
	}
	
	/**
	 * Check recursively an access permission relative to this cathegory
	 *
	 * @return bool
	 * @static
	 */
	function checkCurrentUserPermissionRecursive($action)
	{
		//first check permission in this cathegory
		if(!xAccessPermission::checkCurrentUserPermission('cathegory',NULL,$this->m_id,'view'))
			return FALSE;
		
		//now load parent and check parent
		if($this->m_parent_cathegory != NULL)
		{
			$parent = xCathegory::dbLoad($this->m_parent_cathegory);
			if(! $parent->checkCurrentUserPermissionRecursive($action))
				return false;
		}
		
		return true;
	}
};



/**
 * An internationalized cathegory.
 */
class xCathegoryI18N extends xCathegory
{
	var $m_lang;
	
	/**
	 * @var string
	 * @access public
	 */
	var $m_title;
	
	/**
	 * @var string
	 * @access public
	 */
	var $m_name;
	
	
	/**
	 * @var string
	 * @access public
	 */
	var $m_description;
	
	/**
	 *
	 */
	function xCathegoryI18N($id,$type,$parent_cathegory,$name,$title,$description,$lang)
	{
		$this->xCathegory($id,$type,$parent_cathegory);
		
		$this->m_name = $name;
		$this->m_lang = $lang;
		$this->m_title = $title;
		$this->m_description = $description;
	}
	
	
	// DOCS INHERITHED  ========================================================
	function render()
	{
		$error = '';
		$description = xContentFilterController::applyFilter('html',$this->m_description,$error);
		$title = xContentFilterController::applyFilter('notags',$this->m_title,$error);
		
		return xTheme::render4('renderCathegory',$title,$description);
	}
	
	/** 
	 * Inserts this into db
	 *
	 * @return bool FALSE on error
	 */
	function dbInsert()
	{
		return xCathegoryI18NDAO::insert($this);
	}
	
	/**
	 * Update this in db
	 *
	 * @return bool FALSE on error
	 */
	function dbUpdate()
	{
		return xCathegoryI18NDAO::update($this);
	}
	
	
		/** 
	 * Inserts this into db
	 *
	 * @return bool FALSE on error
	 */
	function dbInsertTranslation()
	{
		return xCathegoryI18NDAO::insertTranslation($this);
	}
	
	
	/** 
	 * Delete this cathegory from db
	 *
	 * @return bool FALSE on error
	 */
	function dbDeleteTranslation()
	{
		return xCathegoryI18NDAO::deleteTranslation($this->m_id,$this->m_lang);
	}
	
	
	/**
	 * Retrieve a specific cathegory from db
	 *
	 * @return xCathegory
	 * @static
	 */
	function dbLoad($id,$lang)
	{
		if(is_numeric($id))
		{
			return xCathegoryI18NDAO::load((int) $id,$lang);
		}
		
		return xCathegoryI18NDAO::loadByName($id,$lang);
	}	
	
	/**
	 * Retrieves generic xCathegory objects by different search parameters
	 *
	 * @return array(xCathegory)
	 * @static
	 */
	function find($type = NULL,$parent_cathegory = NULL,$name = NULL,$lang = NULL)
	{
		return xCathegoryI18NDAO::find($type,$parent_cathegory,$name,$lang);
	}
	
	/**
	 *
	 */
	function findChildNodes()
	{
	
	}
};


/**
 *
 */
class xCathegoryPage extends xCathegoryI18N
{
	/**
	 *
	 */
	function xCathegoryPage($id,$type,$parent_cathegory,$name,$title,$description,$lang)
	{
		$this->xCathegoryI18N($id,$type,$parent_cathegory);
	}
	
	/**
	 * Retrieves generic xCathegory objects by different search parameters
	 *
	 * @return array(xCathegory)
	 * @static
	 */
	function find($parent_cathegory = NULL,$name = NULL,$lang = NULL)
	{
		return xCathegoryI18NDAO::find('page',$parent_cathegory,$name,$lang);
	}
};



/**
 * Checks for cathegory existence, for permissions and for type matching.
 */
class xCreateIntoCathegoryValidator extends xInputValidatorInteger
{
	var $m_type;
	
	function xCreateIntoCathegoryValidator($type)
	{
		xInputValidatorInteger::xInputValidatorInteger();
		$this->m_type = $type;
	}
	
	// DOCS INHERITHED  ========================================================
	function isValid($input)
	{
		if(empty($input))
			return true;
			
		if(!xInputValidatorInteger::isValid($input))
			return FALSE;
		
		$cathegory = xCathegory::dbLoad($input);
		if($cathegory === NULL)
		{
			echo "here";
			$this->m_last_error = 'Cathegory not found';
			return false;
		}
		if($cathegory->m_type != $this->m_type)
		{
			$this->m_last_error = 'Child and parent type does not match';
			return false;
		}
		if(! $cathegory->checkCurrentUserPermissionRecursive('create_inside'))
		{
			$this->m_last_error = 'You are not authorized to insert inside this cathegory';
			return false;
		}
		return true;
	}
}



?>
