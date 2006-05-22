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
* A module for tests
*/
class xModuleTest extends xModule
{
	function xModuleTest()
	{
		$this->xModule('Test','engine/cms/components/');
	}

	// DOCS INHERITHED  ========================================================
	function getContent($path)
	{
		if($path->m_base_path == 'test')
		{	
			//create form
			$form = new xForm('?p=' . $path->m_full_path);
			$form->m_elements[] = new xFormElementCheckbox('test[1][hello]','Label','',1,FALSE,FALSE,new xInputValidatorInteger());
			$form->m_elements[] = new xFormElementTextField('void','Label','','',FALSE,new xInputValidatorText(0));
			
			//submit buttom
			$form->m_elements[] = new xFormSubmit('submit','Create');
			
			$ret = $form->validate();
			if(! $ret->isEmpty())
			{
				print_r($ret);
				if(empty($ret->m_errors))
				{
					
					return new xContentSimple("Create new item page",'','','');
				}
				else
				{
					foreach($ret->m_errors as $error)
					{
						xNotifications::add(NOTIFICATION_WARNING,$error);
					}
				}
			}
			
			return new xContentSimple("Create new item page",$form->render(),'','');
		}
		
		return NULL;
	}
};

xModule::registerDefaultModule(new xModuleTest());
	
?>
