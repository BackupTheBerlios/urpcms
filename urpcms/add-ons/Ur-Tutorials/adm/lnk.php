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
// | V 0.0.1     : Alpha version.
// ==================================================================
// | [lnk.php] file must be present in each [add-on/[$addon_name]/adm]
// | directories if [$addon_name] Must be managed. The file is included
// | when the admin menu is built.
// |
// | $addon_name  : This variable was define before the [include]
// ******************************************************************

/*
// ******************************************************************
// AUTHORIZED GROUPS 
// ******************************************************************
// In addition to Master, you can authorize one or more other groups.
// Indicate the names in following variable. If there is several groups,
// use a comma to separate them. If there is no groups, only the Master
// can reach it...
// ******************************************************************
$groups_adm = "";
// ******************************************************************

if ($user->member_of($group_needed)) {				// You don't have need to declare the Master User, member of all groups.
													// The [member_of] function will return True if the user is menber of Master group.
													
*/
	$page->icon("add-ons/".$addon_name."/adm/pics/menu.gif", $addon_name, "index.php?cmd=admin&amp;menu=$addon_name", "60");
/*
}
*/
?>