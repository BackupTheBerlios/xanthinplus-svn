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
 * A module for box
 */
class xModuleBoxGroup extends xModule
{
	function xModuleBoxGroup()
	{
		xModule::xModule();
	}


	/**
	 * @see xDummyModule::xm_fetchContent()
	 */ 
	function xm_fetchContent($path)
	{
		if($path->m_resource === 'boxgroup' && $path->m_action === 'admin' && $path->m_type === NULL)
		{
			return new xPageContentBoxGroupAdmin($path);
		}
		
		elseif($path->m_resource === 'boxgroup' && $path->m_action === 'edit' && $path->m_id !== NULL)
		{
			return new xPageContentBoxGroupEdit($path);
		}
		
		return NULL;
	}
	
	/**
	 * @see xDummyModule::xm_fetchPermissionDescriptors()
	 */ 
	function xm_fetchPermissionDescriptors()
	{
		/*
		$descrs = array();
		$descr[] = new xAccessPermissionDescriptor('admin/box',NULL,NULL,'create','Create a custom box of any type');
		return $descrs;
		*/
	}
};

xModule::registerDefaultModule(new xModuleBoxGroup());




/**
 *
 */
class xPageContentBoxGroupAdmin extends xPageContent
{

	function xPageContentBoxGroupAdmin($path)
	{
		xPageContent::xPageContent($path);
	}
	
	// DOCS INHERITHED  ========================================================
	function onCheckPreconditions()
	{
		if(!xAccessPermission::checkCurrentUserPermission('boxgroup',NULL,NULL,'admin'))
			return new xPageContentNotAuthorized($this->m_path);
			
		return true;
	}
	
	
	// DOCS INHERITHED  ========================================================
	function onCreate()
	{
		$groups = xBoxGroup::find(NULL,FALSE);
		
		$out = '<div class = "admin"><table>';
		$out .= '<tr><th>Name</th><th>Render?</th><th>Description</th></tr>';
		foreach($groups as $group)
		{
			$out  .= '<tr><td><a href="'.xPath::renderLink($this->m_path->m_lang,'boxgroup','edit','notype',$group->m_name).'">'.
				$group->m_name.'</a></td><td>';
			if($group->m_render)
				$out  .= 'TRUE';
			else
				$out  .= 'FALSE';
			
			$out  .= '</td><td>'.$group->m_description.'</td></tr>';
		}
		
		$out  .= "</table></div>\n";
		
		xPageContent::_set("Admin box groups",$out,'','');
		return true;
	}
}



/**
 *
 */
class xPageContentBoxGroupEdit extends xPageContent
{

	function xPageContentBoxGroupEdit($path)
	{
		xPageContent::xPageContent($path);
	}
	
	/**
	 * Check for permission and for group existence
	 */
	function onCheckPreconditions()
	{
		if(!xAccessPermission::checkCurrentUserPermission('boxgroup',NULL,NULL,'edit'))
			return new xPageContentNotAuthorized($this->m_path);
		
		if(xBoxGroup::load($this->m_path->m_id) === NULL)
			return new xPageContentNotFound($this->m_path);
			
		return true;
	}
	
	
	// DOCS INHERITHED  ========================================================
	function onCreate()
	{
		$boxgroup = xBoxGroup::load($this->m_path->m_id);
		
		//create form
		$form = new xForm('edit_group',$this->m_path->getLink());
		
		//description
		$form->m_elements[] = new xFormElementTextField('description','Description','',$boxgroup->m_description,false,
			new xInputValidatorText(255));
		
		//renderizable
		$form->m_elements[] = new xFormElementCheckbox('render','Should render?','',1,$boxgroup->m_render,FALSE,
			new xInputValidatorInteger());
		
		$group = new xFormGroup('Assigned Boxes');
		$all_boxes = xBox::find();
		foreach($all_boxes as $box)
		{
			$selected = false;
			if(in_array($boxgroup,$box->findBoxGroups()))
				$selected = true;
				
			$group->m_elements[] = new xFormElementCheckbox('box['.$box->m_name.']',$box->m_name,'',
				1,$selected,FALSE,new xInputValidatorText(32));
		}
		$form->m_elements[] = $group;
		
		//submit buttom
		$form->m_elements[] = new xFormSubmit('submit','Update');
		
		$ret = $form->validate();
		if(! $ret->isEmpty())
		{
			if(empty($ret->m_errors))
			{
				$boxes = array();
				foreach($ret->m_valid_data['box'] as $name => $val)
				{
					if($val == 1)
						$boxes[] = reset(xBox::find($name));
				}
				
				$bgroup = new xBoxGroup($this->m_path->m_id,$ret->m_valid_data['description'],
					$ret->m_valid_data['render'],$boxes);
				
				if($bgroup->update())
				{
					xNotifications::add(NOTIFICATION_NOTICE,'Box group updated successfully');
				}
				else
				{
					xNotifications::add(NOTIFICATION_ERROR,'Error: box group was not updated');
				}
				
				$this->_set("Edit box group",'','','');
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

		$this->_set("Edit box group",$form->render(),'','');
		return TRUE;
	}
}



?>
