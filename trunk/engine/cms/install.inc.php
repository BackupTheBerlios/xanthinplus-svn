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
		
		//access filter set
		xDB::getDB()->query("
			CREATE TABLE access_filter_set (
			id INT UNSIGNED AUTO_INCREMENT NOT NULL,
			name VARCHAR(32) NOT NULL,
			description VARCHAR(256) NOT NULL,
			PRIMARY KEY(id)
			)TYPE=InnoDB"
		);
		
		//access filter role
		xDB::getDB()->query("
			CREATE TABLE access_filter_role (
			filterid INT UNSIGNED NOT NULL,
			roleName VARCHAR(32) NOT NULL,
			UNIQUE(filterid,roleName),
			INDEX(filterid),
			INDEX(roleName),
			FOREIGN KEY (filterid) REFERENCES access_filter_set(id) ON DELETE CASCADE,
			FOREIGN KEY (roleName) REFERENCES role(name) ON DELETE CASCADE
			)TYPE=InnoDB"
		);
		
		//access filter path include
		xDB::getDB()->query("
			CREATE TABLE access_filter_path_include (
			filterid INT UNSIGNED NOT NULL,
			incpath VARCHAR(128) NOT NULL,
			UNIQUE(filterid),
			INDEX(filterid),
			FOREIGN KEY (filterid) REFERENCES access_filter_set(id) ON DELETE CASCADE
			)TYPE=InnoDB"
		);
		
		//access filter path exclude
		xDB::getDB()->query("
			CREATE TABLE access_filter_path_exclude (
			filterid INT UNSIGNED NOT NULL,
			excpath VARCHAR(128) NOT NULL,
			UNIQUE(filterid),
			INDEX(filterid),
			FOREIGN KEY (filterid) REFERENCES access_filter_set(id) ON DELETE CASCADE
			)TYPE=InnoDB"
		);
		
		//access permission
		xDB::getDB()->query("
			CREATE TABLE access_permission (
			name VARCHAR(64) NOT NULL,
			filterset INT UNSIGNED NOT NULL,
			PRIMARY KEY(name),
			INDEX(filterset),
			FOREIGN KEY (filterset) REFERENCES access_filter_set(id) ON DELETE RESTRICT
			)TYPE=InnoDB"
		);
		
		//box
		xDB::getDB()->query("
			CREATE TABLE box(
			name VARCHAR(64) NOT NULL,
			title VARCHAR(255) NOT NULL,
			area VARCHAR(32),
			type VARCHAR(32) NOT NULL,
			weight TINYINT NOT NULL,
			filterset INT UNSIGNED,
			PRIMARY KEY(name),
			INDEX(filterset),
			FOREIGN KEY (filterset) REFERENCES access_filter_set(id) ON DELETE SET NULL
			)TYPE=InnoDB"
		);
		
		//static box
		xDB::getDB()->query("
			CREATE TABLE box_static(
			box_name VARCHAR(64) NOT NULL,
			content TEXT NOT NULL,
			content_filter VARCHAR(64) NOT NULL,
			PRIMARY KEY (box_name),
			FOREIGN KEY (box_name) REFERENCES box(name) ON DELETE CASCADE
			)TYPE=InnoDB"
		);
		
		//menu_static_items
		xDB::getDB()->query("
			CREATE TABLE menu_items (
			box_name VARCHAR(32) NOT NULL,
			id INT UNSIGNED AUTO_INCREMENT NOT NULL,
			label VARCHAR(128) NOT NULL,
			link VARCHAR(128) NOT NULL,
			weight TINYINT NOT NULL,
			parent INT UNSIGNED,
			accessfiltersetid INT UNSIGNED,
			PRIMARY KEY(id),
			INDEX(box_name),
			FOREIGN KEY (parent) REFERENCES menu_items(id) ON DELETE CASCADE,
			FOREIGN KEY (box_name) REFERENCES box(name) ON DELETE CASCADE,
			FOREIGN KEY (accessfiltersetid) REFERENCES access_filter_set(id) ON DELETE SET NULL
			)TYPE=InnoDB"
		);
		
		
		//item type
		xDB::getDB()->query("
			CREATE TABLE item_type (
			name VARCHAR(32) NOT NULL,
			description VARCHAR(256) NOT NULL,
			default_content_filter VARCHAR(64) NOT NULL,
			default_approved TINYINT NOT NULL,
			default_published TINYINT NOT NULL,
			default_sticky TINYINT NOT NULL,
			default_weight TINYINT NOT NULL,
			default_accept_replies TINYINT NOT NULL,
			accessfiltersetid INT UNSIGNED,
			PRIMARY KEY (name),
			FOREIGN KEY (accessfiltersetid) REFERENCES access_filter_set(id) ON DELETE SET NULL
			)TYPE=InnoDB"
		);
		
		
		//item cathegory
		xDB::getDB()->query("
			CREATE TABLE item_cathegory (
			id INT UNSIGNED AUTO_INCREMENT NOT NULL,
			name VARCHAR(32) NOT NULL,
			description TEXT NOT NULL,
			accessfiltersetid INT UNSIGNED,
			parent_cathegory INT UNSIGNED,
			items_type VARCHAR(32),
			PRIMARY KEY (id),
			UNIQUE(name),
			FOREIGN KEY (accessfiltersetid) REFERENCES access_filter_set(id) ON DELETE SET NULL,
			FOREIGN KEY (parent_cathegory) REFERENCES item_cathegory(id) ON DELETE CASCADE,
			FOREIGN KEY (items_type) REFERENCES item_type(name) ON DELETE SET NULL
			)TYPE=InnoDB"
		);
		
		
		//item
		xDB::getDB()->query("
			CREATE TABLE item (
			id INT UNSIGNED NOT NULL AUTO_INCREMENT,
			title VARCHAR(256) NOT NULL,
			type VARCHAR(32) NOT NULL,
			author VARCHAR(64) NOT NULL,
			content TEXT NOT NULL,
			content_filter VARCHAR(64) NOT NULL,
			published TINYINT NOT NULL,
			approved TINYINT NOT NULL,
			accept_replies TINYINT NOT NULL,
			sticky TINYINT NOT NULL,
			weight TINYINT NOT NULL,
			description VARCHAR(512) NOT NULL,
			keywords VARCHAR(128) NOT NULL,
			creation_time DATETIME NOT NULL,
			lastedit_time DATETIME,
			PRIMARY KEY (id),
			FOREIGN KEY (type) REFERENCES item_type(name) ON DELETE RESTRICT
			)TYPE=InnoDB"
		);
		
		//replies
		xDB::getDB()->query("
			CREATE TABLE item_replies (
			parentid INT UNSIGNED NOT NULL,
			childid INT UNSIGNED NOT NULL,
			FOREIGN KEY (parentid) REFERENCES item(id) ON DELETE CASCADE,
			FOREIGN KEY (childid) REFERENCES item(id) ON DELETE CASCADE
			)TYPE=InnoDB"
		);
		
		//catogory items
		xDB::getDB()->query("
			CREATE TABLE item_to_cathegory (
			itemid INT UNSIGNED NOT NULL,
			catid INT UNSIGNED NOT NULL,
			FOREIGN KEY (itemid) REFERENCES item(id) ON DELETE CASCADE,
			FOREIGN KEY (catid) REFERENCES item_cathegory(id) ON DELETE CASCADE
			)TYPE=InnoDB"
		);
		
		
		
		$role = new xRole('administrator','Administrator');
		$role->dbInsert();
		$role = new xRole('authenticated','Authenticated user');
		$role->dbInsert();
		$role = new xRole('anonymous','Anonymous visitor');
		$role->dbInsert();
		
		
		$user = new xUser('','admin','root@localhost.com');
		$user->dbInsert('pass');
		$user->giveRole('administrator');
		
		
		$acc_filter = new xAccessFilterSet(-1,'Default admin sections','',array(new xAccessFilterRole('administrator')));
		$acc_filter->dbInsert();
		$perm = new xAccessPermission('manage box',$acc_filter->m_id);
		$perm->dbInsert();
		$perm = new xAccessPermission('admin item',$acc_filter->m_id);
		$perm->dbInsert();
		$perm = new xAccessPermission('admin item create',$acc_filter->m_id);
		$perm->dbInsert();
		
		
		//create some default box
		$box = new xBox('Login','Login','dynamic',0,NULL,'leftArea');
		$box->dbInsert();
		
		//menus
		$menu = new xMenu('Admin','Admin','menu',0,array(),NULL,'leftArea');
	
		$menuitem = new xMenuItem('Homepage','?',0);
		$menu->m_items[] = $menuitem;
		
		$menuitem = new xMenuItem('Manage Boxes','?p=admin/box',0);
		$menu->m_items[] = $menuitem;
		
		$menuitem = new xMenuItem('Manage Items','?p=admin/item',0);
		$menuitem->m_subitems[] = new xMenuItem('Create Item (Generic)','?p=admin/item/create',0);
		$menu->m_items[] = $menuitem;
		
		$menuitem = new xMenuItem('Manage Access Filters','?p=admin/accessfilters',0);
		$menuitem->m_subitems[] = new xMenuItem('Manage Access Permission','?p=admin/accesspermissions',0);
		$menu->m_items[] = $menuitem;
		
		$menu->dbInsert();
		
		$item_type = new xItemType('article','A generic article','html',true,true,false,0,true,NULL);
		$item_type->dbInsert();
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
