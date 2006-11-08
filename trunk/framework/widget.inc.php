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
 * Represents all objects ables to be rendered.
 */
class xRenderable
{
	/**
	 * 
	 */
	var $m_processed;
	
	/**
	 * 
	 */
	var $m_checked;
	
	/**
	 * 
	 */
	var $m_filtered;
	
	/**
	 * 
	 */
	function xRenderable()
	{
		$this->m_processed = false;
		$this->m_checked = false;
		$this->m_filtered = NULL;
	}
	
	/**
	 * Check preconditions for the current object, before render it.
	 * Usually you do not need to override this method. Override _preconditions() method otherwise.
	 * 
	 * @return mixed Returns true on valid preconditions, false if the creation workflow
	 * should fail silently, an xError object on fatal error.
	 */
	function preconditions()
	{
		$ret = $this->_preconditions();
		if($ret !== true)
			return $ret;
		
		$ret = xModuleManager::invoke('xm_checkPreconditionsExlusive',array(&$this));
		if($ret === true)
		{
			$this->m_checked = true;
			return true;
		}
		
		$ret = xModuleManager::invokeAll('xm_checkPreconditionsInclusive',array(&$this));
		if($ret->containsError())
			return new xErrorGroup($ret->getErrors());
		elseif($ret->containsValue(false))
			return false;
		
		$this->m_checked = true;
		return true;
	}
	
	
	/**
	 * Check preconditions for the current element, before render it.
	 * Usually you do not need to override this method. Override _preconditions() method otherwise.
	 * 
	 * @return @see xRenderable::preconditions()
	 */
	function _preconditions()
	{
	}
	
	
	/**
	 * Process and fills the contents of this object if necessary, so they are ready to be rendered.
	 * Usually you do not need to override this method. Override _process() method otherwise.
	 * 
	 * @return NULL
	 */
	function process()
	{
		if(!$this->m_checked)
		{
			xLog::log(LOG_LEVEL_WARNING,'This object must be checked before to be processed. Dump: '.
				var_export($this,true),__FILE__,__LINE__);
			return;					
		}
		
		xModuleManager::invokeAll('xm_preprocess',array(&$this));
		$this->_process();
		xModuleManager::invokeAll('xm_postprocess',array(&$this));
		
		$this->m_processed = true;
	}
	
	
	/**
	 * Excecutes class-specific content processing functions.
	 * Override this method to satisfy you class specific processing needs.
	 * 
	 * @see xRenderable::process()
	 */
	function _process()
	{
	}
	
	/**
	 * Create a copy of this object and filters its contents before rendering. 
	 * This function creates a copy of the object in $this->m_filtered, becouse after
	 * processing its contents, they are usually non consistent with the original object logic.
	 * Usually you do not need to override this method. Override _filter() method otherwise.
	 * 
	 * @return NULL
	 */
	function filter()
	{
		if(!$this->m_processed)
		{
			xLog::log(LOG_LEVEL_WARNING,'This object must be processed before to be filtered. Dump: '.
				var_export($this,true),__FILE__,__LINE__);
			return;					
		}
		
		$copy = xanth_clone($this);
		xModuleManager::invokeAll('xm_prefilter',array(&$copy));
		$copy->_filter();
		xModuleManager::invokeAll('xm_postfilter',array(&$copy));
		
		$this->m_filtered = $copy;
	}
	
	
	/**
	 * Apply object-specific filters before its contents are rendered. 
	 * Usually this method is called on a copy of the original object, and after
	 * the prefilter stage, so it should not return nothing.
	 * Override this method to satisfy you class specific processing needs.
	 * 
	 * @return NULL
	 */
	function _filter()
	{
	}
	
	/**
	 * Render the widget using a module call. This function have some effect only 
	 * if the object was checked with preconditions(), processed and filtered.
	 * Usually you do not need to override this method.
	 *
	 * @return string A string representing the renderized element.
	 */
	function render()
	{
		if($this->m_checked && $this->m_processed && $this->m_filtered !== NULL)
			return xModuleManager::invoke('xm_render',array(&$this->filtered));
		
		return '';
	}
}



/**
 * Widgets are visual elements easily identifiable by a name, a type.
 */
class xWidget extends xRenderable
{
	var $m_name;
	
	/**
	 * 
	 */
	function xWidget($name)
	{
		$this->xRenderable();
		$this->m_name = $name;
	}
};


?>