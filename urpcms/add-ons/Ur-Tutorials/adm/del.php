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
// | V 0.0.1     	: The first Alpha release.
// ==================================================================
// | This file is included at each [del] call. DEL is an [$adm] function.
// ==================================================================
// | $table         : String > Table mane where the entry will be deleted.
// | $where         : String > SQL WHERE condition. (Ex : "item_id='$id'")
// | $call_function : String > Function name which will be called after the operation, if needed.
// ==================================================================

switch(strtolower($name)) {
	// ========================================================
	// Delete a tutorial from database.
	// The comfirm request and error pop-up is automaticly managed
	// by the DEL command of [$adm] object.
	// ========================================================
	case "tutorial":
		$table = $addon_prefix."_urtutorials";
		$where = "tutid='$id'";
		$call_function = "urtut_del_linkedchapters";
		break;
}

function urtut_del_linkedchapters() {

}

?>