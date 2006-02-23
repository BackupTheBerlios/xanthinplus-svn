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


class xPage
{
	var $xanthpath;
	
	//mapped array of rendered areas
	var $areas;
	
	//mapped array containing header elements
	var $header;
	
	function xPage($xanthpath)
	{
		$this->xanthpath = $xanthpath;
	}
	
	function render()
	{
		$theme = xTheme::get_default();
		
		//retrieve content
		$page_content = xanth_invoke_mono_hook(MONO_HOOK_PAGE_CONTENT_CREATE,$this->xanthpath->base_path,
			array($this->xanthpath->resource_id));
		if($page_content === NULL)
		{
			$page_content = new xPageContent('Page not found',"<b>Page not found</b>");
		}
		
		$logs = '';
		$log_entries = xanth_get_screen_log();
		//display log messages
		if(!empty($log_entries))
		{
			$logs .= '<table border="1" width="90%"><tr><td><ul>';
			foreach($log_entries as $entry)
			{
				$logs .= '<li>' . $entry->level . ' ' . $entry->component . ' ' . htmlspecialchars($entry->message) . ' ' . $entry->filename . '@' . $entry->line . '</li>';
			}
			$logs .= '</ul></td></tr></table>' . "\n";
		}
		$page_content->body = $logs . $page_content->body;
		
		//retrieve areas
		$areas = xThemeArea::find_all();
		$this->areas = array();
		$this->boxes = array();
		
		//retrieve innermost elements
		foreach($areas as $area)
		{
			$xboxes = xBox::find($area->name);
			$this->boxes[$area->name] = array();
			foreach($xboxes as $xbox)
			{
				//retrieve box view
				$this->boxes[$area->name][] = $xbox->render();
			}
			
			//Generate area view (not using view mode)
			$this->areas[$area->name] = $area->render($this->boxes[$area->name],$page_content->body);
		}

		//construct metadata array
		$this->header = array();
		if(empty($page_content->description))
		{
			$this->header['description'] = xSettings::get('site_description');
		}
		else
		{
			$this->header['description'] = $page_content->description;
		}
		
		if(empty($page_content->keywords))
		{
			$this->header['keywords'] = xSettings::get('site_keywords');
		}
		else
		{
			$this->header['keywords'] = $page_content->keywords;
		}
		
		//retrieve the full page
		$this->header['title'] = xSettings::get('site_name') . ' | ' . $page_content->title;
		$page_ready_to_print = eval($theme->get_view_mode_procedure('page'));
		
		return $page_ready_to_print;
	}
}

?>