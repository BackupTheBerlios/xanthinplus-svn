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

/*
*
*/
function xanth_test_create($hook_primary_id,$hook_secondary_id,$arguments)
{
	$text = '[Tes=ts][b]dasd[/b]as[/tes][/b]';
	$bbparser = new xBBCodeParser($text,TRUE);
	$result = $bbparser->parse();
	
	if($result)
		$result = "TRUE";
	else
		$result = "FALSE";
		
	return  "$result";
}


/*
*
*/
function xanth_init_component_test()
{
	xanth_register_mono_hook(MONO_HOOK_MAIN_ENTRY_CREATE, 'test','xanth_test_create');
}



?>