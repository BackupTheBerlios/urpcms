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
// | add-ons/[$addon_name]/add.php : V 0.0.2
// ==================================================================
// | This file is included at each [add] call. ADD is an [adm] function.
// ==================================================================
// | $table         : Table mane where the entry will be add.
// | $fields        : Fields list to put values
// | $values        : Values list
// | $call_function : Function name which will be called after the operation (Empty=No call)
// | $errmsg        : If this variable is not empty, the request will not executed and the string will dispayed.
// ==================================================================

switch(strtolower($name)) {
	// ========================================================
	// Add a New Tutorial
	// ========================================================
	case "tutorial";
    // GETS THE POSTED VALUES
    $tuttitle = $_POST['tuttitle'];

		// Check For Bad Values...
		if (trim($tuttitle) == '') {$errmsg = ADM_ERR_NOEMPTYNAME;}			
		// Request Items
		$table = $addon_prefix."_urtutorials";
		$fields = "tutid, tut_title";
		$values = "'NULL', '$tuttitle'";
		$call_function = "";
		$return_menu = $addon;	
		break;
}

?>
