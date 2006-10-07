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
		assert(FASLE);
	}
	
	/**
	* Installs the cms in a Mysql db
	*/
	function installDBMySql()
	{
		//log
		xDB::getDB()->query("
			CREATE TABLE xanth_log (
			id INT UNSIGNED AUTO_INCREMENT NOT NULL,
			level MEDIUMINT NOT NULL,
			message TEXT NOT NULL,
			filename  VARCHAR(255) NOT NULL,
			line MEDIUMINT NOT NULL,
			timestamp DATETIME NOT NULL,
			stacktrace BLOB,
			PRIMARY KEY(id)
			)TYPE=InnoDB"
		);
		
		
		//sessions
		xDB::getDB()->query("
			CREATE TABLE sessions (
			session_id VARCHAR(32) NOT NULL,
			session_data TEXT NOT NULL,
			session_timestamp DATETIME NOT NULL,
			PRIMARY KEY  (session_id)
			)TYPE=InnoDB"
		);
		
		//uniqueid
		xDB::getDB()->query("
			CREATE TABLE uniqueid (
			tablename VARCHAR(32) NOT NULL,
			currentid INT UNSIGNED NOT NULL,
			PRIMARY KEY  (tablename)
			)TYPE=InnoDB"
		);
		
		/////////////////////////////////////////////////////
		//CMS RELATED
		/////////////////////////////////////////////////////
		
		
		//settings
		xDB::getDB()->query("
			CREATE TABLE settings (
			name VARCHAR(32) NOT NULL,
			value VARCHAR(512) NOT NULL,
			PRIMARY KEY (name)
			)TYPE=InnoDB"
		);

		//Roles
		xDB::getDB()->query("
			CREATE TABLE role (
			name VARCHAR(32) NOT NULL,
			description VARCHAR(255) NOT NULL,
			PRIMARY KEY(name)
			)TYPE=InnoDB"
		);
		
		//Users
		xDB::getDB()->query("
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
			)TYPE=InnoDB");
		xUniqueId::createNew('user');
			
		//User to role
		xDB::getDB()->query("
			CREATE TABLE user_to_role (
			userid INT UNSIGNED NOT NULL,
			roleName VARCHAR(32) NOT NULL,
			UNIQUE(userid,roleName),
			INDEX(userid),
			INDEX(roleName),
			FOREIGN KEY (userid) REFERENCES user(id) ON DELETE CASCADE,
			FOREIGN KEY (roleName) REFERENCES role(name) ON DELETE CASCADE
			)TYPE=InnoDB");
		
		
		//access permission
		xDB::getDB()->query("
			CREATE TABLE access_permission (
			resource VARCHAR(64) NOT NULL,
			resource_type VARCHAR(64),
			resource_id INT UNSIGNED,
			action VARCHAR(32) NOT NULL,
			role VARCHAR(32) NOT NULL,
			PRIMARY KEY(resource,resource_type,resource_id,action,role),
			FOREIGN KEY (role) REFERENCES role(name) ON DELETE CASCADE
			)TYPE=InnoDB"
		);
		
		//box
		xDB::getDB()->query("
			CREATE TABLE box(
			name VARCHAR(64) NOT NULL,
			title VARCHAR(255) NOT NULL,
			content TEXT NOT NULL,
			content_filter VARCHAR(64) NOT NULL,
			type VARCHAR(32) NOT NULL,
			weight TINYINT NOT NULL,
			PRIMARY KEY(name)
			)TYPE=InnoDB"
		);
		
		xDB::getDB()->query("
			CREATE TABLE box_group(
			name VARCHAR(64) NOT NULL,
			PRIMARY KEY(name)
			)TYPE=InnoDB"
		);
		
		
		//box to group
		xDB::getDB()->query("
			CREATE TABLE box_to_group(
			box_group VARCHAR(64) NOT NULL,
			box_name VARCHAR(64) NOT NULL,
			UNIQUE (box_group,box_name),
			FOREIGN KEY (box_name) REFERENCES box(name) ON DELETE CASCADE,
			FOREIGN KEY (box_group) REFERENCES box_group(name) ON DELETE CASCADE
			)TYPE=InnoDB"
		);
		
		//menu_static_items
		xDB::getDB()->query("
			CREATE TABLE menu_item (
			id INT UNSIGNED NOT NULL,
			box_name VARCHAR(64) NOT NULL,
			label VARCHAR(128) NOT NULL,
			link VARCHAR(128) NOT NULL,
			weight TINYINT NOT NULL,
			parent INT UNSIGNED,
			PRIMARY KEY(id),
			INDEX(box_name),
			FOREIGN KEY (parent) REFERENCES menu_item(id) ON DELETE CASCADE,
			FOREIGN KEY (box_name) REFERENCES box(name) ON DELETE CASCADE
			)TYPE=InnoDB"
		);
		xUniqueId::createNew('menu_items');
		
		
		//item type
		xDB::getDB()->query("
			CREATE TABLE node_and_cathegory_type (
			name VARCHAR(32) NOT NULL,
			description VARCHAR(256) NOT NULL,
			PRIMARY KEY (name)
			)TYPE=InnoDB"
		);
		
		//item cathegory
		xDB::getDB()->query("
			CREATE TABLE cathegory (
			id INT UNSIGNED NOT NULL,
			name VARCHAR(32) NOT NULL,
			title VARCHAR(64) NOT NULL,
			type VARCHAR(32) NOT NULL,
			description TEXT NOT NULL,
			parent_cathegory INT UNSIGNED,
			PRIMARY KEY (id),
			UNIQUE(name),
			FOREIGN KEY (parent_cathegory) REFERENCES cathegory(id) ON DELETE CASCADE,
			FOREIGN KEY (type) REFERENCES node_and_cathegory_type(name) ON DELETE RESTRICT
			)TYPE=InnoDB"
		);
		xUniqueId::createNew('cathegory');
		
		
		//item
		xDB::getDB()->query("
			CREATE TABLE node (
			id INT UNSIGNED NOT NULL,
			title VARCHAR(256) NOT NULL,
			alias VARCHAR(255),
			type VARCHAR(32) NOT NULL,
			author VARCHAR(64) NOT NULL,
			content TEXT NOT NULL,
			content_filter VARCHAR(64) NOT NULL,
			creation_time DATETIME NOT NULL,
			edit_time DATETIME NOT NULL,
			UNIQUE (alias),
			PRIMARY KEY (id),
			FOREIGN KEY (type) REFERENCES node_and_cathegory_type(name) ON DELETE RESTRICT
			)TYPE=InnoDB"
		);
		xUniqueId::createNew('node');
		
		//item to cathegory
		xDB::getDB()->query("
			CREATE TABLE node_to_cathegory (
			nodeid INT UNSIGNED NOT NULL,
			catid INT UNSIGNED NOT NULL,
			UNIQUE (nodeid,catid),
			FOREIGN KEY (nodeid) REFERENCES node(id) ON DELETE CASCADE,
			FOREIGN KEY (catid) REFERENCES cathegory(id) ON DELETE CASCADE
			)TYPE=InnoDB"
		);
		
		//pageitem
		xDB::getDB()->query("
			CREATE TABLE node_page (
			nodeid INT UNSIGNED NOT NULL,
			published TINYINT NOT NULL,
			sticky TINYINT NOT NULL,
			accept_replies TINYINT NOT NULL,
			approved TINYINT NOT NULL,
			meta_description VARCHAR(128) NOT NULL,
			meta_keywords VARCHAR(128) NOT NULL,
			PRIMARY KEY (nodeid),
			FOREIGN KEY (nodeid) REFERENCES node(id) ON DELETE CASCADE
			)TYPE=InnoDB"
		);
		
		
		//pageitem
		xDB::getDB()->query("
			CREATE TABLE node_reply (
			nodeid INT UNSIGNED NOT NULL,
			parentid INT UNSIGNED NOT NULL,
			approved TINYINT NOT NULL,
			PRIMARY KEY (nodeid),
			FOREIGN KEY (parentid) REFERENCES node(id) ON DELETE CASCADE,
			FOREIGN KEY (nodeid) REFERENCES node(id) ON DELETE CASCADE
			)TYPE=InnoDB"
		);
		
		
		//xSettings::insertNew('site_name','');
		//xSettings::insertNew('site_description','');
		//xSettings::insertNew('site_keywords','');
		//xSettings::insertNew('site_theme','');
		
		$role = new xRole('administrator','Administrator');
		$role->dbInsert();
		$role = new xRole('authenticated','Authenticated user');
		$role->dbInsert();
		$role = new xRole('anonymous','Anonymous visitor');
		$role->dbInsert();
		
		
		$user = new xUser('','admin','root@localhost.com');
		$user->dbInsert('pass');
		$user->giveRole('administrator');
		
		
		$node_type = new xNodeType('page','Basic node type');
		$node_type->dbInsert();
		
		$cat = new xCathegory(-1,'page_root','Root cathegory','page','',NULL);
		$cat->dbInsert();
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
		$db->connect(xConf::get('db_host',''),xConf::get('db_name',''),xConf::get('db_user',''),xConf::get('db_pass',''),xConf::get('db_port',''));
		xDB::setDB($db);
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
