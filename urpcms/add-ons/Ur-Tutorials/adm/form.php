<?php

// ==================================================================
// | UrPCMS : Your Powerful Content Management System
// ==================================================================
// |
// | Copyright (c) 2003 UrStudios.Net by Bruno Cluizel
// | http://www.urpcms.com
// |
// | This program is free software; you can redistribute it and/or
// | modify it under the terms of the GNU General Public License as
// | as published by the Free Software Foundation; version 2 of the License.
// |
// | V 0.0.1     : The first Alpha version.
// ==================================================================
// | This file is included at each [form] call. Form is an [adm] function.
// | The [$name] swicth must be here. Otherwise, the code will be 
// | executed each time.
// ==================================================================

switch ($name) {

	case "Ur-Tutorials":
		$page->title_content(ADM_TITLE_ADMINOF.$name, "kernel/pics/ic-48/config.gif");
		echo "<br>\n<table align=\"center\" border=\"0\" cellpadding=\"2\" cellspacing=\"1\">";
		echo "<tr><td bgcolor=\"".$tab_title_bgcolor."\"><b>".URTUT_TITLE_TUT."&nbsp;</b></td><td bgcolor=\"".$tab_title_bgcolor."\"><b></b></td>"
			."<td bgcolor=\"".$tab_title_bgcolor."\"></td><td bgcolor=\"".$tab_title_bgcolor."\">&nbsp;</td></tr>";
		urtut_build_tutorial_list();
		echo "\n</table><br>\n";				
		$page->html_table_end();
		
		$page->html_table_start(true);
		$page->title_content(URTUT_TITLE_ADDTUT, "kernel/pics/ic-48/config.gif");
		// ADD FORM
		echo "<br>\n<form action=\"index.php\" method=\"post\">"
			."<input type=\"hidden\" name=\"cmd\" value=\"admin\">"
			."<input type=\"hidden\" name=\"addon\" value=\"".$name."\">"
			."<input type=\"hidden\" name=\"menu\" value=\"tutorial\">"
			."<input type=\"hidden\" name=\"act\" value=\"add\">\n";
		echo "<table align=\"center\" border=\"0\" cellpadding=\"2\" cellspacing=\"0\">";
		echo "<tr><td bgcolor=\"".$tab_bgcolor."\"><font class=\"text_big_dark\">".URTUT_TUTTITLE."</font></td>"
			."<td bgcolor=\"".$tab_bgcolor."\">"
			."<input type=\"text\" name=\"tuttitle\" size=\"36\" maxlength=\"64\" style=\"width: 250\"></td></tr>";
		echo $trow4;
		echo "<tr><td align=\"center\" bgcolor=\"".$tab_bgcolor."\" colspan=\"2\"><font class=\"text_big_dark\">"
			."<input type=\"submit\" value=\"".TXT_ADD."\" style=\"width: 150\"></font></td></tr>";
		echo $trow4;
		echo "\n</table>\n</form>";
	break;

}


function urtut_build_tutorial_list() {
	global $db, $db_prefix, $addon_prefix, $tab_bgcolor, $tab_title_bgcolor2, $tab_title_bgcolor;

	// GET TUTORIALS LIST
	$res = $db->sql_query("SELECT tutid, tut_title FROM ".$db_prefix."_".$addon_prefix."_urtutorials ORDER BY tut_title");
	while(list($tutid, $tut_title) = $db->sql_fetchrow($res)) {
	
		echo "<tr><td bgcolor=\"".$tab_bgcolor."\"><b>".$tut_title."</b></td>"
			."<td bgcolor=\"".$tab_bgcolor."\"></td><td bgcolor=\"".$tab_bgcolor."\"></td>"
			."<td bgcolor=\"".$tab_bgcolor."\">"
			."<a href=\"index.php?cmd=admin&&addon=Ur-Tutorials&menu=tutorial&act=delete&id=".$tutid."\">".TXT_DELETE."</a>"
			."</td></tr>";
	}
}

?>