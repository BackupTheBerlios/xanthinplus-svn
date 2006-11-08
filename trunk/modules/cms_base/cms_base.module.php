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


function xm_load_cms_base()
{
	return new xModuleCmsBase();
}


/**
 * <strong> Weight = 0 <strong>
 */
class xModuleCmsBase extends xModule
{
	function xModuleCmsBase()
	{
		$this->xModule(0,'Provides xWidgetGroup,xPage,xContent objects and starts page creation workflow',
			'Mario Casciaro <xshadow [at] email (dot) it>','pre-alhpa5');	
	}
	
	
	/**
	 * @see xDummyModule::xm_install()
	 */
	function xm_install($db_name)
	{
		$db =& xDB::getDB();
		
		//widget group
		$db->query("
			CREATE TABLE widget_group (
			name VARCHAR(64) NOT NULL,
			PRIMARY KEY (name)
			)TYPE=InnoDB DEFAULT CHARACTER SET utf8"
		);
		
		//group to widget
		$db->query("
			CREATE TABLE group_to_widget (
			group_name VARCHAR(64) NOT NULL,
			class_name VARCHAR(64) NOT NULL,
			widget_name VARCHAR(64) NOT NULL,
			PRIMARY KEY (group_name,class_name,widget_name),
			FOREIGN KEY (group_name) REFERENCES widget_group(name)
			)TYPE=InnoDB DEFAULT CHARACTER SET utf8"
		);
	}
	
	
	/**
	 * @see xDummyModule::xm_createPage()
	 */
	function xm_createPage(&$path)
	{
		
	}
}



//###########################################################################
//###########################################################################
//###########################################################################


/**
 * 
 */
class xWidgetGroup extends xWidget
{
	var $m_widgets;
	
	/**
	 * 
	 */
	function xWidgetGroup($name,$widgets = array())
	{
		$this->xWidget($name);	
	}
	
	
	/**
	 * 
	 */
	function dbInsert()
	{
		xWidgetGroupDAO::insert($this);
	}
	
	/**
	 * 
	 */
	function dbUpdate()
	{
		xWidgetGroupDAO::update($this);
	}
	
	
	/**
	 * @static
	 */
	function find($name)
	{
		xWidgetGroupDAO::find();
	}
	
	
	/**
	 * {@inheritdoc}
	 */
	function _preconditions()
	{
		$result_set = new xResultSet();
		foreach($this->m_widgets as $widget)
			$result_set = $widget->preconditions();
		
		if($result_set->containsError())
			return new xErrorGroup($result_set->getErrors());
		elseif($result_set->containsValue(false))
			return false;
			
		return true;
	}
	
}


//###########################################################################
//###########################################################################
//###########################################################################


/**
 * Represent the whole web page
 */
class xPage extends xWidgetGroup
{
	function xPage(&$path)
	{
		$this->xWidgetGroup('page');
	}
	
	
	
}


?>