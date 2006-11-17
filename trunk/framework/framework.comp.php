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
 * The component to manage xanthin framework.
 */
class xFrameworkComponent extends xModule
{
	/**
	 * 
	 */
	function __construct()
	{
		parent::__construct('Manage framework function','Mario Casciaro','alpha');
	}
	
	
	/**
	 * 
	 */
	function registerHooks(&$mod_man)
	{
		$mod_man->registerHook($this,'xh_fetchDAO','xm_fetchDAO');
		$mod_man->registerHook($this,'xh_install','xm_install');
		$mod_man->registerHook($this,'xh_templateMapping','xm_templateMapping');
		$mod_man->registerHook($this,'xh_createDocument','xm_createDocument');
		$mod_man->registerHook($this,'xh_documentComponents','xm_documentComponents');
		$mod_man->registerHook($this,'xh_documentStylesheets','xm_documentStylesheets');
	}
	
	/**
	 * {@inheritdoc}
	 */
	function xm_fetchDAO($db_type,$name)
	{
		if($db_type == 'mysql')
		{
			switch($name)
			{
				case 'module':
					return new xModuleDAO();	
			}	
		}	
	}
	
	
	/**
	 * {@inheritdoc}
	 */
	function xm_templateMapping($name)
	{
		if($name === 'test')
			return dirname(__FILE__).'/templates/test.tpl.php';
		if($name === 'document')
			return dirname(__FILE__).'/templates/document.tpl.php';
		if($name === 'content')
			return dirname(__FILE__).'/templates/content.tpl.php';
		if($name === 'page_not_found')
			return dirname(__FILE__).'/templates/page_not_found.tpl.php';
	}
	
		/**
	 * @see xEngineDummyModule::xh_documentComponents()
	 */
	function xm_documentComponents(&$path)
	{
		$arr['content'] = xContentController::fetchContent($path);
		return $arr;
	}
	
	
	/**
	 * @see xEngineDummyModule::xh_documentStylesheets()
	 */
	function xm_documentStylesheets(&$path)
	{
		return dirname(__FILE__).'/engine.css';
	}
	
	/**
	 * 
	 */
	function xm_createDocument()
	{
		$doc =& xDocument::getInstance();
		$doc->process();
		$doc->display();
	}
	
	
	/**
	 * {@inheritdoc}
	 */
	function xm_install()
	{
		$db =& xDB::getDB();
		
		//log
		$db->query("
			CREATE TABLE xanth_log (
			id INT UNSIGNED AUTO_INCREMENT NOT NULL,
			cathegory VARCHAR(32) NOT NULL,
			level MEDIUMINT NOT NULL,
			message TEXT NOT NULL,
			filename  VARCHAR(255) NOT NULL,
			line MEDIUMINT NOT NULL,
			referer TEXT NOT NULL,
			url TEXT NOT NULL,
			ip VARCHAR(16) NOT NULL,
			time DATETIME NOT NULL,
			stacktrace BLOB,
			PRIMARY KEY(id)
			)TYPE=InnoDB DEFAULT CHARACTER SET utf8"
		);
		
		//sessions
		$db->query("
			CREATE TABLE sessions (
			session_id VARCHAR(32) NOT NULL,
			session_data TEXT NOT NULL,
			session_timestamp DATETIME NOT NULL,
			PRIMARY KEY  (session_id)
			)TYPE=InnoDB DEFAULT CHARACTER SET utf8"
		);
		
		//uniqueid
		$db->query("
			CREATE TABLE uniqueid (
			tablename VARCHAR(32) NOT NULL,
			currentid INT UNSIGNED NOT NULL,
			PRIMARY KEY  (tablename)
			)TYPE=InnoDB DEFAULT CHARACTER SET utf8"
		);
		
		
		//active_modules
		$db->query("
			CREATE TABLE active_modules (
			path VARCHAR(255) NOT NULL,
			enabled TINYINT NOT NULL,
			installed TINYINT NOT NULL,
			PRIMARY KEY (path)
			)TYPE=InnoDB DEFAULT CHARACTER SET utf8"
		);
		
		//language
		$db->query("
			CREATE TABLE language (
			name VARCHAR(2) NOT NULL,
			full_name VARCHAR(32) NOT NULL,
			PRIMARY KEY (name)
			)TYPE=InnoDB DEFAULT CHARACTER SET utf8"
		);
	}
}

?>