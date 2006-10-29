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
class xModuleMenu extends xModule
{
	function xModuleMenu()
	{
		xModule::xModule();
	}


	/**
	 * @see xDummyModule::xm_fetchContent()
	 */ 
	function xm_fetchContent($path)
	{
		if($path->m_resource === 'box' && $path->m_action === 'edit' && $path->m_type === 'menu' 
			&& $path->m_id !== NULL)
		{
			return new xPageContentBoxEdit($path);
		}
		elseif($path->m_resource === 'box' && $path->m_action === 'create' && $path->m_type === 'menu')
		{
			return new xPageContentBoxCreate($path);
		}
		elseif($path->m_resource === 'box' && $path->m_action === 'edit_translation' && $path->m_type === 'menu'
			&& $path->m_id !== NULL)
		{
			return new xPageContentBoxMenuEditTranslation($path);
		}
		
		return NULL;
	}
	
	/**
	 * @see xDummyModule::xm_fetchPermissionDescriptors()
	 */ 
	function xm_fetchPermissionDescriptors()
	{
	}
};

xModule::registerDefaultModule(new xModuleMenu());






/**
 *
 */
class xPageContentBoxMenuEditTranslation extends xPageContentBoxEditTranslation
{

	function xPageContentBoxMenuEditTranslation($path)
	{
		xPageContentBoxEditTranslation::xPageContentBoxEditTranslation($path);
	}
	
	// DOCS INHERITHED  ========================================================
	function onCreate()
	{
		$menus = xMenu::find($this->m_path->m_id,'menu',$this->m_lang);
		
		$out = '<a href="'.xPath::renderLink($this->m_path->m_lang,'menu_item','create','notype',$menu->m_name).
			'">Create menu item</a><br/><br/>';
			
		$out .= '<div class = "admin"><table>';
		$out .= '<tr><th>ID</th><th>Label</th><th>Actions</th></tr>';
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
			$out .= '<tr><td>'.$name.'</td><td>'.$box->m_type.'</td><td>' .
				xContentFilterController::applyFilter('notags',$box->m_title,$error) . '</td>
				<td>';
				
			$groups	 = $box->findBoxGroups();
			foreach($groups as $group)
			{
				$out .= $group->m_name . ' ';
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
			
			$out .= '<td>';
			$ops = call_user_func(array(xBox::getBoxTypeClass($box->m_type),'getOperations'));
			foreach($ops as $op)
			{
				$out .= '<a href="'.$op->getLink('box',$box->m_type,$box->m_name,$this->m_path->m_lang).
					'">'.$op->m_name.'</a> - ';
			}
			$out .= '</td>';
		}
		
		$out  .= "</table></div>\n";
		
		xPageContent::_set("Admin box",$out,'','');
		return true;
	}
}

?>