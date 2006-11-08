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
	function xRenderable()
	{
	}
	
	/**
	 * Check preconditions for the current object, before render it.
	 * Usually you do not need to override this method. Override _preconditions() method otherwise.
	 * 
	 * @return mixed Returns an xError object on error
	 */
	function preconditions()
	{
		xModuleManager::invoke('xm_checkPreconditions',array(&$copy));
	}
	
	
	/**
	 * Check preconditions for the current element, before render it.
	 * Usually you do not need to override this method. Override _preconditions() method otherwise.
	 * 
	 * @return mixed Returns an xError object on error
	 */
	function _preconditions()
	{
		xModuleManager::invoke('xm_checkPreconditions',array(&$copy));
	}
	
	
	/**
	 * Process and fills the contents of this object if necessary, so they are ready to be rendered.
	 * Usually you do not need to override this method. Override _process() method otherwise.
	 * 
	 * @return NULL
	 */
	function process()
	{
		xModuleManager::invokeAll('xm_preprocess',array(&$this));
		$this->_process();
		xModuleManager::invokeAll('xm_postprocess',array(&$this));
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
	 * This function returns a copy of the object, becouse after
	 * processing its contents, they are usually non consistent with the original object logic.
	 * Usually you do not need to override this method. Override _filter() method otherwise.
	 * 
	 * @return xRenderable
	 */
	function filter()
	{
		$copy = xanth_clone($this);
		xModuleManager::invokeAll('xm_prefilter',array(&$copy));
		$copy->_filter();
		xModuleManager::invokeAll('xm_postfilter',array(&$copy));
		
		return $copy;
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
	 * Render the widget using a module call after processing it's contents. 
	 * Usually you do not need to override this method.
	 *
	 * @return string A string representing the renderized element.
	 */
	function render()
	{
		$this->process();
		$filtered = $this->filter();
		return xModuleManager::invoke('xm_render',array(&$filtered));
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