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
 * Represent the simplest item in xanthin+
 */
class xItemPage extends xItemCathegorizable
{
	/**
	 * @var bool
	 * @access public
	 */
	var $m_sticky;
	
	/**
	 * @var bool
	 * @access public
	 */
	var $m_accept_replies;
	
	/**
	 * @var bool
	 * @access public
	 */
	var $m_published;
	
	/**
	 * @var bool
	 * @access public
	 */
	var $m_approved;
	
	/**
	 * @var string
	 * @access public
	 */
	var $m_meta_description;
	
	/**
	 * @var bool
	 * @access public
	 */
	var $m_meta_keywords;
	
	/**
	 * @var bool
	 * @access public
	 */
	var $m_last_edit_time;
	
	/**
	 *
	 */
	function xItemPage($id,$title,$type,$author,$content,$content_filter,$creation_time,$cathegory,
		$published,$sticky,$accept_replies,$approved,$meta_description,$meta_keywords,$last_edit_time)
	{
		$this->xItemCathegorizable($id,$title,$type,$author,$content,$content_filter,$creation_time,$cathegory);
		
		$this->m_sticky = $sticky;
		$this->m_accept_replies = $accept_replies;
		$this->m_published = $published;
		$this->m_approved = $approved;
		$this->m_meta_description = $meta_description;
		$this->m_meta_keywords = $meta_keywords;
		$this->m_last_edit_time = $last_edit_time;
	}
	
	/** 
	 * Inserts this into db
	 *
	 * @return bool FALSE on error
	 */
	function dbInsert()
	{
		$this->m_id = xItemPageDAO::insert($this);
		return $this->m_id;
	}
	
	/**
	 * Update this in db
	 *
	 * @return bool FALSE on error
	 */
	function dbUpdate()
	{
		return xItemPageDAO::update($this);
	}
	
	/**
	 * Retrieve a specific item page from db
	 *
	 * @return xItemPage
	 * @static
	 */
	function dbLoad($id)
	{
		return xItemPageDAO::load($id);
	}
	
	/**
	 * Retrieves all items.
	 *
	 * @param string $type Exact search
	 * @param string $title Like search
	 * @param string $author Exact search
	 * @param string $content Like search
	 * @param int $cathegory Exact search on category id
	 * @param int $nelementpage Number of elements per page
	 * @param int $npage Number of page (starting from 1).
	 * @return array(xItem)
	 * @static
	 */
	function find($title = NULL,$author = NULL,$content = NULL,$cathegory = NULL,$nelementpage = 0,$npage = 0)
	{
		return xItemPageDAO::find($title,$author,$content,$cathegory,$nelementpage,$npage);
	}
	
	
	/**
	 * Return a form element for asking for published input
	 *
	 * @param string $var_name The name of the form element
	 * @param string $label
	 * @param string $description
	 * @param bool $checked
	 * @return xFormElement
	 * @static
	 */
	function getFormPublishedCheck($var_name,$label,$description,$checked)
	{
		return new xFormElementCheckbox($var_name,$label,$description,1,$checked,FALSE,new xInputValidatorInteger());
	}
	
	/**
	 * Return a form element for asking for approved check
	 *
	 * @param string $var_name The name of the form element
	 * @param string $label
	 * @param string $description
	 * @param bool $checked
	 * @return xFormElement
	 * @static
	 */
	function getFormApprovedCheck($var_name,$label,$description,$checked)
	{
		return new xFormElementCheckbox($var_name,$label,$description,1,$checked,FALSE,new xInputValidatorInteger());
	}
	
	
	/**
	 * Return a form element for asking for accept replies check
	 *
	 * @param string $var_name The name of the form element
	 * @param string $label
	 * @param string $description
	 * @param bool $checked
	 * @return xFormElement
	 * @static
	 */
	function getFormAcceptRepliesCheck($var_name,$label,$description,$checked)
	{
		return new xFormElementCheckbox($var_name,$label,$description,1,$checked,FALSE,new xInputValidatorInteger());
	}
	
	
	/**
	 * Return a form element for asking for sticky input
	 *
	 * @param string $var_name The name of the form element
	 * @param string $label
	 * @param string $description
	 * @param bool $checked
	 * @return xFormElement
	 * @static
	 */
	function getFormStickyCheck($var_name,$label,$description,$checked)
	{
		return new xFormElementCheckbox($var_name,$label,$description,1,$checked,FALSE,new xInputValidatorInteger());
	}
	
	/**
	 * Return a form element for asking for description input
	 *
	 * @param string $var_name The name of the form element
	 * @param string $value
	 * @param string $label
	 * @param string $description
	 * @param bool $mandatory True if this input is manadtory
	 * @return xFormElement
	 * @static
	 */
	function getFormDescriptionInput($var_name,$label,$description,$value,$mandatory)
	{
		return new xFormElementTextField($var_name,$label,$description,$value,$mandatory,new xInputValidatorText(512));
	}
	
	/**
	 * Return a form element for asking for keywords input
	 *
	 * @param string $var_name The name of the form element
	 * @param string $value
	 * @param string $label
	 * @param string $description
	 * @param bool $mandatory True if this input is manadtory
	 * @return xFormElement
	 * @static
	 */
	function getFormKeywordsInput($var_name,$label,$description,$value,$mandatory)
	{
		return new xFormElementTextField($var_name,$label,$description,$value,$mandatory,new xInputValidatorText(128));
	}
};

/**
 * Root class of a hieararchy that manage view,creation,deletion,modification of item types.
 */
class xItemPageManager extends xItemCathegorizableManager
{
	function xItemPageManager()
	{}
	
	// DOCS INHERITHED  ========================================================
	function onContentCreate($path,&$content)
	{
		$type = $path->m_vars['type'];
		//check for cathegory permission
		$cathegory = NULL;
		if(isset($path->m_vars['cathegory']))
			$cathegory = $path->m_vars['cathegory'];
				

		//create form
		$form = new xForm('?p=' . $path->m_full_path);
		
		if($cathegory === NULL)
		{
			$form->m_elements[] = xCathegory::getFormCathegoryChooser('cathegory','Cathegory','','',FALSE,TRUE,
				$type);
		}
		
		
		//item title
		$form->m_elements[] = xItem::getFormTitleInput('title','',true);
		//item body
		$form->m_elements[] = xItem::getFormBodyInput('body','Body','','',true,
			new xInputValidatorDynamicContentFilter(0,'filter'));
		//item filter
		$form->m_elements[] = xContentFilterController::getFormContentFilterChooser('filter','html',TRUE);
		
		
		$group = new xFormGroup('Parameters');
		//item published
		$group->m_elements[] = xItemPage::getFormPublishedCheck('published','Published','',false);
		//item approved
		$group->m_elements[] = xItemPage::getFormApprovedCheck('approved','Approved','',false);
		//item sticky
		$group->m_elements[] = xItemPage::getFormStickyCheck('sticky','Sticky','',false);
		//item accept replies
		$group->m_elements[] = xItemPage::getFormAcceptRepliesCheck('accept_replies','Accept Replies','',false);
		$form->m_elements[] = $group;
		
		$group = new xFormGroup('Metadata');
		//item description
		$group->m_elements[] = xItemPage::getFormDescriptionInput('description','Description','','',false);
		//item keywords
		$group->m_elements[] = xItemPage::getFormKeywordsInput('keywords','Keywords','','',false);
		$form->m_elements[] = $group;
		
		//submit buttom
		$form->m_elements[] = new xFormSubmit('submit','Create');
		
		$ret = $form->validate();
		if(! $ret->isEmpty())
		{
			if(empty($ret->m_errors))
			{
				if($cathegory === NULL)
				{
					if(! xCathegory::cathegorySupportItemType($path->m_vars['cathegory'],$path->m_vars['type']))
						return FALSE;
					
					$cathegory = $ret->m_valid_data['cathegory'];
				}
				
				$item = new xItemPage(-1,$ret->m_valid_data['title'],$path->m_vars['type'],'autore',
					$ret->m_valid_data['body'],$ret->m_valid_data['filter'],NULL,$cathegory,
					$ret->m_valid_data['published'],$ret->m_valid_data['sticky'],$ret->m_valid_data['accept_replies'],
					$ret->m_valid_data['approved'],0,$ret->m_valid_data['description'],$ret->m_valid_data['keywords'],
					NULL);
				if($item->dbInsert())
				{
					xNotifications::add(NOTIFICATION_NOTICE,'New item successfully created');
				}
				else
				{
					xNotifications::add(NOTIFICATION_ERROR,'Error: Item was not created');
				}
				
				$content->_set("Create new item page",'','','');
				return TRUE;
			}
			else
			{
				foreach($ret->m_errors as $error)
				{
					xNotifications::add(NOTIFICATION_WARNING,$error);
				}
			}
		}

		$content->_set("Create new item page",$form->render(),'','');
		return TRUE;
	}
	
	
	/**
	 * Retrieve a specific item from db.
	 *
	 * @return xItem
	 * @static
	 */
	function dbLoad($id)
	{
		return xItemPage::dbLoad($id);
	}
}


xItemManager::registerItemManager(new xItemPageManager(),'page');

?>
