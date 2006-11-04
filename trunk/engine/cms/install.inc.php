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
* An object for installing the core cms database.
*/
class xInstallCMS
{
	function xModuleInstallCMS()
	{
		//not instantiable
		assert(FALSE);
	}
	
	/**
	* Installs the cms in a Mysql db
	*/
	function installDBMySql()
	{
		$db =& xDB::getDB();
		
		//log
		$db->query("
			CREATE TABLE xanth_log (
			id INT UNSIGNED AUTO_INCREMENT NOT NULL,
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
		
		/////////////////////////////////////////////////////
		//CMS RELATED
		/////////////////////////////////////////////////////
		
		
		//settings
		$db->query("
			CREATE TABLE settings (
			name VARCHAR(32) NOT NULL,
			value VARCHAR(512) NOT NULL,
			PRIMARY KEY (name)
			)TYPE=InnoDB DEFAULT CHARACTER SET utf8"
		);

		//Roles
		$db->query("
			CREATE TABLE role (
			name VARCHAR(32) NOT NULL,
			description VARCHAR(255) NOT NULL,
			PRIMARY KEY(name)
			)TYPE=InnoDB DEFAULT CHARACTER SET utf8"
		);
		
		//Users
		$db->query("
			CREATE TABLE user (
			id INT UNSIGNED AUTO_INCREMENT NOT NULL,
			username VARCHAR(32) NOT NULL,
			password VARCHAR(64) NOT NULL,
			email VARCHAR(128) NOT NULL,
			cookie_token VARCHAR(64) NOT NULL,
			PRIMARY KEY (id),
			UNIQUE(username),
			INDEX(username),
			UNIQUE(email)
			)TYPE=InnoDB DEFAULT CHARACTER SET utf8");
		xUniqueId::createNew('user');
			
		//User to role
		$db->query("
			CREATE TABLE user_to_role (
			userid INT UNSIGNED NOT NULL,
			roleName VARCHAR(32) NOT NULL,
			UNIQUE(userid,roleName),
			INDEX(userid),
			INDEX(roleName),
			FOREIGN KEY (userid) REFERENCES user(id) ON DELETE CASCADE,
			FOREIGN KEY (roleName) REFERENCES role(name) ON DELETE CASCADE
			)TYPE=InnoDB DEFAULT CHARACTER SET utf8");
		
		
		//access permission
		$db->query("
			CREATE TABLE access_permission (
			resource VARCHAR(64) NOT NULL,
			resource_type VARCHAR(64),
			resource_id INT UNSIGNED,
			action VARCHAR(32) NOT NULL,
			role VARCHAR(32) NOT NULL,
			PRIMARY KEY(resource,resource_type,resource_id,action,role),
			FOREIGN KEY (role) REFERENCES role(name) ON DELETE CASCADE
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
		
		
		//box type
		$db->query("
			CREATE TABLE box_type (
			name VARCHAR(32) NOT NULL,
			user_editable TINYINT NOT NULL,
			description VARCHAR(256) NOT NULL,
			PRIMARY KEY (name)
			)TYPE=InnoDB DEFAULT CHARACTER SET utf8"
		);
		
		
		//box
		$db->query("
			CREATE TABLE box(
			name VARCHAR(32) NOT NULL,
			type VARCHAR(32) NOT NULL,
			weight TINYINT NOT NULL,
			show_filters_type TINYINT NOT NULL,
			show_filters TEXT NOT NULL,
			PRIMARY KEY(name),
			FOREIGN KEY (type) REFERENCES box_type(name) ON DELETE RESTRICT
			)TYPE=InnoDB DEFAULT CHARACTER SET utf8"
		);
		
		
		//box i18n
		$db->query("
			CREATE TABLE box_i18n(
			box_name VARCHAR(32) NOT NULL,
			title VARCHAR(128) NOT NULL,
			lang VARCHAR(2) NOT NULL,
			PRIMARY KEY(box_name,lang),
			FOREIGN KEY (box_name) REFERENCES box(name) ON DELETE CASCADE,
			FOREIGN KEY (lang) REFERENCES language(name) ON DELETE CASCADE
			)TYPE=InnoDB DEFAULT CHARACTER SET utf8"
		);
		
		
		//static box
		$db->query("
			CREATE TABLE box_custom(
			box_name VARCHAR(64) NOT NULL,
			lang VARCHAR(2) NOT NULL,
			content TEXT NOT NULL,
			content_filter VARCHAR(64) NOT NULL,
			PRIMARY KEY (box_name,lang),
			FOREIGN KEY (box_name,lang) REFERENCES box_i18n(box_name,lang) ON DELETE CASCADE
			)TYPE=InnoDB DEFAULT CHARACTER SET utf8"
		);
		
		
		//menu_static_items
		$db->query("
			CREATE TABLE menu_item (
			id INT UNSIGNED NOT NULL,
			box_name VARCHAR(64) NOT NULL,
			lang VARCHAR(2) NOT NULL,
			label VARCHAR(128) NOT NULL,
			link VARCHAR(128) NOT NULL,
			weight TINYINT NOT NULL,
			parent INT UNSIGNED,
			PRIMARY KEY(id),
			INDEX(box_name,lang),
			FOREIGN KEY (parent) REFERENCES menu_item(id) ON DELETE CASCADE,
			FOREIGN KEY (box_name,lang) REFERENCES box_i18n(box_name,lang) ON DELETE CASCADE
			)TYPE=InnoDB DEFAULT CHARACTER SET utf8"
		);
		xUniqueId::createNew('menu_item');
		
		
		$db->query("
			CREATE TABLE box_group(
			name VARCHAR(32) NOT NULL,
			render TINYINT NOT NULL,
			description VARCHAR(255) NOT NULL,
			PRIMARY KEY(name)
			)TYPE=InnoDB DEFAULT CHARACTER SET utf8"
		);
		
		
		//box to group
		$db->query("
			CREATE TABLE box_to_group(
			box_group VARCHAR(64) NOT NULL,
			box_name VARCHAR(64) NOT NULL,
			UNIQUE (box_group,box_name),
			FOREIGN KEY (box_name) REFERENCES box(name) ON DELETE CASCADE,
			FOREIGN KEY (box_group) REFERENCES box_group(name) ON DELETE CASCADE
			)TYPE=InnoDB DEFAULT CHARACTER SET utf8"
		);
		
		
		//item type
		$db->query("
			CREATE TABLE node_and_cathegory_type (
			name VARCHAR(32) NOT NULL,
			description VARCHAR(256) NOT NULL,
			PRIMARY KEY (name)
			)TYPE=InnoDB DEFAULT CHARACTER SET utf8"
		);
		
		
		//item cathegory
		$db->query("
			CREATE TABLE cathegory (
			id INT UNSIGNED NOT NULL,
			title VARCHAR(128) NOT NULL,
			type VARCHAR(32) NOT NULL,
			description TEXT NOT NULL,
			parent_cathegory INT UNSIGNED,
			PRIMARY KEY (id),
			FOREIGN KEY (parent_cathegory) REFERENCES cathegory(id) ON DELETE CASCADE,
			FOREIGN KEY (type) REFERENCES node_and_cathegory_type(name) ON DELETE RESTRICT
			)TYPE=InnoDB DEFAULT CHARACTER SET utf8"
		);
		xUniqueId::createNew('cathegory');
		
		
		//cathegory i18n
		$db->query("
			CREATE TABLE cathegory_i18n (
			catid INT UNSIGNED NOT NULL,
			name VARCHAR(32) NOT NULL,
			title VARCHAR(128) NOT NULL,
			description TEXT NOT NULL,
			lang VARCHAR(2) NOT NULL,
			UNIQUE(name),
			PRIMARY KEY (catid,lang),
			FOREIGN KEY (catid) REFERENCES cathegory(id) ON DELETE CASCADE,
			FOREIGN KEY (lang) REFERENCES language(name) ON DELETE CASCADE
			)TYPE=InnoDB DEFAULT CHARACTER SET utf8"
		);
		
		
		//node
		$db->query("
			CREATE TABLE node (
			id INT UNSIGNED NOT NULL,
			type VARCHAR(32) NOT NULL,
			author VARCHAR(64) NOT NULL,
			content_filter VARCHAR(64) NOT NULL,
			creation_time DATETIME NOT NULL,
			edit_time DATETIME NOT NULL,
			PRIMARY KEY (id),
			FOREIGN KEY (type) REFERENCES node_and_cathegory_type(name) ON DELETE RESTRICT
			)TYPE=InnoDB DEFAULT CHARACTER SET utf8"
		);
		xUniqueId::createNew('node');
		
		
		//node i18n
		$db->query("
			CREATE TABLE node_i18n (
			nodeid INT UNSIGNED NOT NULL,
			title VARCHAR(256) NOT NULL,
			content TEXT NOT NULL,
			lang VARCHAR(2) NOT NULL,
			translator VARCHAR(64) NOT NULL,
			PRIMARY KEY (nodeid,lang),
			FOREIGN KEY (nodeid) REFERENCES node(id) ON DELETE CASCADE,
			FOREIGN KEY (lang) REFERENCES language(name) ON DELETE CASCADE
			)TYPE=InnoDB DEFAULT CHARACTER SET utf8"
		);
		
		//pageitem
		$db->query("
			CREATE TABLE node_page (
			nodeid INT UNSIGNED NOT NULL,
			lang VARCHAR(2) NOT NULL,
			published TINYINT NOT NULL,
			sticky TINYINT NOT NULL,
			accept_replies TINYINT NOT NULL,
			approved TINYINT NOT NULL,
			meta_description VARCHAR(128) NOT NULL,
			meta_keywords VARCHAR(128) NOT NULL,
			PRIMARY KEY (nodeid,lang),
			FOREIGN KEY (nodeid,lang) REFERENCES node_i18n(nodeid,lang) ON DELETE CASCADE
			)TYPE=InnoDB DEFAULT CHARACTER SET utf8"
		);
		
		//item to cathegory
		$db->query("
			CREATE TABLE node_to_cathegory (
			nodeid INT UNSIGNED NOT NULL,
			catid INT UNSIGNED NOT NULL,
			UNIQUE (nodeid,catid),
			FOREIGN KEY (nodeid) REFERENCES node(id) ON DELETE CASCADE,
			FOREIGN KEY (catid) REFERENCES cathegory(id) ON DELETE CASCADE
			)TYPE=InnoDB DEFAULT CHARACTER SET utf8"
		);
		
		
		//pageitem
		$db->query("
			CREATE TABLE node_reply (
			nodeid INT UNSIGNED NOT NULL,
			parentid INT UNSIGNED NOT NULL,
			approved TINYINT NOT NULL,
			PRIMARY KEY (nodeid),
			FOREIGN KEY (parentid) REFERENCES node(id) ON DELETE CASCADE,
			FOREIGN KEY (nodeid) REFERENCES node(id) ON DELETE CASCADE
			)TYPE=InnoDB DEFAULT CHARACTER SET utf8"
		);
		
		//settings
		xSettings::insert('site_name','Site Name');
		xSettings::insert('site_description','');
		xSettings::insert('site_keywords','');
		xSettings::insert('theme','xanthin');
		xSettings::insert('default_lang','en');
		
		//lang
		$lang = new xLanguage('en','English');
		$lang->insert();
		$lang = new xLanguage('it','Italiano');
		$lang->insert();
		
		//roles
		$role = new xRole('administrator','Administrator');
		$role->insert();
		$role = new xRole('authenticated','Authenticated user');
		$role->insert();
		$role = new xRole('anonymous','Anonymous visitor');
		$role->insert();
		
		//user
		$user = new xUser('','admin','root@localhost.com');
		$user->insert('pass');
		$user->giveRole('administrator');
		
		//node	type
		$node_type = new xNodeType('page','Basic node type');
		$node_type->insert();
		
		//box types
		$box_type = new xBoxType('custom','A user custom box');
		$box_type->insert();
		$box_type = new xBoxType('menu','a Menu');
		$box_type->insert();
		$box_type = new xBoxType('builtin','a Menu');
		$box_type->insert();
		
		//root cathegory
		$cat = new xCathegoryI18N(0,'page',NULL,'page_root','Root cathegory','Root cathegory','en');
		$cat->insert();
		
		//menus
		$menu = new xMenu('admin','menu',0,new xShowFilter(XANTH_SHOW_FILTER_EXCLUSIVE,''),'Admin','en');
	
		$menuitem = new xMenuItem(-1,'Homepage','?p=en',-1,'en');
		$menu->m_items[] = $menuitem;
		
		$menuitem = new xMenuItem(-1,'Cathegory Manager','?p=en/cathegory/admin',-1,'en');
		$menu->m_items[] = $menuitem;
		
		$menuitem = new xMenuItem(-1,'Node Manager','?p=en/node/admin',-1,'en');
		$menu->m_items[] = $menuitem;
		
		$menuitem = new xMenuItem(-1,'Access permissions','?p=en/accesspermissions/admin',-1,'en');
		$menu->m_items[] = $menuitem;
		
		$menuitem = new xMenuItem(-1,'Box Manager','?p=en/box/admin',-1,'en');
		$menu->m_items[] = $menuitem;
		
		$menuitem = new xMenuItem(-1,'Box Groups Manager','?p=en/boxgroup/admin',-1,'en');
		$menu->m_items[] = $menuitem;
		
		$menuitem = new xMenuItem(-1,'Login','?p=en/user/login',-1,'en');
		$menu->m_items[] = $menuitem;
		
		$menuitem = new xMenuItem(-1,'Logout','?p=en/user/logout',-1,'en');
		$menu->m_items[] = $menuitem;
		
		$menuitem = new xMenuItem(-1,'Settings','?p=en/settings/admin',-1,'en');
		$menu->m_items[] = $menuitem;
		
		$menuitem = new xMenuItem(-1,'Install','install.php',-1,'en');
		$menu->m_items[] = $menuitem;
		
		$menu->insert();
		
		//box group
		$group = new xBoxGroup('left_group','',true,array($menu));
		$group->insert();
	}
};


/**
*
*/
function xanth_install_main()
{
	//select DB
	if(xConf::get('db_type','mysql') == 'mysql')
	{
		$db = new xDBMysql();
		$db->connect(xConf::get('db_host',''),xConf::get('db_user',''),xConf::get('db_pass',''),xConf::get('db_port',''));
		xDB::setDB($db);
		
		$name = xConf::get('db_name','');
		$db->query("DROP DATABASE $name");
		$db->query("CREATE DATABASE $name");
		
		$db->selectDB($name);
	}
	else
	{
		exit('Unknown database type');
	}
	
	//error handler
	set_error_handler('xanth_php_error_handler');
	
	//install cms
	xInstallCMS::installDBMySql();
	
	//print log
	echo xLogEntry::renderFromScreen();
	
	echo "Xanthin Successfully installed";
}


?>