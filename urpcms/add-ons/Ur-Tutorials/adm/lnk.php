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
// | add-ons/[$addon_name]/lnk.php : V 0.0.2
// ==================================================================
// | [lnk.php] file must be present in each [add-on/[$addon_name]/adm]
// | directories if [$addon_name] Must be managed. The file is included
// | when the admin menu is built.
// |
// | $addon_name  : This variable was define before the [include]
// ******************************************************************

$page->icon("add-ons/".$addon_name."/adm/pics/menu.gif", $addon_name, "index.php?cmd=admin&amp;menu=$addon_name", "60");

?>
