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
 * 
 */
class xAccessControl
{
	function xAccessControl()
	{
		assert(false);
	}
	
	/**
	 * 
	 */
	function checkAccess($lang,$resource,$action,$resource_type = NULL,$resource_id = NULL)
	{
		//todo
		//xModuleManager::invokeAll('xm_checkAccess',array(&$this));
		
		return true;
	}
}



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
	var $m_prec_checked;
	
	/**
	 * 
	 */
	var $m_perm_checked;
	
	/**
	 * 
	 */
	var $m_filtered;
	
	/**
	 * 
	 */
	function xRenderable()
	{
		$this->m_processed = NULL;
		$this->m_checked = NULL;
		$this->m_filtered = NULL;
	}
	
	
	/**
	 * @access private
	 */
	function _recursive_preconditions()
	{
		
	}
	
	
	/**
	 * Check preconditions for the current object, before render it.
	 * Usually you do not need to override this method. Override _preconditions() method otherwise.
	 * 
	 * @return mixed Returns a checked widget(that can or cannot be the same object you 
	 * called preconditions()) or false if the creation workflow of this widget should fail silently.
	 */
	function preconditions()
	{
		$this->m_checked = false;
		
		$ret = $this->_preconditions();
		if($ret !== true)
			return $ret;
		
		
		
		$this->m_checked = true;
		return true;
	}
	
	
	/**
	 * 
	 */
	function permissions()
	{
		
	}
	
	/**
	 * 
	 */
	function _permissions()
	{
		
	}
	
	
	/**
	 * Check preconditions for the current element, before render it.
	 * Usually you do not need to override this method. Override _preconditions() method otherwise.
	 * 
	 * @return mixed 
	 * @see xRenderable::preconditions()
	 */
	function _preconditions()
	{
		return true;
	}
	

	/**
	 * Process and fills the contents of this object if necessary, so they are ready to be 
	 * filtered and rendered.
	 * Usually you do not need to override this method. Override _process() method otherwise.
	 * 
	 * @return bool true on success false otherwise
	 */
	function process()
	{
		$this->m_processed = false;
		if($this->m_checked === true)
		{
			$res = xModuleManager::invokeAll('xm_preprocess',array(&$this));
			if($res->containsValue(false))
				return false;
			
			if($this->_process() === false)
				return false;
				
			$res = xModuleManager::invokeAll('xm_postprocess',array(&$this));
			if($res->containsValue(false))
				return false;
			
			$this->m_processed = true;
			return true;
			
		}
		elseif($this->m_checked === NULL)
			xLog::log(LOG_LEVEL_WARNING,'This object must be checked before to be processed. Dump: '.
				var_export($this,true),__FILE__,__LINE__);

		return false;
	}

	
	/**
	 * Excecutes class-specific content processing functions.
	 * Override this method to satisfy you class specific processing needs.
	 * 
	 * @see xRenderable::process()
	 */
	function _process()
	{
		return true;
	}
	
	/**
	 * Create a copy of this object and filters its contents before rendering. 
	 * This function creates a copy of the object in $this->m_filtered, becouse after
	 * processing its contents, they are usually non consistent with the original object logic.
	 * Usually you do not need to override this method. Override _filter() method otherwise.
	 * 
	 * @return bool true on success false otherwise
	 */
	function filter()
	{
		$this->m_filtered = false;
		if($this->m_processed === true)
		{
			$copy = xanth_clone($this);
		
			$res = xModuleManager::invokeAll('xm_prefilter',array(&$copy));
			if($res->containsValue(false))
				return false;
				
			if($copy->_filter() === false)
				return false;
				
			$res = xModuleManager::invokeAll('xm_postfilter',array(&$copy));
			if($res->containsValue(false))
				return false;
			
			$this->m_filtered = $copy;
			return true;
		}
		elseif($this->m_processed == FALSE)
			xLog::log(LOG_LEVEL_WARNING,'This object must be processed before to be filtered. Dump: '.
				var_export($this,true),__FILE__,__LINE__);
		
		return false;
	}
	
	
	/**
	 * Apply object-specific filters before its contents are rendered. 
	 * Usually this method <strong>is called on a copy</string> of the original object, and after
	 * the prefilter stage, so it should not return nothing.
	 * Override this method to satisfy you class specific processing needs.
	 * 
	 * @return NULL
	 */
	function _filter()
	{
		return true;
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
		if(xanth_instanceof($this->m_filtered,get_class($this)))
		{
			$res = xModuleManager::invoke('xm_render',array(&$this->m_filtered));
			if($res === NULL)
				$res = '';
				
			$this->m_filtered->_render($res);
			return $res;
		}
		elseif($this->m_filtered === NULL)
			xLog::log(LOG_LEVEL_WARNING,'This object must be filtered before to be rendered. Dump: '.
				var_export($this,true),__FILE__,__LINE__);
	
		return '';	
	}
	
	
	/**
	 * Executes object specific rendering needs.Usually this method <strong>is called on a copy</string> ,
	 * specifically on the filtered copy.
	 * Override this method to satisfy you class specific processing needs.
	 *
	 * @param string $res The pre-rendered output.
	 * @return string A string representing the renderized element.
	 */
	function _render(&$res)
	{
	}
	
	
	/**
	 * Executes preconditions(), process(),filter() and render() and return the string
	 * resulting from this workflow. 
	 * Usually you do not need to override this method.
	 */
	function display()
	{
		$res = $this->preconditions();
		if(xError::isError($res))
		{
			//todo
			return var_export($res,true);
		}
		elseif($res === true)
		{
			if($this->process())
				if($this->filter())
					return $this->render();
		}
		
		return '';
	}
}


/**
 * 
 */
class xRenderableDecorator extends xRenderable
{
	
	function render()
	{
		$this->m_decorated->render();
			
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
	
	
	/**
	 * Loads a widget.
	 * Override this method to satisfy you class specific processing needs.
	 */
	function load($name)
	{
		return new  xWidget($name);
	}
};


/**
 * 
 */
class xWidgetDecorator extends xWidget
{
	
}



/**
 * Static widgets are widgets in wich you can call display() statically
 */
class xStaticWidget extends xWidget
{
	/**
	 * 
	 */
	function xStaticWidget()
	{
		$this->xWidget('static');
	}
	
	
	/**
	 * Loads a widget.
	 * Override this method to satisfy you class specific processing needs.
	 */
	function load($name)
	{
		return new xStaticWidget();
	}
};


/**
 * 
 */
class xWidgetUtilities
{
	/**
	 * 
	 */
	function xWidgetUtilities()
	{
		assert(false);
	}
}



?>