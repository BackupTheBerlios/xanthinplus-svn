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
* Module responsible of cathegory management
*/
class xModuleCathegory extends xModule
{
	function xModuleCathegory()
	{
		$this->xModule();
	}
	
	// DOCS INHERITHED  ========================================================
	function getContent($path)
	{
		switch($path->m_base_path)
		{
			case 'admin/cathegory':
				return $this->_getContentAdminCathegory();
			case 'cathegory/create':
				return $this->_getContentCathegoryCreate();
		}
		
		return NULL;
	}
	
	/**
	 * @access private
	 */
	function _getContentAdminCathegory()
	{
		if(!xAccessPermission::checkCurrentUserPermission('cathegory',0,'admin'))
		{
			return new xContentNotAuthorized();
		}
		
		$cathegories = xCathegory::findAll();
		
		$output = 
		'<table class="admin-table">
		<tr><th>id</th><th>Name</th><th>Operations</th></tr>
		';
		foreach($cathegories as $cathegory)
		{
			$output .= '<tr><td>' . $cathegory->m_id . '</td><td>' . $cathegory->m_name . '</td><td>Edit</td></tr>';
		}
		$output .= "</table>\n";
		
		return new xContentSimple("Admin cathegories",$output,'','');
	}
	
	
		/**
	 * @access private
	 */
	function _getContentCathegoryCreate()
	{
		if(!xAccessPermission::checkCurrentUserPermission('itemtype',0,'create'))
		{
			return new xContentNotAuthorized();
		}
		
		//create form
		$form = new xForm('?p=cathegory/create');
		//name
		$form->m_elements[] = xCathegory::getFormNameInput('name','',TRUE);
		//description
		$form->m_elements[] = xCathegory::getFormDescriptionInput('description','',FALSE);
		//parent cathegory
		$form->m_elements[] = xCathegory::getFormCathegoryChooser('parent','',FALSE);
		
		//submit buttom
		$form->m_elements[] = new xFormSubmit('submit','Create');
		
		$ret = $form->validate();
		if(isset($ret->m_valid_data['submit']))
		{
			if(empty($ret->m_errors))
			{
				$cat = new xCathegory(0,$ret->m_valid_data['name'],$ret->m_valid_data['description'],
					$ret->m_valid_data['parent'],NULL,NULL);
				$cat->dbInsert();
				
				return new xContentSimple("Create new item (generic)",'New cathegory was created with id: ','','');
			}
			else
			{
				foreach($ret->m_errors as $error)
				{
					xLog::log(LOG_LEVEL_USER_MESSAGE,$error);
				}
			}
		}
		
		return new xContentSimple("Create new cathegory",$form->render(),'','');
	}
	
};

xModule::registerDefaultModule(new xModuleCathegory());

	
?>

