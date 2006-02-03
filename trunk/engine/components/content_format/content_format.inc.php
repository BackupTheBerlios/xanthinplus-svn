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

require_once('engine/components/content_format/content_format.class.inc.php');
require_once('engine/components/content_format/bbcode.class.inc.php');

/*
*
*/
function xanth_content_format_apply_bbcode($hook_primary_id,$hook_secondary_id,$arguments)
{
	//$arguments[0] the content
	//$arguments[1] a reference to error string
	
	$bbparser = new xBBCodeParser($arguments[0]);
	
	$result = $bbparser->parse();
	$arguments[1] = $bbparser->last_error;
	
	return $result;
}

/*
*
*/
function xanth_content_format_apply_php($hook_primary_id,$hook_secondary_id,$arguments)
{
	//$arguments[0] the content
	//$arguments[1] a reference to error string
	
	ob_start();
	eval($arguments[0]);
	$result = ob_get_clean();
	
	return $result;
}

/*
*
*/
function xanth_content_format_apply_html($hook_primary_id,$hook_secondary_id,$arguments)
{
	//$arguments[0] the content
	//$arguments[1] a reference to error string
	
	return $arguments[0];
}

/*
*
*/
function xanth_content_format_apply_filtext($hook_primary_id,$hook_secondary_id,$arguments)
{
	//$arguments[0] the content
	//$arguments[1] a reference to error string
	$content = htmlspecialchars($arguments[0]);
	$content = nl2br($content);
	
	return $content;
}


/*
*
*/
function xanth_init_component_content_format()
{
	xanth_register_mono_hook(MONO_HOOK_CONTENT_FORMAT_APPLY, 'BBCode','xanth_content_format_apply_bbcode');
	xanth_register_mono_hook(MONO_HOOK_CONTENT_FORMAT_APPLY, 'Php source','xanth_content_format_apply_php');
	xanth_register_mono_hook(MONO_HOOK_CONTENT_FORMAT_APPLY, 'Full Html','xanth_content_format_apply_html');
	xanth_register_mono_hook(MONO_HOOK_CONTENT_FORMAT_APPLY, 'Filtered text','xanth_content_format_apply_filtext');
}



?>
