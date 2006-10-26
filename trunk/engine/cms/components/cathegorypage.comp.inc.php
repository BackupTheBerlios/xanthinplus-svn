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
* Cathegory module
*/
class xModuleCathegoryPage extends xModule
{
	function xModuleCathegoryPage()
	{
		$this->xModule();
	}


	/**
	 * @see xDummyModule::xm_fetchContent()
	 */ 
	function xm_fetchContent($path)
	{
		if($path->m_resource === "cathegory" && $path->m_type === 'page' && $path->m_action == 'admin')
		{
			return new xPageContentCathegoryAdminPage($path);
		}
		elseif($path->m_resource === "cathegory" && $path->m_type == 'page' && $path->m_action == 'create')
		{
			return new xPageContentCathegoryCreatePage($path);
		}
		elseif($path->m_resource === "cathegory" && $path->m_type == 'page' && $path->m_action == 'view')
		{
			$cat = xCathegoryPage::dbLoad($path->m_id,$path->m_lang);
			if($cat === NULL)
			{
				return new xPageContentNotFound($path);
			}
			return new xPageContentCathegoryViewPage($path,$cat);
		}
		return NULL;
	}
};

xModule::registerDefaultModule(new xModuleCathegoryPage());



/**
 * 
 */
class xPageContentCathegoryViewPage extends xPageContentCathegoryView
{	
	function xPageContentCathegoryViewPage($path,$cat)
	{
		xPageContentCathegoryView::xPageContentCathegoryView($path,$cat);
	}
	
	
	/**
	 * Fill this object with node properties by calling xNode->render(). Only metadata are not filled-id, 
	 * so override this funciton in your node type implementation.
	 */
	function onCreate()
	{
		$res = xPageContentNodeView::onCreate();
		if($res !== TRUE)
			return $res;
		
		$error = '';
		$title = xContentFilterController::applyFilter('notags',$this->m_cat->m_title,$error);
		
		xPageContent::_set($title,$this->m_cat->render(),'','');
		
		return true;
	}
}




/**
 * 
 */
class xPageContentCathegoryAdminPage extends xPageContent
{	
	function xPageContentCathegoryAdminPage($path)
	{
		$this->xPageContent($path);
	}
	
	/**
	 * Check node admin type permission
	 */
	function onCheckPreconditions()
	{
		if(!xAccessPermission::checkCurrentUserPermission('cathegory',$this->m_path->m_type,NULL,'admin'))
			return new xPageContentNotAuthorized($this->m_path);
		
		return true;
	}
	
	/**
	 * @access private
	 */
	function _groupCathegory($cats)
	{
		$out = array();
		foreach($cats as $cat)
		{
			$out[$cat->m_id][$cat->m_lang] = $cat;
		}
		
		return $out;
	}
	
	/**
	 * 
	 */
	function onCreate()
	{
		$cats = xCathegoryI18N::find(NULL);
		$cats = $this->_groupCathegory($cats);
		$out = '<a href="'.xPath::renderLink($this->m_path->m_lang,'cathegory','create','page').
			'">Create new cathegory page</a><br/><br/>';
		$out .= "<div class = 'admin'><table>\n";
		$out .= "<tr><th>ID</th><th>Title</th><th>Parent</th><th>In your lang?</th><th>Translated in</th><th>Translate in</th></tr>\n";
		$langs = xLanguage::findNames();
		foreach($cats as $id => $cat_array)
		{
			$cat = NULL;
			
			if(isset($cat_array[$this->m_path->m_lang])) 				//select current language node
			{
				$cat = $cat_array[$this->m_path->m_lang];
			}
			elseif(isset($cat_array[xSettings::get('default_lang')]))	//select default language node
			{
				$cat = $cat_array[xSettings::get('default_lang')];
			}
			else														//select first found language node
			{
				$cat = reset($cat_array);
			}
			$error = '';
			$out .= '<tr><td>'.$id.'</td><td><a href="'.
				xPath::renderLink($cat->m_lang,'cathegory','view',$cat->m_type,$cat->m_id) . '">'.
				xContentFilterController::applyFilter('notags',$cat->m_title,$error) . '</a></td>
				<td>'.$cat->m_parent_cathegory.'</td><td>';
				
			if($cat->m_lang == $this->m_path->m_lang)
				$out .= 'Yes';
			else			
				$out .= 'No';
			
			$out .= '</td><td>';
			foreach($cat_array as $lang => $ignore)
			{
				$out .= $lang . '  ';
			}
			$out .= '</td><td>';
			
			
			foreach($langs as $lang)
			{
				if(!array_key_exists($lang, $cat_array))
				{
					$out .= '<a href="'. 
						xPath::renderLink($lang,'cathegory','translate',$cat->m_type,$id) . 
						'">' . $lang . '</a>';
				}
			}
		}
		
		$out  .= "</table></div>\n";
		
		xPageContent::_set("Admin cathegory page",$out,'','');
		return true;
	}
}




/**
 *
 */
class xPageContentCathegoryCreatePage extends xPageContentCathegoryCreate
{

	function xPageContentCathegoryCreatePage($path)
	{
		xPageContentCathegoryCreate::xPageContentCathegoryCreate($path);
	}
	
	// DOCS INHERITHED  ========================================================
	function onCreate()
	{
		//create form
		$form = new xForm(xanth_relative_path($this->m_path->m_full_path));
		
		//no cathegory in path so let user choose according to its permissions
		if($this->m_path->m_id == NULL)
		{
			$cathegories = xCathegoryI18N::find($this->m_path->m_type);
			
			$options = array();
			foreach($cathegories as $cathegory)
			{
				$options[$cathegory->m_name] = $cathegory->m_id;
			}
			
			$form->m_elements[] = new xFormElementOptions('parent_cathegory','Parent cathegory','','',$options,FALSE,
				TRUE,new xCreateIntoCathegoryValidator($this->m_path->m_type));
		}
		
		//cat name
		$form->m_elements[] = new xFormElementTextField('name','Unique Name','','',true,new xInputValidatorTextNameId(32));
		
		//cat title
		$form->m_elements[] = new xFormElementTextField('title','Title','','',true,new xInputValidatorText(128));
		
		//cat description
		$form->m_elements[] = new xFormElementTextArea('description','Description','','',false,
			new xInputValidatorText());
			
		//submit buttom
		$form->m_elements[] = new xFormSubmit('submit','Create');
		
		$ret = $form->validate();
		if(! $ret->isEmpty())
		{
			if(empty($ret->m_errors))
			{
				$cathegory = array();
				if($this->m_path->m_id != NULL)
					$cathegory = $this->m_path->m_id;
				else
					$cathegory = $ret->m_valid_data['parent_cathegory'];
					
				$cat = new xCathegoryI18N(-1,$this->m_path->m_type,$cathegory,$ret->m_valid_data['name'],
					$ret->m_valid_data['title'],$ret->m_valid_data['description'],$this->m_path->m_lang);
				
				if($cat->dbInsert())
				{
					xNotifications::add(NOTIFICATION_NOTICE,'New cathegory successfully created');
				}
				else
				{
					xNotifications::add(NOTIFICATION_ERROR,'Error: cathegory was not created');
				}
				
				$this->_set("Create new cathegory page",'','','');
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

		$this->_set("Create new cathegory page",$form->render(),'','');
		return TRUE;
	}
}
	
?>