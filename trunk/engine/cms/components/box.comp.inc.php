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
class xModuleBox extends xModule
{
	function xModuleBox()
	{
		xModule::xModule();
	}


	/**
	 * @see xDummyModule::xm_fetchContent()
	 */ 
	function xm_fetchContent($path)
	{
		if($path->m_resource === 'box' && $path->m_action === 'admin' && $path->m_type === NULL)
		{
			return new xPageContentBoxAdmin($path);
		}
		
		return NULL;
	}
	
	/**
	 * @see xDummyModule::xm_fetchPermissionDescriptors()
	 */ 
	function xm_fetchPermissionDescriptors()
	{
		$descrs = array();
		$descr[] = new xAccessPermissionDescriptor('admin/box',NULL,NULL,'create','Create a custom box of any type');
		return $descrs;
	}
};

xModule::registerDefaultModule(new xModuleBox());




/**
 *
 */
class xPageContentBoxAdmin extends xPageContent
{

	function xPageContentBoxAdmin($path)
	{
		xPageContent::xPageContent($path);
	}
	
	// DOCS INHERITHED  ========================================================
	function onCheckPreconditions()
	{
		if(!xAccessPermission::checkCurrentUserPermission('box',NULL,NULL,'admin'))
			return new xPageContentNotAuthorized($this->m_path);
			
		return true;
	}
	
	
	/**
	 * @access private
	 */
	function _groupBoxes($boxes)
	{
		$out = array();
		foreach($boxes as $box)
		{
			$out[$box->m_name][$box->m_lang] = $box;
		}
		
		return $out;
	}
	
	
	// DOCS INHERITHED  ========================================================
	function onCreate()
	{
		$boxes = xBoxI18N::find();
		$boxes = $this->_groupBoxes($boxes);
		
		$out = '<div class = "admin"><table>';
		$out .= '<tr><th>Name</th><th>Type</th><th>Title</th><th>Group</th><th>In your lang?</th><th>Translated in</th><th>Translate in</th></tr>';
		$langs = xLanguage::findNames();
		foreach($boxes as $name => $box_array)
		{
			$box = NULL;
			
			if(isset($box_array[$this->m_path->m_lang])) 				//select current language node
			{
				$box = $box_array[$this->m_path->m_lang];
			}
			elseif(isset($box_array[xSettings::get('default_lang')]))	//select default language node
			{
				$box = $box_array[xSettings::get('default_lang')];
			}
			else														//select first found language node
			{
				$box = reset($box_array);
			}
			$error = '';
			$out .= '<tr><td>'.$name.'</td><td>'.$box->m_type.'</td><td><a href="'.
				xPath::renderLink($box->m_lang,'box','view',$box->m_type,$box->m_name) . '">'.
				xContentFilterController::applyFilter('notags',$box->m_title,$error) . '</a></td>
				<td>';
				
			$groups	 = $box->findBoxGroups();
			foreach($groups as $group)
			{
				$out .= $group->m_name;
			}
			
			$out .= '</td><td>';
				
			if($box->m_lang == $this->m_path->m_lang)
				$out .= 'Yes';
			else			
				$out .= 'No';
			
			$out .= '</td><td>';
			foreach($box_array as $lang => $ignore)
			{
				$out .= $lang . '  ';
			}
			$out .= '</td><td>';
			
			
			foreach($langs as $lang)
			{
				if(!array_key_exists($lang, $box_array))
				{
					$out .= '<a href="'. 
						xPath::renderLink($lang,'box','translate',$box->m_type,$name) . 
						'">' . $lang . '</a>';
				}
			}
		}
		
		$out  .= "</table></div>\n";
		
		xPageContent::_set("Admin box",$out,'','');
		return true;
	}
}

	
?>
