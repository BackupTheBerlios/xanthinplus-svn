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
	$text = '[b][i]argumen[/i][u]t[/u][/b] fsd[color=#FF0]Hello![/color]f[size=24]sdf[/size][code] some code[/code]';
	$text .= "[list=square][li]test[b][/b][/li][/list]&#058;";
	$text .= "[b]\n asdasdad[/b] asdad [url=http://testf]http://test[/url] [url]http://anoterlink[/url]";
	$text .= "<br><br> [url=http://localhost/][img]http://localhost/xanthinplus/xfiles/subversion.png[/img][/url]";
	$bbparser = new xBBCodeParser($text);
	$result = $bbparser->parse();
	
	if($result)
	{
		$result = "TRUE";
		return  "<br>$result <br><br>BBtext:<br> $text<br><br> HtmlResult:<br>" .htmlspecialchars($bbparser->htmltext). 
		"<br><br>Result:<br>". $bbparser->htmltext;
	}	
	else
	{
		$result = "FALSE";
		return "<br>Error:<br>".$bbparser->last_error;
	}
		
	
}


/*
*
*/
function xanth_init_component_test()
{
	xanth_register_mono_hook(MONO_HOOK_MAIN_ENTRY_CREATE, 'test','xanth_test_create');
}



?>