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
// | adm.php : V 0.0.5
// ==================================================================

if (eregi("adm.php", $_SERVER['PHP_SELF'])) {Header("Location: ../index.php");die();}

if (@file_exists("kernel/locale/adm.$language.php")) {require_once("kernel/locale/adm.$language.php");}

// Verifies if the user is logged and have at least one admin privilege.
if ($user->name != "") {
	$tst = $user->is_admin();
	if ($tst == false) {
		// ACCESS DENIED !
		//		
		$page->html_header(TXT_LOG_LOGINTITLE);
		$page->MsgBox(ERR_ACC_DENIED, TXT_OK, "", 0, "index.php");
		$page->html_foot();
		die();
	}
} else {
	// CONNECTING FORM IF THE USER IS NOT LOGGED...
	//
	$code = make_code();
	$ccode = encrypt_data($code);
	setcookie("session_code", $ccode, time() + $timezone + 3600);
	$page->html_header(TXT_LOG_LOGINTITLE);
	$page->title_page(TXT_LOG_LOGINTITLE, 2);
	$page->form_login($code, "admin", TXT_LOG_TXTACCESS);
	$page->html_foot();
	die();
}

class ur_adm {

	// ******************************************************************
	// * MAIN MENU FUNCTIONS                                            *
	// ******************************************************************
	function main_menu() {
		global $addon_list, $addon_right, $addon_list_name, $addon_list_id, $page, $user, $language;

		// ========================================================
		// TAKE THE ADD-ONS FILES LIST
		// ========================================================
		$hd = opendir("add-ons");
		while ($file = readdir($hd)) {
			if ($file != ".") {
				if ($file != "..") {
					if ($file != "index.html") {$addon_list[] = $file;}
				}
			}
		}
		closedir($hd);
		// ========================================================

		// ========================================================
		// Build the administration menu
		// ========================================================
    $cmp = 0;
		$page->html_table_start(true);

		// Menu table
		echo "<table align=\"center\"><tr>\n";
		
		// ADD-ONS
		if ($user->have_nativ_right("addons")) {
  		$cmp++;
  		echo "<td align=\"center\">\n";
  		$page->icon("kernel/pics/adm/add-ons.gif", TXT_ADDONS, "index.php?cmd=admin&amp;menu=addons", "80");
  		echo "</td>\n";
    }
		// ASPECT
		if ($user->have_nativ_right("aspect")) {		
   		$cmp++;
  		echo "<td align=\"center\">\n";
  		$page->icon("kernel/pics/adm/aspect.gif", TXT_ASPECT, "index.php?cmd=admin&amp;menu=aspect", "80");
  		echo "</td>\n";
    }  		
		// COLORS
		if ($user->have_nativ_right("colors")) {		
  		$cmp++;
  		echo "<td align=\"center\">\n";
  		$page->icon("kernel/pics/adm/colors.gif", TXT_COLORS, "index.php?cmd=admin&amp;menu=colors", "80");
  		echo "</td>\n";
    }  		
		// GROUPS
		if ($user->have_nativ_right("groups")) {
  		$cmp++;		
  		echo "<td align=\"center\">\n";
  		$page->icon("kernel/pics/adm/groups.gif", TXT_GROUPS, "index.php?cmd=admin&amp;menu=groups", "80");
  		echo "</td>\n";
    }  		
		// INDEX ICON
		if ($user->have_nativ_right("index")) {
  		$cmp++;		
  		echo "<td align=\"center\">\n";
  		$page->icon("kernel/pics/adm/index.gif", TXT_INDEX, "index.php?cmd=admin&amp;menu=index", "80");
  		echo "</td>\n";
    }  		
		// PANELS
		if ($user->have_nativ_right("panels")) {
  		$cmp++;		
  		echo "<td align=\"center\">\n";
  		$page->icon("kernel/pics/adm/aspect.gif", TXT_PANELS, "index.php?cmd=admin&amp;menu=panels", "80");
  		echo "</td>\n";
    }  		
		// SECURITY
		if ($user->have_nativ_right("security")) {
  		$cmp++;
			if ($cmp == 7) {$cmp = 0;	echo "</tr><tr>\n";}  		
  		echo "<td align=\"center\">\n";
  		$page->icon("kernel/pics/adm/security.gif", TXT_SECURITY, "index.php?cmd=admin&amp;menu=security", "80");
  		echo "</td>\n";  		
    }  		
		// USERS
		if ($user->have_nativ_right("users")) {
  		$cmp++;
			if ($cmp == 7) {$cmp = 0;	echo "</tr><tr>\n";}      	
  		echo "<td align=\"center\">\n";
  		$page->icon("kernel/pics/adm/users.gif", TXT_USERS, "index.php?cmd=admin&amp;menu=users", "80");
  		echo "</td>\n"; 		
    }  		
		// ADD-ONS ICONS
		$cmpt = count($addon_list_name);
		for ($v=0; $v<$cmpt; $v++) {
			$addon_name = $addon_list_name[$v];
			$addon_right = $user->addon_right($addon_list_id[$v]);				
			if ($addon_right > 2) {
				if (@file_exists("add-ons/".$addon_name."/adm/lnk.php")) {
					$cmp = $cmp + 1;
					if ($cmp == 7) {$cmp = 0;	echo "</tr><tr>\n";}
					echo "<td align=\"center\">\n";
					include("add-ons/".$addon_name."/adm/lnk.php");
					echo "</td>\n";
				}
			}
		}
		// LOGOUT ICON
		$cmp = $cmp + 1;
		if ($cmp == 7) {
			$cmp = 0;
			echo "</tr><tr>\n";
		}
		echo "<td align=\"center\">\n";
		$page->icon("kernel/pics/adm/logout.gif", TXT_LOGOUT, "index.php?cmd=logout", "60");
		echo "</td>\n";

		echo "</tr></table>\n";

		$page->html_table_end();
	}
	// ******************************************************************	

	// ******************************************************************
	// * STANDARD ACTIONS FUNCTIONS                                     *
	// ******************************************************************
	// * add      : Add Entries In Database                             *
	// * del      : Delete An Entries In Database                       *
	// * edit     : Edit Entries From Database                          *
	// * form     : Display the Main Form                               *  	
	// * modify   : Change Values In Database                           *
	// ******************************************************************
	function add($name) {
		// ========================================================
		// Add Entries In Database
		// ========================================================
		// Entry variables :
		// -----------------
		// - $name     : Heading Name. Used by the Switch Case
		// ========================================================
		// Internal variables :
		// --------------------
		// - $table         : Table mane where the entry will be add.
		// - $fields        : Fields list to put values
		// - $values        : Values list
		// - $call_function : Function name which will be called after the operation (Empty=No call)
		// - $errmsg        : If this variable is a string, the request will not executed and the string will dispayed.
		// ========================================================
		global $db, $db_prefix, $acc_prefix, $page, $addon;

    $errmsg = "";
    
		if ($addon == "") {
			// NO ADD-ON COMMAND
			switch(strtolower($name)) {
				// ========================================================
				// Add a New Group
				// ========================================================
				case "groups";					
          // GETS THE POSTED VALUES
          $grpname = $_POST['grpname'];
          $grpdesc = $_POST['grpdesc'];
	
					// Check For Bad Values...
					if (trim($grpname) == '') {$errmsg = ADM_ERR_NOEMPTYNAME;}				
					// Request Items
					$table = $acc_prefix."_groups";
					$fields = "groupid, group_name, group_desc";
					$values = "'NULL', '$grpname', '$grpdesc'";
					$call_function = "db_update_groups_set";
					break;
	
				// ========================================================
				// Add a New Panel
				// ========================================================
				case "panels";
          // GETS THE POSTED VALUES
          $pantit = $_POST['pantit'];
          $pantype = $_POST['pantype'];
          $pantxt = $_POST['pantxt'];
          $panaddon = $_POST['panaddon'];
          $posw = $_POST['posw'];
	
					// Check For Bad Values...
					if (($posw < 0) || ($posw > 2)) {$errmsg = ADM_PANELS_ERR_POSX;}
					if ($pantype == 1) {$pantxt = "";} else {$panaddon = "";}
					// Request Items
					$table = "panels";
					$fields = "panelid, panel_addon, panel_title, panel_text, panel_type, posx";
					$values = "'NULL', '$panaddon', '$pantit', '$pantxt', '$pantype', '$posw'";
					$call_function = "";
					break;
	
				// ========================================================
				// Add a New User
				// ========================================================
				case "users";
					global $timezone;
	
          // GETS THE POSTED VALUES
          $usrname = $_POST['usrname'];
          $pass1 = $_POST['pass1'];
          $pass2 = $_POST['pass2'];
          $usremail = $_POST['usremail'];
          $usrurl = $_POST['usrurl'];
          $sec = $_POST['sec'];
					
          // Verify the session's code
					$ref = $_COOKIE["session_code"];
					$cref = decrypt_data($ref);
					$sec = md5($sec);
					if ($sec != $cref) {$errmsg = ADM_USERS_ERR_DENIED;}
					// Check For Bad Values...
					if (trim($usrname) == '') {$errmsg = ADM_ERR_NOEMPTYNAME;}
					if ($pass1 != $pass2) {$errmsg = ERR_ACC_PASSWASDIFF;}
					if ($pass1 == "") {$errmsg = ERR_ACC_NOEMPTYPASS;}
					$pass = encrypt_data($pass1);
					// Request Items
					$table = $acc_prefix."_users";
					$fields = "userid, user_name, user_pass, user_email, user_url";
					$values = "'NULL', '$usrname', '$pass', '$usremail', '$usrurl'";
					$call_function = "";	
					break;
			}
			$return_menu = $name;
		} else {	
			// ADD-ON COMMAND
			global $addon_prefix;
			
			if (@file_exists("add-ons/".$addon."/adm/add.php")) {
				include("add-ons/".$addon."/adm/add.php");
				$return_menu = $addon;
			} else {
				$errmsg = "ADD : ".ADM_ERR_ADDONCOMNOTEXIST;
			}
		}

		// ========================================================
		// Add a New Values in Database
		// ========================================================
		if ($table) {
			if ($errmsg == "") {
				// ATTEMPT TO ADD THE NEW ENTRY						
				$res = $db->sql_query("INSERT INTO ".$db_prefix."_".$table." (".$fields.") VALUES (".$values.")");
				if (!$res) {
					// Display Error Message...
					$page->html_header(ADM_TITLE_ADMIN);
					$page->title_page(ADM_TITLE_ADMIN, 2);
					$page->MsgBox(ERR_DB_CANTADD, TXT_OK);
					$page->html_foot();
					die();
				}
				if ($call_function) {$call_function();}
				header("Location: index.php?cmd=admin&menu=".$return_menu);
				die();
			} else {
				// Display Error Message...
				$page->html_header(ADM_TITLE_ADMIN);
				$page->title_page(ADM_TITLE_ADMIN, 2);
				$page->MsgBox($errmsg, TXT_OK);
				$page->html_foot();
				die();
			}
		}
		// ========================================================
	}

	function del($name, $id) {
		// ========================================================
		// Delete An Entries In Database
		// ========================================================
		// Entry variables :
		// -----------------
		// - $name     : Heading Name. Used by the Switch Case
		// - $id       : Entry ID in database (Primary Index)
		// ========================================================
		// Internal variables :
		// --------------------
		// - $table         : String > Table mane where the entry will be deleted.
		// - $where         : String > SQL WHERE condition. (Ex : "item_id='$id'")
		// - $call_function : String > Function name which will be called after the operation if needed.
		// ========================================================
		// The entry will be erased only if [$confirmed] is TRUE
		// If [$confirmed] is FALSE, an comfirm message will be displayed
		// ========================================================
		global $db, $db_prefix, $acc_prefix, $page, $confirmed, $addon;
		
    // GETS THE POSTED VALUES
    error_reporting(E_ALL & ~(E_NOTICE));
    $confirm = $_POST['confirm'];
    error_reporting($errr);
		
		if ($addon == "") {
			// NO ADD-ON COMMAND		
			switch(strtolower($name)) {
	
				case "groups":
					$table = $acc_prefix."_groups";
					$where = "groupid='$id'";
					$call_function = "db_update_groups_set";
					break;
	
				case "panels":
					$table = "panels";
					$where = "panelid='$id'";
					$call_function = "pannels_shift";
					// In first, we get the position, because
					// we must shift the next panels, when [panelid] will be erased. 
					if ($confirmed) {
						$res = $db->sql_query("SELECT posx, posy FROM ".$db_prefix."_panels WHERE panelid='$id' LIMIT 1");
						list($posx, $posy) = $db->sql_fetchrow($res);
					}
					break;

				case "users":
					$table = $acc_prefix."_users";
					$where = "userid='$id'";
					$call_function = "";
					break;					
			}
			$return_menu = $name;
		}  else {	
			// ADD-ON COMMAND
			global $addon_prefix;
			
			if (@file_exists("add-ons/".$addon."/adm/del.php")) {
				include("add-ons/".$addon."/adm/del.php");
				$return_menu = $addon;
			} else {
				$errmsg = "ADD : ".ADM_ERR_ADDONCOMNOTEXIST;
			}
		}
		
		// ========================================================
		// Delete the Values in Database
		// ========================================================
		if ($confirm) {
			$res = $db->sql_query("DELETE FROM ".$db_prefix."_".$table." WHERE ".$where." LIMIT 1");
			if (!$res) {
				// Display Error Message...
				$page->html_header(ADM_TITLE_ADMIN);
				$page->title_page(ADM_TITLE_ADMIN, 2);
				$page->MsgBox(ERR_DB_CANTDEL, TXT_OK);
				$page->html_foot();
				die();
			}
			if ($call_function) {$call_function();}
			header("Location: index.php?cmd=admin&menu=".$return_menu);
			die();
		} else {
			// Display Comfirm Message
			$page->html_header(ADM_TITLE_ADMIN);
			$page->title_page(ADM_TITLE_ADMIN, 2);
			$page->MsgBox(ADM_Q_REALYDEL, TXT_YES, TXT_NO, 1, "index.php?cmd=admin&addon=".$addon."&menu=".$name."&act=delete&id=".$id);
			$page->html_foot();
			die();
		}
		// ========================================================
	}

	function edit($name, $id) {
		// ========================================================
		// Edit Entries From Database
		// ========================================================
		// Entry variables :
		// -----------------
		// - $name     : Item to Edit. Used by the Switch Case
		// - $id       : Entry ID in database
		// ========================================================
		// Internal variables :
		// --------------------
		// - $hide_form     : Value  > True=displays the main form, False=hides it
		// - $main_title    : String > Form title (displayed by a '->title_content' call)
		// - $html_form     : String > HTML code, to build the form
		// - $call_function : String > Function name which will be called after the operation if needed.		
		// ========================================================
		global $db, $db_prefix, $acc_prefix, $page;
		global $tab_title_bgcolor, $tab_title_bgcolor2, $tab_bgcolor;

		$trow2 = "<tr><td bgcolor=\"".$tab_bgcolor."\" colspan=\"2\">&nbsp;</td></tr>\n";
		$trow4 = "<tr><td bgcolor=\"".$tab_bgcolor."\" colspan=\"4\">&nbsp;</td></tr>\n";
		
		$page->html_header(ADM_TITLE_ADMIN);
		$page->title_page(ADM_TITLE_ADMIN, 2);
		$this->main_menu();
		$page->html_table_start(true);

		switch(strtolower($name)) {
			case "addonrights":
				global $addon_list;

				$res = $db->sql_query("SELECT addon_name, groups_admin, groups_use, groups_view FROM ".$db_prefix."_addons WHERE addonid='$id'");
				if ($res) {
					list($addon_name, $groups_admin, $groups_use, $groups_view) = $db->sql_fetchrow($res);
					$ck_noth = ""; $ck_view = ""; $ck_use = ""; $ck_admin = "";
					$ex1 = $db->sql_numrows($db->sql_query("SELECT * FROM ".$db_prefix."_addons WHERE FIND_IN_SET('all',groups_admin)>0 AND addonid='$id'"));
					$ex2 = $db->sql_numrows($db->sql_query("SELECT * FROM ".$db_prefix."_addons WHERE FIND_IN_SET('all',groups_use)>0 AND addonid='$id'"));
					$ex3 = $db->sql_numrows($db->sql_query("SELECT * FROM ".$db_prefix."_addons WHERE FIND_IN_SET('all',groups_view)>0 AND addonid='$id'"));
					if($ex1>0) {$ck_admin = " checked";}
					elseif($ex2>0) {$ck_use = " checked";}
					elseif($ex3>0) {$ck_view = " checked";}
					else {$ck_noth = " checked";}
					$main_title = ADM_TITLE_ADDONRIGHT." : ".$addon_name;					
					$html_form = $trow2."<tr><td bgcolor=\"".$tab_bgcolor."\"><font class=\"text_big_dark\">\n".ADM_ADDONS_NAME."</font></td>\n"
					."<td bgcolor=\"".$tab_bgcolor."\"><b>\n".$addon_name."</b></td></tr>\n".$trow2
					."<tr><td colspan=\"2\" bgcolor=\"".$tab_bgcolor."\">\n<table>\n"
					."<td align=\"center\" bgcolor=\"".$tab_title_bgcolor."\"><b>\n".TXT_GROUPS."&nbsp;&nbsp;</b></td>\n"
					."<td align=\"center\" bgcolor=\"".$tab_title_bgcolor."\"><b>(".TXT_NONE.")</b></td>\n"
					."<td align=\"center\" bgcolor=\"".$tab_title_bgcolor."\"><b>\n".TXT_VIEW."</b></td>\n"
					."<td align=\"center\" bgcolor=\"".$tab_title_bgcolor."\"><b>\n".TXT_USE."</b></td>\n"
					."<td align=\"center\" bgcolor=\"".$tab_title_bgcolor."\"><b>\n".TXT_ADMIN."</b></td>\n"
					."<td align=\"center\" bgcolor=\"".$tab_title_bgcolor."\"></td></tr>\n"
					."<form action=\"index.php\" method=\"post\"><input type=\"hidden\" name=\"cmd\" value=\"admin\">\n"
					."<input type=\"hidden\" name=\"menu\" value=\"".$name."\"><input type=\"hidden\" name=\"act\" value=\"modify\">\n"
					."<input type=\"hidden\" name=\"id\" value=\"".$id."\"><input type=\"hidden\" name=\"grp_name\" value=\"all\">\n"
					."<tr><td bgcolor=\"".$tab_title_bgcolor2."\"><b>ALL</b></td>\n"
					."<td align=\"center\" bgcolor=\"".$tab_title_bgcolor2."\" width=\"60\"><input type=\"radio\" name=\"right\" value=\"0\"".$ck_noth."></td>\n"
					."<td align=\"center\" bgcolor=\"".$tab_title_bgcolor2."\" width=\"60\"><input type=\"radio\" name=\"right\" value=\"1\"".$ck_view."></td>\n"
					."<td align=\"center\" bgcolor=\"".$tab_title_bgcolor2."\" width=\"60\"><input type=\"radio\" name=\"right\" value=\"2\"".$ck_use."></td>\n"
					."<td align=\"center\" bgcolor=\"".$tab_title_bgcolor2."\" width=\"60\"></td>\n"
					."<td align=\"center\" bgcolor=\"".$tab_title_bgcolor2."\" width=\"60\"><input type=\"submit\" value=\"".TXT_SAVE."\"></td></tr></form>\n";
					
					// GET GROUPS LIST
					$bgc = $tab_bgcolor;
					$res = $db->sql_query("SELECT groupid, group_name FROM ".$db_prefix."_".$acc_prefix."_groups ORDER BY 'group_name'");
					while(list($groupid, $group_name) = $db->sql_fetchrow($res)) {
						// Get the attributes values
						$ck_noth = ""; $ck_view = ""; $ck_use = ""; $ck_admin = "";
						$ex1 = $db->sql_numrows($db->sql_query("SELECT * FROM ".$db_prefix."_addons WHERE FIND_IN_SET('$group_name',groups_admin)>0 AND addonid='$id'"));
						$ex2 = $db->sql_numrows($db->sql_query("SELECT * FROM ".$db_prefix."_addons WHERE FIND_IN_SET('$group_name',groups_use)>0 AND addonid='$id'"));						
						$ex3 = $db->sql_numrows($db->sql_query("SELECT * FROM ".$db_prefix."_addons WHERE FIND_IN_SET('$group_name',groups_view)>0 AND addonid='$id'"));						
						if($ex1>0) {$ck_admin = " checked";}
						elseif($ex2>0) {$ck_use = " checked";}
						elseif($ex3>0) {$ck_view = " checked";}
						else {$ck_noth = " checked";}
						$html_form = $html_form."<form action=\"index.php?\" method=\"post\"><input type=\"hidden\" name=\"cmd\" value=\"admin\">\n"
						."<input type=\"hidden\" name=\"menu\" value=\"".$name."\"><input type=\"hidden\" name=\"act\" value=\"modify\">\n"
						."<input type=\"hidden\" name=\"id\" value=\"".$id."\"><input type=\"hidden\" name=\"grp_name\" value=\"".$group_name."\">\n"
						."<tr><td bgcolor=\"".$bgc."\">\n".$group_name."</td>\n"
						."<td align=\"center\" bgcolor=\"".$bgc."\" width=\"60\"><input type=\"radio\" name=\"right\" value=\"0\"".$ck_noth."></td>\n"
						."<td align=\"center\" bgcolor=\"".$bgc."\" width=\"60\"><input type=\"radio\" name=\"right\" value=\"1\"".$ck_view."></td>\n"
						."<td align=\"center\" bgcolor=\"".$bgc."\" width=\"60\"><input type=\"radio\" name=\"right\" value=\"2\"".$ck_use."></td>\n"
						."<td align=\"center\" bgcolor=\"".$bgc."\" width=\"60\"><input type=\"radio\" name=\"right\" value=\"3\"".$ck_admin."></td>\n"
						."<td align=\"center\" bgcolor=\"".$bgc."\" width=\"60\"><input type=\"submit\" value=\"".TXT_SAVE."\"></td></tr></form>\n";
						if ($bgc == $tab_bgcolor) {$bgc = dechex(hexdec($tab_bgcolor) - hexdec("111111"));} else {$bgc = $tab_bgcolor;}
					}			
					
					$html_form = $html_form."\n</table>\n";
					$html_form = $html_form."</td></tr>\n";
  				$hide_form = true;
					$call_function = "";
				} else {
					$errmsg = ERR_DB_CANTLOAD;
				}
				break;

			case "grouprights":
				global $addon_list;

				$res = $db->sql_query("SELECT group_name FROM ".$db_prefix."_".$acc_prefix."_groups WHERE groupid='$id'");
				if ($res) {
					list($group_name) = $db->sql_fetchrow($res);
					$ex[0] = $db->sql_numrows($db->sql_query("SELECT * FROM ".$db_prefix."_admins WHERE FIND_IN_SET('$group_name',adm_list)>0 AND adm_function='addons'"));
					$ex[1] = $db->sql_numrows($db->sql_query("SELECT * FROM ".$db_prefix."_admins WHERE FIND_IN_SET('$group_name',adm_list)>0 AND adm_function='aspect'"));						
					$ex[2] = $db->sql_numrows($db->sql_query("SELECT * FROM ".$db_prefix."_admins WHERE FIND_IN_SET('$group_name',adm_list)>0 AND adm_function='colors'"));
          $ex[3] = $db->sql_numrows($db->sql_query("SELECT * FROM ".$db_prefix."_admins WHERE FIND_IN_SET('$group_name',adm_list)>0 AND adm_function='groups'"));
          $ex[4] = $db->sql_numrows($db->sql_query("SELECT * FROM ".$db_prefix."_admins WHERE FIND_IN_SET('$group_name',adm_list)>0 AND adm_function='index'"));
          $ex[5] = $db->sql_numrows($db->sql_query("SELECT * FROM ".$db_prefix."_admins WHERE FIND_IN_SET('$group_name',adm_list)>0 AND adm_function='panels'"));
          $ex[6] = $db->sql_numrows($db->sql_query("SELECT * FROM ".$db_prefix."_admins WHERE FIND_IN_SET('$group_name',adm_list)>0 AND adm_function='security'"));
          $ex[7] = $db->sql_numrows($db->sql_query("SELECT * FROM ".$db_prefix."_admins WHERE FIND_IN_SET('$group_name',adm_list)>0 AND adm_function='users'"));					
					for($v=0;$v<8;$v++) {
            if($ex[$v] > 0) {$xs[$v] = " checked";} else {$xs[$v] = "";}
          }
          $main_title = ADM_TITLE_GROUPRIGHT." : ".$group_name;				
					$html_form = "<tr><td align=\"center\" bgcolor=\"".$tab_bgcolor."\"><font class=\"text_big_dark\">\n".ADM_GROUPS_NAME."</font>\n"
          ."&nbsp;:&nbsp;<b>\n".$group_name."</b></td>\n"
					."<td align=\"center\" bgcolor=\"".$tab_bgcolor."\"><b>[".ADM_GROUPS_MEMBERS."]</b></td></tr>\n"
					."<tr><td align=\"center\" colspan=\"2\" bgcolor=\"".$tab_bgcolor."\"><font color=\"#990000\"><br>\n".ADM_GROUPS_CAUTION1."<br>\n"
          ."<br>\n".ADM_GROUPS_CAUTION2."</font></td></tr>\n"
					."<tr><td align=\"center\" colspan=\"2\" bgcolor=\"".$tab_bgcolor."\">\n<table>\n";

					$html_form = $html_form.$trow2."<form action=\"index.php\" method=\"post\"><input type=\"hidden\" name=\"cmd\" value=\"admin\">\n"
					."<input type=\"hidden\" name=\"menu\" value=\"".$name."\"><input type=\"hidden\" name=\"act\" value=\"modify\">\n"
					."<input type=\"hidden\" name=\"grp_name\" value=\"".$group_name."\">\n"
					."<tr><td bgcolor=\"".$tab_title_bgcolor."\">\n".TXT_MENUS."</td><td bgcolor=\"".$tab_title_bgcolor."\">\n".TXT_ADMINISTRATION."</td></tr>\n"
					."<tr><td bgcolor=\"".$tab_bgcolor."\">\n".ADM_TITLE_ADDONS."</td>\n"
          ."<td align=\"center\" bgcolor=\"".$tab_bgcolor."\"><input type=\"checkbox\" name=\"ck_addons\" value=\"1\"".$xs[0]."></td></tr>\n"          				
					."<tr><td bgcolor=\"".$tab_bgcolor."\">\n".ADM_TITLE_ASPECT."</td>\n"
					."<td align=\"center\" bgcolor=\"".$tab_bgcolor."\"><input type=\"checkbox\" name=\"ck_aspect\" value=\"1\"".$xs[1]."></td></tr>\n"
					."<tr><td bgcolor=\"".$tab_bgcolor."\">\n".ADM_TITLE_COLORS."</td>\n"
					."<td align=\"center\" bgcolor=\"".$tab_bgcolor."\"><input type=\"checkbox\" name=\"ck_colors\" value=\"1\"".$xs[2]."></td></tr>\n"
					."<tr><td bgcolor=\"".$tab_bgcolor."\">\n".ADM_TITLE_GROUPS."</td>\n"
					."<td align=\"center\" bgcolor=\"".$tab_bgcolor."\"><input type=\"checkbox\" name=\"ck_groups\" value=\"1\"".$xs[3]."></td></tr>\n"
					."<tr><td bgcolor=\"".$tab_bgcolor."\">\n".ADM_TITLE_INDEX."</td>\n"
          ."<td align=\"center\" bgcolor=\"".$tab_bgcolor."\"><input type=\"checkbox\" name=\"ck_index\" value=\"1\"".$xs[4]."></td></tr>\n"
					."<tr><td bgcolor=\"".$tab_bgcolor."\">\n".ADM_TITLE_PANELS."</td>\n"
          ."<td align=\"center\" bgcolor=\"".$tab_bgcolor."\"><input type=\"checkbox\" name=\"ck_panels\" value=\"1\"".$xs[5]."></td></tr>\n"
					."<tr><td bgcolor=\"".$tab_bgcolor."\">\n".ADM_TITLE_SECURITY."</td>\n"
          ."<td align=\"center\" bgcolor=\"".$tab_bgcolor."\"><input type=\"checkbox\" name=\"ck_security\" value=\"1\"".$xs[6]."></td></tr>\n"
					."<tr><td bgcolor=\"".$tab_bgcolor."\">\n".ADM_TITLE_USERS."</td>\n"
          ."<td align=\"center\" bgcolor=\"".$tab_bgcolor."\"><input type=\"checkbox\" name=\"ck_users\" value=\"1\"".$xs[7]."></td></tr>\n";					
					
					$html_form = $html_form."\n</table>\n</td></tr>\n";
					$hide_form = false;					
					$call_function = "";
				} else {
					$errmsg = ERR_DB_CANTLOAD;
				}
				break;

			case "groups":
				$res = $db->sql_query("SELECT groupid, group_name, group_desc FROM ".$db_prefix."_".$acc_prefix."_groups WHERE groupid='$id'");
				if ($res) {
					list($groupid, $group_name, $group_desc) = $db->sql_fetchrow($res);
					$main_title = ADM_TITLE_EDITGROUP;
					$html_form = "<tr><td bgcolor=\"".$tab_bgcolor."\"><font class=\"text_big_dark\">\n".ADM_GROUPS_NAME."</font></td>\n"
					."<td bgcolor=\"".$tab_bgcolor."\">\n"
					."<input type=\"text\" name=\"grpname\" size=\"36\" maxlength=\"32\" value=\"".$group_name."\" style=\"width: 210\"></td></tr>\n"
					."<tr><td bgcolor=\"".$tab_bgcolor."\"><font class=\"text_big_dark\">\n".ADM_GROUPS_DESC."</font></td>\n"
					."<td bgcolor=\"".$tab_bgcolor."\">\n"
					."<input type=\"text\" name=\"grpdesc\" size=\"36\" maxlength=\"128\" value=\"".$group_desc."\" style=\"width: 210\"></td></tr>\n";
					$hide_form = false;
          $call_function = "";
				} else {
					$errmsg = ERR_DB_CANTLOAD;
				}
				break;

			case "panels":
				global $addon_list_id, $addon_list_name;
	
				$res = $db->sql_query("SELECT panelid, panel_addon, panel_title, panel_text, panel_type, posx FROM ".$db_prefix."_panels WHERE panelid='$id'");
				if ($res) {
					list($panelid, $panel_addon, $panel_title, $panel_text, $panel_type, $posx) = $db->sql_fetchrow($res);

					if ($posx == 0) {$p_0 = " selected";} else {$p_0 = "";}
					if ($posx == 1) {$p_1 = " selected";} else {$p_1 = "";}
					if ($posx == 2) {$p_2 = " selected";} else {$p_2 = "";}
					if ($panel_type > 0) {$op_0 = " checked"; $op_1 = "";} else {$op_1 = " checked"; $op_0 = "";}

					$main_title = ADM_TITLE_EDITPANEL;
					$html_form = $trow2
          ."<tr>\n"
          ."  <td bgcolor=\"".$tab_bgcolor."\">\n"
          ."    <font class=\"text_big_dark\">\n".ADM_PANELS_TITLE."</font>\n"
          ."  </td>\n"
          ."  <td bgcolor=\"".$tab_bgcolor."\">\n"
          ."    <input type=\"text\" name=\"pantit\" size=\"36\" maxlength=\"64\" value=\"".$panel_title."\" style=\"width: 210\">\n"
          ."  </td>\n"
          ."</tr>\n"
          ."<tr>\n"
          ."  <td valign=\"top\" bgcolor=\"".$tab_bgcolor."\">\n"
          ."    <font class=\"text_big_dark\">\n".ADM_PANELS_BODY."</font>\n"
          ."  </td>\n"
          ."  <td bgcolor=\"".$tab_bgcolor."\">\n"
          ."    <input type=\"radio\" name=\"pantype\" value=\"1\"".$op_0.">\n".ADM_PANELS_TYPE2
          ."  </td>\n"
          ."</tr>\n"
          ."<tr>\n"
          ."  <td bgcolor=\"".$tab_bgcolor."\">  </td>\n"
          ."  <td bgcolor=\"".$tab_bgcolor."\">\n"
          ."    <select size=\"1\" name=\"panaddon\" style=\"width: 210\">\n";
					$max = count($addon_list_name);
					for ($v=0;$v<$max;$v++)
          {
						if ($panel_addon == $v) {$pp = " selected";} else {$pp = "";}
						$html_form = $html_form."<option value=\"".$addon_list_id[$v]."\"".$pp.">\n".$addon_list_name[$v]."</option>\n";
					}				
					$html_form = $html_form."</select>\n<br>\n<br>\n</td>\n</tr>\n<td bgcolor=\"".$tab_bgcolor."\">\n</td>\n<td bgcolor=\"".$tab_bgcolor."\">\n"
					."<input type=\"radio\" name=\"pantype\" value=\"0\"".$op_1.">\n".ADM_PANELS_TYPE1."</td>\n</tr>\n<tr>\n"
					."<td bgcolor=\"".$tab_bgcolor."\">\n</td>\n<td bgcolor=\"".$tab_bgcolor."\">\n";
//          <textarea rows=\"12\" cols=\"36\" name=\"pantxt\" style=\"width: 210\">\n".$panel_text."</textarea>\n</td>\n</tr>\n"
					wysiwyg('pantxt',$panel_text,0);
          echo $trow2."<tr>\n<td bgcolor=\"".$tab_bgcolor."\">\n<font class=\"text_big_dark\">\n".ADM_PANELS_POSX."</font>\n</td>\n"
					."<td bgcolor=\"".$tab_bgcolor."\">\n<select size=\"1\" name=\"posw\">\n"
					."<option value=\"0\"".$p_0.">\n(".TXT_CENTER.")</option>\n<option value=\"1\"".$p_1.">\n".TXT_LEFT."</option>\n"
					."<option value=\"2\"".$p_2.">\n".TXT_RIGHT."</option>\n"
					."</select>\n</td>\n</tr>\n";
					$hide_form = false;
					$call_function = "";
				} else {
					$errmsg = ERR_DB_CANTLOAD;
				}
				break;

			case "users":
				global $userid;		// Global for function call
				
				$res = $db->sql_query("SELECT userid, user_name, user_email, user_url, user_groups FROM ".$db_prefix."_".$acc_prefix."_users WHERE userid='$id'");
				if ($res) {
					list($userid, $user_name, $user_email, $user_url, $user_groups) = $db->sql_fetchrow($res);

					$main_title = ADM_TITLE_EDITUSER;
					$html_form = "<tr>\n<td bgcolor=\"".$tab_bgcolor."\">\n<font class=\"text_big_dark\">\n".ADM_USERS_NAME."</font>\n</td>\n"
					."<td bgcolor=\"".$tab_bgcolor."\">\n<b>\n".$user_name."</b>\n</td>\n</tr>\n"
					."<tr>\n<td bgcolor=\"".$tab_bgcolor."\">\n<font class=\"text_big_dark\">\n".ADM_USERS_EMAIL."</font>\n</td>\n"
					."<td bgcolor=\"".$tab_bgcolor."\">\n"
					."<input type=\"text\" name=\"usrname\" size=\"36\" maxlength=\"128\" value=\"".$user_email."\" style=\"width: 210\">\n</td>\n</tr>\n"
					."<tr>\n<td bgcolor=\"".$tab_bgcolor."\">\n<font class=\"text_big_dark\">\n".ADM_USERS_URL."</font>\n</td>\n"
					."<td bgcolor=\"".$tab_bgcolor."\">\n"
					."<input type=\"text\" name=\"usrurl\" size=\"36\" maxlength=\"255\" value=\"".$user_url."\" style=\"width: 210\">\n</td>\n</tr>\n"
					."<tr>\n<td colspan=\"2\" bgcolor=\"".$tab_bgcolor."\">\n";
				
					$html_form = $html_form."</td>\n</tr>\n";
					$hide_form = false;
          $call_function = "users_groups_list";				
				} else {
					$errmsg = ERR_DB_CANTLOAD;
				}
				break;
				
  		case "usersgrp":
  			global $userid;		// Global for [users_groups_list] function call
  			
  			$res = $db->sql_query("SELECT userid, user_name FROM ".$db_prefix."_".$acc_prefix."_users WHERE userid='$id'");
  			if ($res) {
  				list($userid, $user_name) = $db->sql_fetchrow($res);

  				$main_title = ADM_TITLE_EDITUSER;
  				$html_form = "<tr>\n<td>\n<br>\n</td>\n</tr>\n<tr>\n<td bgcolor=\"".$tab_bgcolor."\">\n<font class=\"text_big_dark\">\n".ADM_USERS_NAME."&nbsp;:</font>\n</td>\n"
  				."<td valign=\"center\" bgcolor=\"".$tab_bgcolor."\">\n<font class=\"title_big_dark\">\n".$user_name."</font>\n</td>\n</tr>\n";
  				$hide_form = true;
  				$call_function = "users_groups_list";				
  			} else {
  				$errmsg = ERR_DB_CANTLOAD;
  			}
  			break;				
		}

		if ($main_title) {
			$page->title_content($main_title, "kernel/pics/ic-48/config.gif", 1);
			if ($hide_form == false)
      {
			  formw();
				echo "<input type=\"hidden\" name=\"cmd\" value=\"admin\">\n"
					."<input type=\"hidden\" name=\"menu\" value=\"".$name."\">\n<input type=\"hidden\" name=\"act\" value=\"modify\">\n"
					."<input type=\"hidden\" name=\"id\" value=\"".$id."\">\n\n";
			}
			echo "<table align=\"center\" border=\"0\" cellpadding=\"2\" cellspacing=\"0\">\n";
			echo $html_form;
			echo $trow2;
			echo "<tr>\n<td align=\"center\" bgcolor=\"".$tab_bgcolor."\" colspan=\"2\">\n";
			if ($hide_form == false) {echo "<input type=\"submit\" value=\"".TXT_SAVE."\" style=\"width: 150\">\n";}
			echo "</td>\n</tr>\n<tr>\n<td align=\"center\" bgcolor=\"".$tab_bgcolor."\" colspan=\"2\">\n";
			if ($call_function) {echo "<br>\n"; $call_function();}
			echo $trow2."</td>\n</tr>\n\n</table>\n\n";
			if ($hide_form == false) {echo "</form>\n\n";}
		} elseif ($errmsg)
    {
			$page->MsgBox($errmsg, TXT_OK);			
		}

		$page->html_table_end();
	}

	function form($name) {
		// ========================================================
		// Display the Main Form
		// ========================================================
		// - $name     : Heading Name. Used by the Switch Case
		// - $id       : Entry ID in database
		// ========================================================
		global $db, $db_prefix, $page, $addon_list, $timezone, $language;
		global $site_logo, $site_bgcolor_1, $site_bgcolor_2, $site_txtcolor, $site_width, $col_left_width, $col_right_width, $contents_bgcolor;
		global $contents_width, $header_banner, $header_bglogo, $panels_bgcolor, $panels_title_bgcolor, $panels_hspacing, $panels_vspacing;
		global  $theme_style, $theme_edge, $tab_title_bgcolor, $tab_title_bgcolor2, $tab_bgcolor, $bars_001, $bars_002, $bars_003;
	
		$trow2 = "<tr>\n<td bgcolor=\"".$tab_bgcolor."\" colspan=\"2\">\n&nbsp;</td>\n</tr>\n";
		$trow4 = "<tr>\n<td bgcolor=\"".$tab_bgcolor."\" colspan=\"4\">\n&nbsp;</td>\n</tr>\n";

		// MAKE A SESSION CODE FOR USER FORM
		if (strtolower($name) == "users") {
			$code = make_code();
			$ccode = encrypt_data($code);
			setcookie("session_code", $ccode, time() + $timezone + 3600);
		}

		$page->html_header(ADM_TITLE_ADMIN);
		$page->title_page(ADM_TITLE_ADMIN, 2);
		$this->main_menu();
		$page->html_table_start(true);
	
		switch(strtolower($name)) {

		// ========================================================
		// Build an Aspect Management Form
		// ========================================================
		case "addons":
			$page->title_content(ADM_TITLE_ADDONS, "kernel/pics/ic-48/config.gif");

			echo "<br>\n\n<table align=\"center\" border=\"0\" cellpadding=\"2\" cellspacing=\"1\">\n";
			echo "<tr>\n<td bgcolor=\"".$tab_title_bgcolor2."\">\n<b>\n".TXT_ADDONS."&nbsp;</b>\n</td>\n<td bgcolor=\"".$tab_title_bgcolor2."\">\n&nbsp;</td>\n"
				."<td bgcolor=\"".$tab_title_bgcolor2."\">\n&nbsp;</td>\n<td align=\"center\" bgcolor=\"".$tab_title_bgcolor2."\">\n<b>\n".TXT_RIGHTS."</b>\n</td>\n</tr>\n";
			build_table_addons();

			echo $trow4;
			echo "\n</table>\n\n";
		break;

		// ========================================================
		// Build an Aspect Management Form
		// ========================================================
		case "aspect":
			// First bars
			if ($bars_001 == 0) {$bar1_0 = " selected";} else {$bar1_0 = "";}
			if ($bars_001 == 1) {$bar1_1 = " selected";} else {$bar1_1 = "";}
			if ($bars_001 == 2) {$bar1_2 = " selected";} else {$bar1_2 = "";}
			if ($bars_001 == 3) {$bar1_3 = " selected";} else {$bar1_3 = "";}
			if ($bars_001 > 3) {$bar1_4 = " selected";} else {$bar1_4 = "";}
			// Second bars
			if ($bars_002 == 0) {$bar2_0 = " selected";} else {$bar2_0 = "";}
			if ($bars_002 == 1) {$bar2_1 = " selected";} else {$bar2_1 = "";}
			if ($bars_002 == 2) {$bar2_2 = " selected";} else {$bar2_2 = "";}
			if ($bars_002 == 3) {$bar2_3 = " selected";} else {$bar2_3 = "";}
			if ($bars_002 > 3) {$bar2_4 = " selected";} else {$bar2_4 = "";}
			// Third bars
			if ($bars_003 == 0) {$bar3_0 = " selected";} else {$bar3_0 = "";}
			if ($bars_003 == 1) {$bar3_1 = " selected";} else {$bar3_1 = "";}
			if ($bars_003 == 2) {$bar3_2 = " selected";} else {$bar3_2 = "";}
			if ($bars_003 == 3) {$bar3_3 = " selected";} else {$bar3_3 = "";}
			if ($bars_003 > 3) {$bar3_4 = " selected";} else {$bar3_4 = "";}

			$page->title_content(ADM_TITLE_ASPECT, "kernel/pics/ic-48/config.gif");
				
			echo "<br>\n";
      formw();
      echo "<input type=\"hidden\" name=\"cmd\" value=\"admin\">\n"
				."<input type=\"hidden\" name=\"menu\" value=\"aspect\">\n<input type=\"hidden\" name=\"act\" value=\"modify\">\n";
			echo "<table align=\"center\" border=\"0\" cellpadding=\"2\" cellspacing=\"0\">\n";
			echo "<tr>\n<td bgcolor=\"".$tab_title_bgcolor2."\" colspan=\"2\">\n<font class=\"title_dark\">\n".TXT_GENERAL."</font>\n</td>\n</tr>\n";
			echo $trow2;

			echo "<tr>\n<td bgcolor=\"".$tab_bgcolor."\">\n<font class=\"text_big_dark\">\n".ADM_ASPECT_THSTYLE."</font>\n</td>\n"
				."<td bgcolor=\"".$tab_bgcolor."\">\n<select size=\"1\" name=\"th_style\" style=\"width: 180\">\n";
			$hd = opendir("themes/texts");
			while ($file = readdir($hd)) {
				if ($file != ".") {
					if ($file != "..") {
						if (strtolower($theme_style) == strtolower($file)) {$strt = " selected";} else {$strt = "";}
						if ($file != "index.html") {echo "<option".$strt.">\n".$file."</option>\n";}
					}
				}
			}
			closedir($hd);
			echo "</select>\n</td>\n</tr>\n";
			echo "<tr>\n<td bgcolor=\"".$tab_bgcolor."\">\n<font class=\"text_big_dark\">\n".ADM_ASPECT_WIDTH."</font>\n</td>\n"
				."<td bgcolor=\"".$tab_bgcolor."\">\n<input type=\"text\" name=\"swidth\" size=\"3\" maxlength=\"3\" value=\"".$site_width."\">\n&nbsp;%</td>\n</tr>\n";
			echo "<tr>\n<td bgcolor=\"".$tab_bgcolor."\">\n<font class=\"text_big_dark\">\n".ADM_ASPECT_CWIDTH."</font>\n</td>\n"
				."<td bgcolor=\"".$tab_bgcolor."\">\n<input type=\"text\" name=\"cwidth\" size=\"3\" maxlength=\"3\" value=\"".$contents_width."\">\n&nbsp;%</td>\n</tr>\n";
			echo "<tr>\n<td bgcolor=\"".$tab_bgcolor."\">\n<font class=\"text_big_dark\">\n".ADM_ASPECT_COLUMN3."</font>\n</td>\n<td bgcolor=\"".$tab_bgcolor."\">\n"
				."<input type=\"text\" name=\"hcspace\" size=\"3\" maxlength=\"3\" value=\"".$panels_hspacing."\">\n&nbsp;".TXT_PIXELS."</td>\n</tr>\n";
			echo "<tr>\n<td bgcolor=\"".$tab_bgcolor."\">\n<font class=\"text_big_dark\">\n".ADM_ASPECT_VSPACE."</font>\n</td>\n<td bgcolor=\"".$tab_bgcolor."\">\n"
				."<input type=\"text\" name=\"vspace\" size=\"3\" maxlength=\"3\" value=\"".$panels_vspacing."\">\n&nbsp;".TXT_PIXELS."</td>\n</tr>\n";
			echo $trow2;

			echo "<tr>\n<td bgcolor=\"".$tab_title_bgcolor2."\" colspan=\"2\">\n<font class=\"title_dark\">\n".TXT_HEADER."</font>\n</td>\n</tr>\n";
			echo $trow2;
/*
			echo "<tr>\n<td bgcolor=\"".$tab_bgcolor."\">\n<font class=\"text_big_dark\">\n".ADM_ASPECT_LOGO."</font>\n</td>\n<td bgcolor=\"".$tab_bgcolor."\">\n"
				."<input type=\"text\" name=\"logo\" size=\"36\" style=\"width: 180\" maxlength=\"255\" value=\"".$site_logo."\">\n</td>\n</tr>\n";
			echo "<tr>\n<td bgcolor=\"".$tab_bgcolor."\">\n<font class=\"text_big_dark\">\n".ADM_ASPECT_BGLOGO."</font>\n</td>\n<td bgcolor=\"".$tab_bgcolor."\">\n"
				."<input type=\"text\" name=\"bglogo\" size=\"36\" style=\"width: 180\" maxlength=\"255\" value=\"".$header_bglogo."\">\n</td>\n</tr>\n";
			echo "<tr>\n<td bgcolor=\"".$tab_bgcolor."\">\n<font class=\"text_big_dark\">\n".ADM_ASPECT_RIGHTBANNER."</font>\n</td>\n<td bgcolor=\"".$tab_bgcolor."\">\n"
				."<input type=\"text\" name=\"banner\" size=\"36\" style=\"width: 180\" maxlength=\"255\" value=\"".$header_banner."\">\n</td>\n</tr>\n";

			echo "<tr>\n<td bgcolor=\"".$tab_bgcolor."\">\n<font class=\"text_big_dark\">\n".ADM_ASPECT_BANNHEIGHT."</font>\n</td>\n<td bgcolor=\"".$tab_bgcolor."\">\n"
				."<input type=\"text\" name=\"hheight\" size=\"3\" maxlength=\"3\" value=\"".$head_height."\">\n&nbsp;".TXT_PIXELS."</td>\n</tr>\n";
*/				
			echo "<tr>\n<td bgcolor=\"".$tab_bgcolor."\">\n<font class=\"text_big_dark\">\n".ADM_ASPECT_BUP."</font>\n</td>\n"
				."<td bgcolor=\"".$tab_bgcolor."\">\n<select size=\"1\" name=\"bar1\" style=\"width: 180\">\n"
				."<option value=\"0\"".$bar1_0.">\n(".TXT_NOTHING.")</option>\n<option value=\"1\"".$bar1_1.">\n".TXT_BARS_SUPPORT."</option>\n"
				."<option value=\"2\"".$bar1_2.">\n".TXT_BARS_INDEX."</option>\n<option value=\"3\"".$bar1_3.">\n".TXT_BARS_SEARCH."</option>\n"
				."<option value=\"4\"".$bar1_4.">\n".TXT_BARS_USER."</option>\n</select>\n</td>\n</tr>\n";
			echo "<tr>\n<td bgcolor=\"".$tab_bgcolor."\">\n<font class=\"text_big_dark\">\n".ADM_ASPECT_BDOWN."</font>\n</td>\n"
				."<td bgcolor=\"".$tab_bgcolor."\">\n<select size=\"1\" name=\"bar2\" style=\"width: 180\">\n"
				."<option value=\"0\"".$bar2_0.">\n(".TXT_NOTHING.")</option>\n<option value=\"1\"".$bar2_1.">\n".TXT_BARS_SUPPORT."</option>\n"
				."<option value=\"2\"".$bar2_2.">\n".TXT_BARS_INDEX."</option>\n<option value=\"3\"".$bar2_3.">\n".TXT_BARS_SEARCH."</option>\n"
				."<option value=\"4\"".$bar2_4.">\n".TXT_BARS_USER."</option>\n</select>\n</td>\n</tr>\n";
			echo "<tr>\n<td bgcolor=\"".$tab_bgcolor."\">\n<font class=\"text_big_dark\">\n".ADM_ASPECT_BMOREDOWN."</font>\n</td>\n"
				."<td bgcolor=\"".$tab_bgcolor."\">\n<select size=\"1\" name=\"bar3\" style=\"width: 180\">\n"
				."<option value=\"0\"".$bar3_0.">\n(".TXT_NOTHING.")</option>\n<option value=\"1\"".$bar3_1.">\n".TXT_BARS_SUPPORT."</option>\n"
				."<option value=\"2\"".$bar3_2.">\n".TXT_BARS_INDEX."</option>\n<option value=\"3\"".$bar3_3.">\n".TXT_BARS_SEARCH."</option>\n"
				."<option value=\"4\"".$bar3_4.">\n".TXT_BARS_USER."</option>\n</select>\n</td>\n</tr>\n";
			echo $trow2;
			
			echo "<tr>\n<td bgcolor=\"".$tab_title_bgcolor2."\" colspan=\"2\">\n<font class=\"title_dark\">\n".TXT_COLUMNS."</font>\n</td>\n</tr>\n";
			echo $trow2;
			echo "<tr>\n<td bgcolor=\"".$tab_bgcolor."\">\n<font class=\"text_big_dark\">\n".ADM_ASPECT_COLUMN1."</font>\n</td>\n<td bgcolor=\"".$tab_bgcolor."\">\n"
				."<input type=\"text\" name=\"lsize\" size=\"3\" maxlength=\"3\" value=\"".$col_left_width."\">\n&nbsp;".TXT_PIXELS."</td>\n</tr>\n";
			echo "<tr>\n<td bgcolor=\"".$tab_bgcolor."\">\n<font class=\"text_big_dark\">\n".ADM_ASPECT_COLUMN2."</font>\n</td>\n<td bgcolor=\"".$tab_bgcolor."\">\n"
				."<input type=\"text\" name=\"rsize\" size=\"3\" maxlength=\"3\" value=\"".$col_right_width."\">\n&nbsp;".TXT_PIXELS."</td>\n</tr>\n";
			echo "<tr>\n<td bgcolor=\"".$tab_bgcolor."\">\n</td>\n<td bgcolor=\"".$tab_bgcolor."\">\n(".ADM_ASPECT_COLUMNCOM.")</td>\n</tr>\n";
			echo $trow2;

			echo "<tr>\n<td bgcolor=\"".$tab_title_bgcolor2."\" colspan=\"2\">\n<font class=\"title_dark\">\n".TXT_PANELS."</font>\n</td>\n</tr>\n";
			echo $trow2;
			echo "<tr>\n<td bgcolor=\"".$tab_bgcolor."\">\n<font class=\"text_big_dark\">\n".ADM_ASPECT_THGFXPANELS."&nbsp;</font>\n</td>\n"
				."<td bgcolor=\"".$tab_bgcolor."\">\n<select size=\"1\" name=\"th_blocks\" style=\"width: 180\">\n";
			$hd = opendir("themes/borders");
			while ($file = readdir($hd)) {
				if ($file != ".") {
					if ($file != "..") {
						if (strtolower($theme_edge) == strtolower($file)) {$strt = " selected";} else {$strt = "";}
						if ($file != "index.html") {echo "<option".$strt.">\n".$file."</option>\n";}
					}
				}
			}
			closedir($hd);
			echo "</select>\n</td>\n</tr>\n";
			echo $trow2;

			echo "<tr>\n<td bgcolor=\"".$tab_title_bgcolor2."\" colspan=\"2\">\n<font class=\"text_big_dark\">\n&nbsp;</font>\n</td>\n";
			echo $trow2;
			echo "<tr>\n<td align=\"center\" bgcolor=\"".$tab_bgcolor."\" colspan=\"2\">\n<font class=\"text_big_dark\">\n"
				."<input type=\"submit\" value=\"".TXT_SAVE."\" style=\"width: 150\">\n</font>\n</td>\n";
			echo "</table>\n</form>\n";
		break;

		// ========================================================
		// Build a Colors Management Form
		// ========================================================
		case "colors":
			$page->title_content(ADM_TITLE_COLORS, "kernel/pics/ic-48/config.gif");

			echo "<br>\n<center>\n".ADM_COLORS_CAUTION1."</center>\n<br>\n\n<form action=\"index.php\" method=\"post\">\n<input type=\"hidden\" name=\"cmd\" value=\"admin\">\n"
				."<input type=\"hidden\" name=\"menu\" value=\"colors\">\n<input type=\"hidden\" name=\"act\" value=\"modify\">\n\n";
			echo "<table align=\"center\" border=\"0\" cellpadding=\"2\" cellspacing=\"0\">\n";
			echo "<tr>\n<td bgcolor=\"".$tab_title_bgcolor2."\" colspan=\"2\">\n<font class=\"title_dark\">\n".TXT_GENERAL."</font>\n</td>\n</tr>\n";
			echo "<tr>\n<td bgcolor=\"".$tab_bgcolor."\">\n<font class=\"text_big_dark\">\n".ADM_COLORS_BGCOLSITE."</font>\n</td>\n"
				."<td bgcolor=\"".$tab_bgcolor."\">\n<input type=\"text\" name=\"bgcolsite\" size=\"7\" maxlength=\"6\" value=\"".$site_bgcolor_1."\">\n</td>\n</tr>\n";
			echo "<tr>\n<td bgcolor=\"".$tab_bgcolor."\">\n<font class=\"text_big_dark\">\n".ADM_COLORS_BGCOLSECOND."</font>\n</td>\n"
				."<td bgcolor=\"".$tab_bgcolor."\">\n<input type=\"text\" name=\"bgcolsupp\" size=\"7\" maxlength=\"6\" value=\"".$site_bgcolor_2."\">\n</td>\n</tr>\n";
			echo "<tr>\n<td bgcolor=\"".$tab_bgcolor."\">\n<font class=\"text_big_dark\">\n".TXT_TEXT."</font>\n</td>\n"
				."<td bgcolor=\"".$tab_bgcolor."\">\n<input type=\"text\" name=\"txtcolsite\" size=\"7\" maxlength=\"6\" value=\"".$site_txtcolor."\">\n</td>\n</tr>\n";				
			
			echo "<tr>\n<td bgcolor=\"".$tab_title_bgcolor2."\" colspan=\"2\">\n<font class=\"title_dark\">\n".TXT_PANELS."</font>\n</td>\n</tr>\n";
			echo "<tr>\n<td bgcolor=\"".$tab_bgcolor."\">\n<font class=\"text_big_dark\">\n".ADM_COLORS_BGCOLTITLEPANEL."</font>\n</td>\n"
				."<td bgcolor=\"".$tab_bgcolor."\">\n<input type=\"text\" name=\"bgcoltitpanel\" size=\"7\" maxlength=\"6\" value=\"".$panels_title_bgcolor."\">\n</td>\n</tr>\n";
			echo "<tr>\n<td bgcolor=\"".$tab_bgcolor."\">\n<font class=\"text_big_dark\">\n".ADM_COLORS_BGCOLPANEL."</font>\n</td>\n"
				."<td bgcolor=\"".$tab_bgcolor."\">\n<input type=\"text\" name=\"bgcolpanel\" size=\"7\" maxlength=\"6\" value=\"".$panels_bgcolor."\">\n</td>\n</tr>\n";
			
			echo "<tr>\n<td bgcolor=\"".$tab_title_bgcolor2."\" colspan=\"2\">\n<font class=\"title_dark\">\n".TXT_CONTENTS."</font>\n</td>\n</tr>\n";
			echo "<tr>\n<td bgcolor=\"".$tab_bgcolor."\">\n<font class=\"text_big_dark\">\n".ADM_COLORS_BGCOLCONTENTS."</font>\n</td>\n"
				."<td bgcolor=\"".$tab_bgcolor."\">\n<input type=\"text\" name=\"bgcolcontents\" size=\"7\" maxlength=\"6\" value=\"".$contents_bgcolor."\">\n</td>\n</tr>\n";
			echo "<tr>\n<td bgcolor=\"".$tab_bgcolor."\">\n<font class=\"text_big_dark\">\n".ADM_COLORS_BGCOLTITLETABLE."</font>\n</td>\n"
				."<td bgcolor=\"".$tab_bgcolor."\">\n<input type=\"text\" name=\"bgcoltittab\" size=\"7\" maxlength=\"6\" value=\"".$tab_title_bgcolor."\">\n</td>\n</tr>\n";
			echo "<tr>\n<td bgcolor=\"".$tab_bgcolor."\">\n<font class=\"text_big_dark\">\n".ADM_COLORS_BGCOLTITLETABLE2."</font>\n</td>\n"
				."<td bgcolor=\"".$tab_bgcolor."\">\n<input type=\"text\" name=\"bgcoltab2\" size=\"7\" maxlength=\"6\" value=\"".$tab_title_bgcolor2."\">\n</td>\n</tr>\n";
			echo "<tr>\n<td bgcolor=\"".$tab_bgcolor."\">\n<font class=\"text_big_dark\">\n".ADM_COLORS_BGCOLTABLE1."</font>\n</td>\n"
				."<td bgcolor=\"".$tab_bgcolor."\">\n<input type=\"text\" name=\"bgcoltab\" size=\"7\" maxlength=\"6\" value=\"".$tab_bgcolor."\">\n</td>\n</tr>\n";
			echo "<tr>\n<td bgcolor=\"".$tab_title_bgcolor2."\" colspan=\"2\">\n<font class=\"text_big_dark\">\n&nbsp;</font>\n</td>\n";
			echo $trow2;
			echo "<tr>\n<td align=\"center\" bgcolor=\"".$tab_bgcolor."\" colspan=\"2\">\n<font class=\"text_big_dark\">\n"
				."<input type=\"submit\" value=\"".TXT_SAVE."\" style=\"width: 150\">\n</font>\n</td>\n";
			echo "</table>\n</form>\n";
		break;

		// ========================================================
		// Build a Groups Management Form
		// ========================================================
		case "groups":
			$page->title_content(ADM_TITLE_GROUPS, "kernel/pics/ic-48/config.gif");

			echo "<br>\n\n<table align=\"center\" border=\"0\" cellpadding=\"2\" cellspacing=\"1\">\n";
			echo "<tr>\n<td bgcolor=\"".$tab_title_bgcolor."\">\n<b>\n".TXT_GROUPS."&nbsp;</b>\n</td>\n<td bgcolor=\"".$tab_title_bgcolor."\">\n<b>\n".TXT_DESCRIPTION."</b>\n</td>\n"
				."<td bgcolor=\"".$tab_title_bgcolor."\">\n&nbsp;</td>\n<td bgcolor=\"".$tab_title_bgcolor."\">\n&nbsp;</td>\n</tr>\n";
			build_table_groups();

			echo $trow4;
			echo "\n</table>\n\n";
			$page->html_table_end();

			$page->html_table_start(true);
			$page->title_content(ADM_TITLE_ADDGROUP, "kernel/pics/ic-48/config.gif");

			// ADD FORM
			echo "<br>\n\n";
      formw();
      echo "<input type=\"hidden\" name=\"cmd\" value=\"admin\">\n"
				  ."<input type=\"hidden\" name=\"menu\" value=\"groups\">\n<input type=\"hidden\" name=\"act\" value=\"add\">\n\n";
			echo "<table align=\"center\" border=\"0\" cellpadding=\"2\" cellspacing=\"0\">\n";
			echo "<tr>\n<td bgcolor=\"".$tab_bgcolor."\">\n<font class=\"text_big_dark\">\n".ADM_GROUPS_NAME."</font>\n</td>\n"
				  ."<td bgcolor=\"".$tab_bgcolor."\">\n"
				  ."<input type=\"text\" name=\"grpname\" size=\"36\" maxlength=\"32\" style=\"width: 210\">\n</td>\n</tr>\n";
			echo "<tr>\n<td bgcolor=\"".$tab_bgcolor."\">\n<font class=\"text_big_dark\">\n".ADM_GROUPS_DESC."</font>\n</td>\n"
				  ."<td bgcolor=\"".$tab_bgcolor."\">\n"
				  ."<input type=\"text\" name=\"grpdesc\" size=\"36\" maxlength=\"128\" style=\"width: 210\">\n</td>\n</tr>\n";
			echo $trow4;
			echo "<tr>\n<td align=\"center\" bgcolor=\"".$tab_bgcolor."\" colspan=\"2\">\n<font class=\"text_big_dark\">\n"
				  ."<input type=\"submit\" value=\"".TXT_ADD."\" style=\"width: 150\">\n</font>\n</td>\n</tr>\n";
			echo $trow4;
			echo "\n</table>\n\n</form>\n";			
		break;

		case "index":
			$page->title_content(ADM_TITLE_INDEX, "kernel/pics/ic-48/config.gif");


		break;

		case "panels":
			global $addon_list_id, $addon_list_name;
			
			$page->title_content(ADM_TITLE_PANELS, "kernel/pics/ic-48/config.gif");

			echo "<br>\n\n<table align=\"center\" border=\"0\" cellpadding=\"2\" cellspacing=\"1\">\n";

			// ========================================================
			// LEFT PANELS
			// ========================================================
			echo "<tr>\n<td align=\"center\" bgcolor=\"".$tab_bgcolor."\" colspan=\"6\">\n<font class=\"title_big_dark\">\n".ADM_PANELS_LPANELS."</font>\n</td>\n</tr>\n";
			echo "<tr>\n<td bgcolor=\"".$tab_title_bgcolor."\">\n<b>\n".TXT_ID."</b>\n</td>\n"
				."<td bgcolor=\"".$tab_title_bgcolor."\">\n<b>\n".ADM_PANELS_TITLE."&nbsp;</b>\n</td>\n"
				."<td align=\"center\" bgcolor=\"".$tab_title_bgcolor."\">\n<b>\n".TXT_ADDONS."</b>\n</td>\n"
				."<td bgcolor=\"".$tab_title_bgcolor."\">\n<b>\n".TXT_WEIGHT."</b>\n</td>\n"
				."<td bgcolor=\"".$tab_title_bgcolor."\">\n&nbsp;</td>\n<td bgcolor=\"".$tab_title_bgcolor."\">\n&nbsp;</td>\n</tr>\n";
			build_table_panels(1);
			// ========================================================

			// ========================================================
			// CENTERED PANELS
			// ========================================================
			echo $trow4;
			echo "<tr>\n<td align=\"center\" bgcolor=\"".$tab_bgcolor."\" colspan=\"6\">\n<font class=\"title_big_dark\">\n".ADM_PANELS_CPANELS."</font>\n</td>\n</tr>\n";
			echo "<tr>\n<td bgcolor=\"".$tab_title_bgcolor."\">\n<b>\n".TXT_ID."</b>\n</td>\n"
				."<td bgcolor=\"".$tab_title_bgcolor."\">\n<b>\n".ADM_PANELS_TITLE."&nbsp;</b>\n</td>\n"
				."<td align=\"center\" bgcolor=\"".$tab_title_bgcolor."\">\n<b>\n".TXT_ADDONS."</b>\n</td>\n"
				."<td bgcolor=\"".$tab_title_bgcolor."\">\n<b>\n".TXT_WEIGHT."</b>\n</td>\n"
				."<td bgcolor=\"".$tab_title_bgcolor."\">\n&nbsp;</td>\n<td bgcolor=\"".$tab_title_bgcolor."\">\n&nbsp;</td>\n</tr>\n";
			build_table_panels(0);
			// ========================================================

			// ========================================================
			// RIGHT PANELS
			// ========================================================
			echo $trow4;
			echo "<tr>\n<td align=\"center\" bgcolor=\"".$tab_bgcolor."\" colspan=\"6\">\n<font class=\"title_big_dark\">\n".ADM_PANELS_RPANELS."</font>\n</td>\n</tr>\n";
			echo "<tr>\n<td bgcolor=\"".$tab_title_bgcolor."\">\n<b>\n".TXT_ID."</b>\n</td>\n"
				."<td bgcolor=\"".$tab_title_bgcolor."\">\n<b>\n".ADM_PANELS_TITLE."&nbsp;</b>\n</td>\n"
				."<td align=\"center\" bgcolor=\"".$tab_title_bgcolor."\">\n<b>\n".TXT_ADDONS."</b>\n</td>\n"
				."<td bgcolor=\"".$tab_title_bgcolor."\">\n<b>\n".TXT_WEIGHT."</b>\n</td>\n"
				."<td bgcolor=\"".$tab_title_bgcolor."\">\n&nbsp;</td>\n<td bgcolor=\"".$tab_title_bgcolor."\">\n&nbsp;</td>\n</tr>\n";
			build_table_panels(2);
			// ========================================================

			echo $trow4;
			echo "\n</table>\n\n";
			$page->html_table_end();

			$page->html_table_start(true);
			$page->title_content(ADM_TITLE_ADDPANEL, "kernel/pics/ic-48/config.gif");

			// ADD FORM
			echo "<br>\n\n";
      formw();
      echo "<input type=\"hidden\" name=\"cmd\" value=\"admin\">\n"
				  ."<input type=\"hidden\" name=\"menu\" value=\"panels\">\n<input type=\"hidden\" name=\"act\" value=\"add\">\n\n";
			echo "<table align=\"center\" border=\"0\" cellpadding=\"2\" cellspacing=\"0\">\n";
			echo "<tr>\n<td bgcolor=\"".$tab_bgcolor."\">\n<font class=\"text_big_dark\">\n".ADM_PANELS_TITLE."</font>\n<br>\n<br>\n</td>\n"
				  ."<td bgcolor=\"".$tab_bgcolor."\">\n"
				  ."<input type=\"text\" name=\"pantit\" size=\"36\" maxlength=\"64\" style=\"width: 210\">\n</td>\n</tr>\n";				
			echo "<tr>\n<td valign=\"top\" bgcolor=\"".$tab_bgcolor."\">\n<font class=\"text_big_dark\">\n".ADM_PANELS_BODY."</font>\n</td>\n";
			echo "<td bgcolor=\"".$tab_bgcolor."\">\n<input type=\"radio\" name=\"pantype\" value=\"1\">\n".ADM_PANELS_TYPE2."</td>\n</tr>\n<tr>\n"
				  ."<td bgcolor=\"".$tab_bgcolor."\">\n</td>\n<td bgcolor=\"".$tab_bgcolor."\">\n<select size=\"1\" name=\"panaddon\" style=\"width: 210\">\n";
			$max = count($addon_list_name);
			for ($v=0;$v<$max;$v++) {
				echo "<option value=\"".$addon_list_id[$v]."\">\n".$addon_list_name[$v]."</option>\n";
			}				
			echo "</select>\n<br>\n<br>\n</td>\n</tr>\n<td bgcolor=\"".$tab_bgcolor."\">\n</td>\n<td bgcolor=\"".$tab_bgcolor."\">\n"
				  ."<input type=\"radio\" name=\"pantype\" value=\"0\" checked>\n".ADM_PANELS_TYPE1."</td>\n</tr>\n<tr>\n"			
				  ."<td bgcolor=\"".$tab_bgcolor."\">\n</td>\n<td bgcolor=\"".$tab_bgcolor."\">\n";
//				  ."<textarea rows=\"12\" cols=\"36\" name=\"pantxt\" style=\"width: 210\">\n</textarea>\n</td>\n</tr>\n<tr>\n";
      wysiwyg('pantxt','',0);
			echo $trow2;
			echo "<tr>\n<td bgcolor=\"".$tab_bgcolor."\">\n<font class=\"text_big_dark\">\n".ADM_PANELS_POSX."</font>\n</td>\n"
				  ."<td bgcolor=\"".$tab_bgcolor."\">\n<select size=\"1\" name=\"posw\">\n"
				  ."<option value=\"0\">\n(".TXT_CENTER.")</option>\n<option value=\"1\">\n".TXT_LEFT."</option>\n"
				  ."<option value=\"2\">\n".TXT_RIGHT."</option>\n"
				  ."</select>\n</td>\n</tr>\n";
			echo $trow2;
			echo "<tr>\n<td align=\"center\" bgcolor=\"".$tab_bgcolor."\" colspan=\"2\">\n"
				  ."<input type=\"submit\" value=\"".TXT_ADD."\" style=\"width: 150\">\n</td>\n</tr>\n";
			echo $trow2;
			echo "\n</table>\n\n</form>\n";
		break;

		// ========================================================
		// Build a Security Management Form
		// ========================================================
		case "security";
			global $secur_code;

			// Use Security Code
			if ($secur_code == 0) {$s1_1 = " checked"; $s1_2 = "";} else {$s1_2 = " checked"; $s1_1 = "";}

			$page->title_content(ADM_TITLE_SECURITY, "kernel/pics/ic-48/config.gif");
			echo "<br>\n\n<form action=\"index.php\" method=\"post\">\n<input type=\"hidden\" name=\"cmd\" value=\"admin\">\n"
				."<input type=\"hidden\" name=\"menu\" value=\"security\">\n<input type=\"hidden\" name=\"act\" value=\"modify\">\n\n";
			echo "<table align=\"center\" border=\"0\" cellpadding=\"2\" cellspacing=\"0\">\n";
			echo "<tr>\n<td bgcolor=\"".$tab_bgcolor."\">\n<font class=\"text_big_dark\">\n".ADM_SECUR_USECODE."&nbsp;&nbsp;</font>\n</td>\n"
				."<td bgcolor=\"".$tab_bgcolor."\">\n"
				."<input type=\"radio\" name=\"sec_code\" value=\"1\"".$s1_2.">\n".TXT_YES."&nbsp;&nbsp;"
				."<input type=\"radio\" name=\"sec_code\" value=\"0\"".$s1_1.">\n".TXT_NO."</td>\n</tr>\n";
			echo "<tr>\n<td bgcolor=\"".$tab_bgcolor."\">\n<font class=\"text_big_dark\">\n...</font>\n</td>\n"
				."<td bgcolor=\"".$tab_bgcolor."\">\n...</td>\n</tr>\n";
			echo $trow4;
			echo "<tr>\n<td align=\"center\" bgcolor=\"".$tab_bgcolor."\" colspan=\"2\">\n"
				."<input type=\"submit\" value=\"".TXT_SAVE."\" style=\"width: 150\">\n</td>\n</tr>\n";
			echo $trow4;
			echo "\n</table>\n\n</form>\n";				
		break;

		case "users":
			$page->title_content(ADM_TITLE_USERS, "kernel/pics/ic-48/config.gif");

			echo "<br>\n\n<table align=\"center\" border=\"0\" cellpadding=\"2\" cellspacing=\"1\">\n";

			echo "<tr>\n<td bgcolor=\"".$tab_title_bgcolor."\">\n<b>\n".TXT_USERS."&nbsp;</b>\n</td>\n<td bgcolor=\"".$tab_title_bgcolor."\">\n<b>\n".TXT_LOG_LAST."</b>\n</td>\n"
				."<td bgcolor=\"".$tab_title_bgcolor."\">\n</td>\n<td bgcolor=\"".$tab_title_bgcolor."\">\n&nbsp;</td>\n</tr>\n";
			build_table_users();

			echo $trow4;
			echo "\n</table>\n\n";
			$page->html_table_end();

			$page->html_table_start(true);
			$page->title_content(ADM_TITLE_ADDUSER, "kernel/pics/ic-48/config.gif");

			// ADD FORM
			echo "<br>\n\n";
      formw();
      echo "<input type=\"hidden\" name=\"cmd\" value=\"admin\">\n"
				  ."<input type=\"hidden\" name=\"menu\" value=\"users\">\n<input type=\"hidden\" name=\"act\" value=\"add\">\n"
				  ."<input type=\"hidden\" name=\"sec\" value=\"".$code."\">\n\n";
			echo "<table align=\"center\" border=\"0\" cellpadding=\"2\" cellspacing=\"0\">\n";
			echo "<tr>\n<td bgcolor=\"".$tab_bgcolor."\">\n<font class=\"text_big_dark\">\n".ADM_USERS_NAME."</font>\n</td>\n"
				  ."<td bgcolor=\"".$tab_bgcolor."\">\n"
				  ."<input type=\"text\" name=\"usrname\" size=\"36\" maxlength=\"24\" style=\"width: 210\">\n</td>\n</tr>\n";
			echo "<tr>\n<td bgcolor=\"".$tab_bgcolor."\">\n<font class=\"text_big_dark\">\n".ADM_USERS_PASS."</font>\n</td>\n"
				  ."<td bgcolor=\"".$tab_bgcolor."\">\n"
				  ."<input type=\"password\" name=\"pass1\" size=\"16\" maxlength=\"12\">\n</td>\n</tr>\n";
			echo "<tr>\n<td bgcolor=\"".$tab_bgcolor."\">\n<font class=\"text_big_dark\">\n".TXT_LOG_CONFIRMPASS."</font>\n</td>\n"
			 	  ."<td bgcolor=\"".$tab_bgcolor."\">\n"
				  ."<input type=\"password\" name=\"pass2\" size=\"16\" maxlength=\"12\">\n</td>\n</tr>\n";
			echo "<tr>\n<td bgcolor=\"".$tab_bgcolor."\">\n<font class=\"text_big_dark\">\n".ADM_USERS_EMAIL."</font>\n</td>\n"
				  ."<td bgcolor=\"".$tab_bgcolor."\">\n"
				  ."<input type=\"text\" name=\"usremail\" size=\"36\" maxlength=\"128\" style=\"width: 210\">\n</td>\n</tr>\n";
			echo "<tr>\n<td bgcolor=\"".$tab_bgcolor."\">\n<font class=\"text_big_dark\">\n".ADM_USERS_URL."</font>\n</td>\n"
				  ."<td bgcolor=\"".$tab_bgcolor."\">\n"
				  ."<input type=\"text\" name=\"usrurl\" size=\"36\" maxlength=\"255\" style=\"width: 210\">\n</td>\n</tr>\n";
			
			echo $trow4;
			echo "<tr>\n<td align=\"center\" bgcolor=\"".$tab_bgcolor."\" colspan=\"2\">\n<font class=\"text_big_dark\">\n"
				  ."<input type=\"submit\" value=\"".TXT_ADD."\" style=\"width: 150\">\n</font>\n</td>\n</tr>\n";
			echo $trow4;
			echo "\n</table>\n\n</form>\n";
		
		break;
		}

		// ========================================================
		// Scan ADD-ONS list to find the others [form] case
		// ========================================================
		$cmpt = count($addon_list);
		for ($v=0; $v<$cmpt+1; $v++) {
//			if (addon_right($addon_list_id[$v] >\n 2) {
				if (@file_exists("add-ons/".$addon_list[$v]."/locale/adm.".$language.".php")) {include("add-ons/".$addon_list[$v]."/locale/adm.".$language.".php");}
				if (@file_exists("add-ons/".$addon_list[$v]."/adm/form.php")) {include("add-ons/".$addon_list[$v]."/adm/form.php");}
//			}
		}
		// ========================================================

		$page->html_table_end();
	}

	function modify($name) {
		// ========================================================
		// Change Values In Database
		// ========================================================
		// - $name          : Heading Name. Used by the Switch Case
		// ========================================================
		// Internal variables :
		// --------------------
		// - $table         : Table mane to update.
		// - $fields        : Fields list with its values.
		// - $where         : SQL where condition, if needed
		// - $return_lnk    : Link to be followed after the update.
		// - $errmsg        : If this variable is a string, the request will not executed and the string will dispayed.
		// ========================================================		
		global $db, $db_prefix, $acc_prefix, $page;
		
		$errmsg = "";
		
		switch(strtolower($name)) {

			case "addonrights":
        global $id;
        
        // GETS THE POSTED VALUES
        $grp_name = strtolower($_POST['grp_name']);
        $right = $_POST['right'];

				// Get current affected groups
				$res = $db->sql_query("SELECT groups_admin, groups_use, groups_view FROM ".$db_prefix."_addons WHERE addonid='$id'");
				list($groups_admin, $groups_use, $groups_view) = $db->sql_fetchrow($res);
				// Renews the groups rights
				$arr = explode(",", strtolower($groups_admin));
				$groups_admin = "";
				for($v=0;$v<count($arr);$v++) {if($arr[$v]!=$grp_name) {$groups_admin = $groups_admin.",".$arr[$v];}}
				$arr = explode(",", strtolower($groups_use));
				$groups_use = "";				
				for($v=0;$v<count($arr);$v++) {if($arr[$v]!=$grp_name) {$groups_use = $groups_use.",".$arr[$v];}}
				$arr = explode(",", strtolower($groups_view));
				$groups_view = "";				
				for($v=0;$v<count($arr);$v++) {if($arr[$v]!=$grp_name) {$groups_view = $groups_view.",".$arr[$v];}}
				// Update groups list
				if ($right==3) {$groups_admin = $groups_admin.",".$grp_name;}
				if ($right==2) {$groups_use = $groups_use.",".$grp_name;}
				if ($right==1) {$groups_view = $groups_view.",".$grp_name;}				

				// Request Items
				$table = "addons";
				$fields = "groups_admin='$groups_admin', groups_use='$groups_use', groups_view='$groups_view'";
				$where = "addonid='$id' LIMIT 1";
				$return_lnk = "index.php?cmd=admin&menu=".$name."&act=edit&id=".$id;
			break;
			
			case "grouprights":
        global $id;

         // GETS THE POSTED VALUES
        $grp_name = $_POST['grp_name'];
        $ck_addons = $_POST['ck_addons'];
        $ck_aspect = $_POST['ck_aspect'];
        $ck_colors = $_POST['ck_colors'];
        $ck_groups = $_POST['ck_groups'];
        $ck_index = $_POST['ck_index'];
        $ck_panels = $_POST['ck_panels'];
        $ck_security = $_POST['ck_security'];
        $ck_users = $_POST['ck_users'];

        $pref = "UPDATE ".$db_prefix."_admins SET ";
        $sfunc = "";

        for ($v=0;$v<8;$v++) {
          $vv = 0;
          if ($v == 0) {$funct = "addons";  if ($ck_addons) {$vv=1;}}
          if ($v == 1) {$funct = "aspect";  if ($ck_aspect) {$vv=1;}}
          if ($v == 2) {$funct = "colors";  if ($ck_colors) {$vv=1;}}
          if ($v == 3) {$funct = "groups";  if ($ck_groups) {$vv=1;}}
          if ($v == 4) {$funct = "index";   if ($ck_index) {$vv=1;}}
          if ($v == 5) {$funct = "panels";  if ($ck_panels) {$vv=1;}}
          if ($v == 6) {$funct = "security";if ($ck_security) {$vv=1;}}
          if ($v == 7) {$funct = "users";   if ($ck_users) {$vv=1;}}
          // Get current affected groups
  				$res = $db->sql_query("SELECT adm_list FROM ".$db_prefix."_admins WHERE adm_function='".$funct."'");
  				list($adm_list) = $db->sql_fetchrow($res);
  				$adm_list = str_list_without_item($grp_name, $adm_list);
  				// UPDATE RIGHTS
          if ($vv == 1) {$adm_list = $adm_list.",".$grp_name;}
  				// Add right in the database
  				$res = $db->sql_query($pref."adm_list='$adm_list' WHERE adm_function='".$funct."' LIMIT 1;");				
        }

        header("Location: index.php?cmd=admin&menu=".$name."&act=edit&id=".$id);
	   		die();
			break;

			case "aspect":
				global $nbr_barstype;

        // GETS THE POSTED VALUES
        $th_style = $_POST['th_style'];
        $swidth = $_POST['swidth'];
        $cwidth = $_POST['cwidth'];
        $vspace = $_POST['vspace'];
        $bar1 = $_POST['bar1'];
        $bar2 = $_POST['bar2'];
        $bar3 = $_POST['bar3'];
        $logo = $_POST['logo'];
        $th_blocks = $_POST['th_blocks'];
        $lsize = $_POST['lsize'];
        $rsize = $_POST['rsize'];
        $hcspace = $_POST['hcspace'];
				
        // Correct the bars type if upper...
				if ($bar1 < 0) {$bar1 = 0;} elseif ($bar1 > $nbr_barstype) {$bar1 = $nbr_barstype;}
				if ($bar2 < 0) {$bar2 = 0;} elseif ($bar2 > $nbr_barstype) {$bar2 = $nbr_barstype;}
				if ($bar3 < 0) {$bar3 = 0;} elseif ($bar3 > $nbr_barstype) {$bar3 = $nbr_barstype;}

				// Check For Bad Values...
				if (($lsize < 0) || ($lsize > 300)) {$errmsg = ADM_ASPECT_ERR_BWIDTH;}
				if (($rsize < 0) || ($rsize > 300)) {$errmsg = ADM_ASPECT_ERR_BWIDTH;}
				if (($cwidth < 0) || ($cwidth > 100)) {$errmsg = ADM_ASPECT_ERR_CWIDTH;}
				if (($swidth < 20) || ($swidth > 100)) {$errmsg = ADM_ASPECT_ERR_SWIDTH;}
				if (($hcspace < 0) || ($hcspace > 100)) {$errmsg = ADM_ASPECT_ERR_SPACING;}
				if (($vspace < 0) || ($vspace > 100)) {$errmsg = ADM_ASPECT_ERR_SPACING;}

				// Check if the Files Exist
				if (!@file_exists("themes/borders/$th_blocks")) {$errmsg = ADM_ASPECT_ERR_BLOCK;}
				if (!@file_exists("themes/texts/$th_style")) {$errmsg = ADM_ASPECT_ERR_STYLE;}
				// Request Items
				$fields = "site_width='$swidth', col_left_width='$lsize', col_right_width='$rsize', panels_hspacing = '$hcspace', panels_vspacing = '$vspace', "
					."contents_width='$cwidth', theme_edge='$th_blocks', theme_style='$th_style', "
					."bars_001='$bar1', bars_002='$bar2', bars_003='$bar3' ";
				$table = "cfg";
				$where = "";
				$return_lnk = "index.php?cmd=admin&menu=".$name;
			break;

			case "colors":
        // GETS THE POSTED VALUES
        $bgcolsite = $_POST['bgcolsite'];
        $bgcolsupp = $_POST['bgcolsupp'];
        $txtcolsite = $_POST['txtcolsite'];
        $bgcolcontents = $_POST['bgcolcontents'];
        $bgcoltittab = $_POST['bgcoltittab'];
        $bgcoltab = $_POST['bgcoltab'];
        $bgcoltab2 = $_POST['bgcoltab2'];
        $bgcoltitpanel = $_POST['bgcoltitpanel'];
        $bgcolpanel = $_POST['bgcolpanel'];
				
        // Request Items
				$table = "cfg";
				$fields = "site_bgcolor_1='$bgcolsite', site_bgcolor_2='$bgcolsupp', site_txtcolor='$txtcolsite', contents_bgcolor='$bgcolcontents', "
					."panels_title_bgcolor='$bgcoltitpanel', panels_bgcolor='$bgcolpanel', tab_title_bgcolor='$bgcoltittab', "
					."tab_bgcolor='$bgcoltab', tab_title_bgcolor2='$bgcoltab2'";
				$where = "";
				$return_lnk = "index.php?cmd=admin&menu=".$name;
			break;

			case "groups":
				global $id;
				
        // GETS THE POSTED VALUES
        $grpname = $_POST['grpname'];
        $grpdesc = $_POST['grpdesc'];

				// Check For Bad Values...
				if (trim($grpname) == "") {$errmsg = ADM_ERR_NOEMPTYNAME;}
				// Request Items
				$table = $acc_prefix."_groups";
				$fields = "group_name='$grpname', group_desc='$grpdesc'";
				$where = "groupid='$id' LIMIT 1";
				$return_lnk = "index.php?cmd=admin&menu=".$name;
			break;

			case "panels":
				global $id;
				
        // GETS THE POSTED VALUES
        $pantit = $_POST['pantit'];
        $pantype = $_POST['pantype'];
        $pantxt = $_POST['pantxt'];
        $panaddon = $_POST['panaddon'];
        $posw = $_POST['posw'];        				

				// Check For Bad Values...
				if (($posw < 0) || ($posw > 2)) {$errmsg = ADM_PANELS_ERR_POSX;}
				if ($pantype == 1) {$pantxt = "";} else {$panaddon = "";}
				// Request Items
				$table = "panels";
				$fields = "panel_addon='$panaddon', panel_title='$pantit', panel_text='$pantxt', panel_type='$pantype', posx='$posw'";
				$where = "panelid='$id' LIMIT 1";
				$return_lnk = "index.php?cmd=admin&menu=".$name;
			break;

			case "security":
        // GETS THE POSTED VALUES
        $sec_code = $_POST['sec_code'];

				// Request Items
				$table = "cfg";
				$fields = "secur_code='$sec_code'";
				$where = "";
				$return_lnk = "index.php?cmd=admin&menu=".$name;
			break;

			case "users":
				// Request Items
				$table = "users";
				$fields = "";
				$where = "";
				$return_lnk = "index.php?cmd=admin&menu=".$name;

        $errmsg = "...CETTE FONCTION EST EN COURS DE DEVELOPPEMENT...";			
			break;
				
			
      case "usergroups":
				global $id;

        // GETS THE POSTED VALUES
        $grpid = $_POST['grpid'];
        $member = $_POST['member'];

				// Get the groups list
				$res = $db->sql_query("SELECT user_groups FROM ".$db_prefix."_".$acc_prefix."_users WHERE userid='$id' LIMIT 1");
				list($user_groups) = $db->sql_fetchrow($res);				
				$arr = explode(",", $user_groups);			
				// Build a new list
				$blist = "";
				$res = $db->sql_query("SELECT group_name FROM ".$db_prefix."_".$acc_prefix."_groups WHERE groupid='$grpid' LIMIT 1");
				if (list($group_name) = $db->sql_fetchrow($res)) {			
					for($v=0;$v<count($arr);$v++) {
						if ($group_name != $arr[$v]) {$blist = $blist.",".$arr[$v];}
					}
					if ($member == "chk") {$blist = $blist.",".$group_name;}
					$blist = substr($blist, 1);
				}
				// Request Items
				$table = $acc_prefix."_users";
				$fields = "user_groups='$blist'";
				$where = "userid='$id' LIMIT 1";
				$return_lnk = "index.php?cmd=admin&menu=users&act=edit&id=".$id;
			break;
		}

		// ========================================================
		// This Code Change the Values in Database
		// ========================================================
		if ($table) {
			if ($errmsg == "") {
				// ATTEMPT TO ADD THE NEW ENTRY
				if ($where != "") {$where = " WHERE ".$where;}
   			$res = $db->sql_query("UPDATE ".$db_prefix."_".$table." SET ".$fields.$where);
				if (!$res) {
					// Display Error Message...
					$page->html_header(ADM_TITLE_ADMIN);
					$page->title_page(ADM_TITLE_ADMIN, 2);
					$page->MsgBox(ERR_DB_CANTMODIF, TXT_OK);
					$page->html_foot();
					die("");
				}
				header("Location: ".$return_lnk);
				die();
			} else {
				// Display Error Message...
				$page->html_header(ADM_TITLE_ADMIN);
				$page->title_page(ADM_TITLE_ADMIN, 2);
				$page->MsgBox($errmsg, TXT_OK);
				$page->html_foot();
				die();
			}
		}
		// ========================================================
	}
	
  function panels_move($id, $op) {
		// ========================================================
		// Move a Panel
		// ========================================================
		// - $id       : ID of panel in database
		// - $posx     : 0=Center, 1=Left, 2=Right
		// - $ref      : Strating position of panel to move
		// - $op       : Opérator : 1 or -1
		// ========================================================
		global $db, $db_prefix;

    // GETS THE POSTED VALUES
    $posx = $_GET['posx'];
    $ref = $_GET['ref'];
		
    // $OP Regularization.
		if ($op < 0) {
			$op = -1;
			$nref = $ref - 1;
		} else {
			$op = 1;
			$nref = $ref;
		}

		if ($nref == 0) {
			// Push the displayed panels, if a new panel was added
			$res = $db->sql_query("SELECT panelid, posy FROM ".$db_prefix."_panels WHERE posx='$posx' AND posy>\n'$ref' ORDER BY posy");
			while(list($panelid, $posy) = $db->sql_fetchrow($res)) {
				$npos = $posy + $op;
				$db->sql_query("UPDATE ".$db_prefix."_panels SET posy='$npos' WHERE panelid='$panelid'");
			}
		} else {
			// Update the panel it will be swapped, if the panel was already displayed
			$replace = $ref + $op;
			$res = $db->sql_query("SELECT panelid FROM ".$db_prefix."_panels WHERE posx='$posx' AND posy='$replace'");
			if (list($panelid) = $db->sql_fetchrow($res)) {
				$db->sql_query("UPDATE ".$db_prefix."_panels SET posy='$ref' WHERE panelid='$panelid'");
			}
		}

		// Update the panel will be moved
		$npos = $ref + $op;
		$db->sql_query("UPDATE ".$db_prefix."_panels SET posy='$npos' WHERE panelid='$id'");

		// Return to Panels Menu when done...
		header("Location: index.php?cmd=admin&menu=panels");
		die();
	}
	// ******************************************************************
}

function str_list_without_item($item, $read_list) {
  // ========================================================
	// Rebuild a list without [$item]
	// ========================================================
	// - $item       : Item will be removed
	// ========================================================	
	$arr = explode(",", $read_list);
	$read_list = "";
	for($v=0;$v<count($arr);$v++) {if($arr[$v]!=$item) {$read_list = $read_list.",".$arr[$v];}}
  if ($read_list == ",") {$read_list = "";}
	return $read_list;
}

function db_update_groups_set() {
	global $db, $db_prefix, $acc_prefix;

	// Build  the groups list in string format : 'grp1','grp2','grp3',...
	$lst = "'Master','All'";
	$res = $db->sql_query("SELECT group_name FROM ".$db_prefix."_".$acc_prefix."_groups ORDER BY group_name");
	while(list($group_name) = $db->sql_fetchrow($res)) {
		$lst = $lst.",'".$group_name."'";
	}
	
	// Change fields in SET format in addons table
	$rq = "ALTER TABLE ".$db_prefix."_addons "
		."CHANGE groups_admin groups_admin SET(".$lst.") NOT NULL, "
		."CHANGE groups_use groups_use SET(".$lst.") NOT NULL, "
    ."CHANGE groups_view groups_view SET(".$lst.") NOT NULL";
	$res = $db->sql_query($rq);
	// Change fields in SET format in users table
	$rq = "ALTER TABLE ".$db_prefix."_".$acc_prefix."_users "
		 ."CHANGE user_groups user_groups SET(".$lst.") NOT NULL";
	$res = $db->sql_query($rq);
	// Change fields in SET format in admins table
	$rq = "ALTER TABLE ".$db_prefix."_admins "
    ."CHANGE adm_list adm_list SET(".$lst.") NOT NULL";
	$res = $db->sql_query($rq);	
}

function build_table_addons() {
	global $db, $db_prefix, $acc_prefix, $tab_bgcolor, $tab_title_bgcolor2, $tab_title_bgcolor, $addon_list;

	$bgc = $tab_bgcolor;

	$cmpt = count($addon_list);
	for ($v=0; $v<$cmpt; $v++) {
		// Get the Add-On ID. If is not found, the Add-On will be add
		// in database...
		$res = $db->sql_query("SELECT addonid, addon_name FROM ".$db_prefix."_addons WHERE addon_name LIKE '$addon_list[$v]'");
		list($addonid, $addon_name) = $db->sql_fetchrow($res);
		if ($addon_name == "") {
			// ADD THE ENTRY IN DATABASE
			$fields = "addonid, addon_name, groups_admin, groups_use, groups_view";
			$values = "'NULL', '$addon_list[$v]', '', '', ''";
			$res = $db->sql_query("INSERT INTO ".$db_prefix."_addons (".$fields.") VALUES (".$values.")");
			$res = $db->sql_query("SELECT addonid, addon_name FROM ".$db_prefix."_addons WHERE addon_name LIKE '$addon_list[$v]'");
			list($addonid, $addon_name) = $db->sql_fetchrow($res);
		}
		if ($addon_name != "") {
			echo "<tr>\n<td bgcolor=\"".$bgc."\">\n".$addon_list[$v]."</td>\n"
				."<td align=\"center\" bgcolor=\"".$bgc."\">\n</td>\n"
				."<td align=\"center\" bgcolor=\"".$bgc."\">\n</td>\n"
				."<td align=\"center\" bgcolor=\"".$bgc."\">\n<a href=\"index.php?cmd=admin&menu=addonrights&act=edit&id=".$addonid."\">\n".TXT_RIGHTS."</a>\n</td>\n</tr>\n";
			if ($bgc == $tab_bgcolor) {$bgc = dechex(hexdec($tab_bgcolor) - hexdec("111111"));} else {$bgc = $tab_bgcolor;}
		}
	}
}

function build_table_groups() {
	global $db, $db_prefix, $acc_prefix, $tab_bgcolor, $tab_title_bgcolor2, $tab_title_bgcolor;

	$bgc = $tab_bgcolor;

	echo "<tr>\n<td bgcolor=\"".$tab_title_bgcolor2."\">\nMASTER</td>\n<td bgcolor=\"".$tab_title_bgcolor2."\">\n".ADM_DESC_MASTER
		."</td>\n<td align=\"center\" bgcolor=\"".$tab_title_bgcolor2."\">\n".TXT_NATIVE
		."</td>\n<td align=\"center\" bgcolor=\"".$tab_title_bgcolor2."\">\n".TXT_NATIVE."</td>\n</tr>\n";
	echo "<tr>\n<td bgcolor=\"".$tab_title_bgcolor2."\">\nALL</td>\n<td bgcolor=\"".$tab_title_bgcolor2."\">\n".ADM_DESC_GUEST
		."</td>\n<td align=\"center\" bgcolor=\"".$tab_title_bgcolor2."\">\n".TXT_NATIVE
		."</td>\n<td align=\"center\" bgcolor=\"".$tab_title_bgcolor2."\">\n".TXT_NATIVE."</td>\n</tr>\n";	
	// GET GROUPS LIST
	$res = $db->sql_query("SELECT groupid, group_name, group_desc FROM ".$db_prefix."_".$acc_prefix."_groups");
	while(list($groupid, $group_name, $group_desc) = $db->sql_fetchrow($res)) {
		echo "<tr>\n<td bgcolor=\"".$bgc."\">\n<a href=\"index.php?cmd=admin&menu=groups&act=edit&id=".$groupid."\">\n".$group_name."</a>\n"
			."</td>\n<td bgcolor=\"".$bgc."\">\n".$group_desc
			."</td>\n<td align=\"center\" bgcolor=\"".$bgc."\">\n<a href=\"index.php?cmd=admin&menu=grouprights&act=edit&id=".$groupid."\">\n".TXT_RIGHTS."</a>\n";
		echo "</td>\n<td align=\"center\" bgcolor=\"".$bgc."\">\n<a href=\"index.php?cmd=admin&menu=groups&act=delete&id=".$groupid."\">\n".TXT_DELETE."</a>\n"
			."</td>\n</tr>\n";
		if ($bgc == $tab_bgcolor) {$bgc = dechex(hexdec($tab_bgcolor) - hexdec("111111"));} else {$bgc = $tab_bgcolor;}
	}
}

function build_table_panels($column) {
	global $db, $db_prefix, $tab_bgcolor, $tab_title_bgcolor2, $tab_title_bgcolor;

	$bgc = $tab_bgcolor;

	// GET PANELS NUMBER
	$rp = $db->sql_query("SELECT * FROM ".$db_prefix."_panels WHERE posx='$column' AND posy>\n'0'");
	$max = $db->sql_numrows($rp);
	// GET THE PANELS LIST
	$rp = $db->sql_query("SELECT panelid, panel_addon, panel_title, panel_type, posy FROM ".$db_prefix."_panels WHERE posx='$column' ORDER BY posy");
	while(list($panelid, $panel_addon, $panel_title, $panel_type, $posy) = $db->sql_fetchrow($rp)) {
		if ($panel_type == 1) {	
			$rp1 = $db->sql_query("SELECT addon_name FROM ".$db_prefix."_addons WHERE addonid='$panel_addon'");
			if (list($addon_name) = $db->sql_fetchrow($rp1)) {
				$ptype = "<a href=\"index.php?cmd=admin&menu=addonrights&act=edit&id=".$panel_addon."\">\n".$addon_name."</a>\n";
			} else {
				$ptype = "<font color=\"#FF0000\">\n[".TXT_ERROR."]</font>\n";
			}
		} else {
			$ptype = "[".TXT_NO."]";
		}
		if ($posy == 0) {$bgc = $tab_title_bgcolor2;}
		echo "<tr>\n<td bgcolor=\"".$bgc."\">\n<a href=\"index.php?cmd=admin&menu=panels&act=edit&id=".$panelid."\">\n<b>\n".$panelid."</b>\n</a>\n</td>\n"
			."<td bgcolor=\"".$bgc."\">\n".$panel_title."</td>\n"
			."<td align=\"center\" bgcolor=\"".$bgc."\">\n".$ptype."</td>\n"		
			."<td align=\"center\" bgcolor=\"".$bgc."\">\n".$posy."</td>\n"
			."<td align=\"center\" bgcolor=\"".$bgc."\">\n";
		if ($posy >\n 0) {
			echo "<a href=\"index.php?cmd=admin&menu=panels&act=moveup&posx=$column&id=$panelid&ref=$posy\">\n<img border=\"0\" src=\"kernel/pics/up.gif\">\n</a>\n";
		}
		if (($posy < $max) or ($max == 0)) {
			echo "<a href=\"index.php?cmd=admin&menu=panels&act=movedown&posx=$column&id=$panelid&ref=$posy\">\n<img border=\"0\" src=\"kernel/pics/down.gif\">\n</a>\n";
		}
		echo "</td>\n<td align=\"center\" bgcolor=\"".$bgc."\">\n"
			."<a href=\"index.php?cmd=admin&menu=panels&act=delete&id=".$panelid."\">\n".TXT_DELETE."</a>\n"
			."</td>\n</tr>\n";
		if (($bgc == $tab_bgcolor) and ($posy != 0)) {$bgc = dechex(hexdec($tab_bgcolor) - hexdec("111111"));} else {$bgc = $tab_bgcolor;}			
	}
}

function build_table_users() {
	global $db, $db_prefix, $acc_prefix, $tab_bgcolor, $tab_title_bgcolor2, $tab_title_bgcolor;

	$bgc = $tab_bgcolor;
	
	// GET USERS LIST
	$res = $db->sql_query("SELECT userid, user_name, user_lastlog FROM ".$db_prefix."_".$acc_prefix."_users");
	while(list($userid, $user_name, $user_lastlog) = $db->sql_fetchrow($res)) {
		echo "<tr>\n<td bgcolor=\"".$bgc."\">\n<a href=\"index.php?cmd=admin&menu=users&act=edit&id=".$userid."\">\n<b>\n".$user_name."</b>\n</a>\n"
			."</td>\n<td bgcolor=\"".$bgc."\">\n".$user_lastlog
			."</td>\n<td align=\"center\" bgcolor=\"".$bgc."\">\n<a href=\"index.php?cmd=admin&menu=usersgrp&act=edit&id=".$userid."\">\n".TXT_GROUPS."</a>\n";
		echo "</td>\n<td bgcolor=\"".$bgc."\">\n<a href=\"index.php?cmd=admin&menu=users&act=delete&id=".$userid."\">\n".TXT_DELETE."</a>\n"
			."</td>\n</tr>\n";
		if ($bgc == $tab_bgcolor) {$bgc = dechex(hexdec($tab_bgcolor) - hexdec("111111"));} else {$bgc = $tab_bgcolor;}
	}
}

function users_groups_list() {
	// [$userid] must be initialised by the calling function
	global $db, $db_prefix, $acc_prefix, $tab_bgcolor, $tab_title_bgcolor2, $tab_title_bgcolor, $userid;
	
	$bgc = $tab_bgcolor;
	$cmpt = 0;
	echo "<table width=\"100%\">\n<tr>\n<td bgcolor=\"".$tab_title_bgcolor."\">\n<b>\n".ADM_USER_MEMBEROF."</b>\n</td>\n"
		."<td bgcolor=\"".$tab_title_bgcolor."\">\n</td>\n<td bgcolor=\"".$tab_title_bgcolor."\">\n</td>\n"
		."<td bgcolor=\"".$tab_title_bgcolor."\">\n</td>\n</tr>\n";

  echo "<tr>\n<td align=\"left\" bgcolor=\"".$tab_title_bgcolor2."\">\n<b>\nALL</b>\n</td>\n<td align=\"left\" bgcolor=\"".$tab_title_bgcolor2."\">\n".ADM_DESC_GUEST;
  echo "</td>\n<td align=\"center\" bgcolor=\"".$tab_title_bgcolor2."\" width=\"16\">\n</td>\n";
  echo "<td align=\"center\" bgcolor=\"".$tab_title_bgcolor2."\" width=\"60\">\n</td>\n</tr>\n";
  
	if (user_is_member("master", $userid)) {
    echo "<tr>\n<td align=\"left\" bgcolor=\"".$tab_title_bgcolor2."\">\n<b>\nMASTER</b>\n</td>\n<td align=\"left\" bgcolor=\"".$tab_title_bgcolor2."\">\n".ADM_DESC_MASTER;
    echo "</td>\n<td align=\"center\" bgcolor=\"".$tab_title_bgcolor2."\" width=\"16\">\n</td>\n";
    echo "<td align=\"center\" bgcolor=\"".$tab_title_bgcolor2."\" width=\"60\">\n</td>\n</tr>\n";
  }
	
  // GET GROUPS LIST
	$res = $db->sql_query("SELECT groupid, group_name, group_desc FROM ".$db_prefix."_".$acc_prefix."_groups ORDER BY group_name LIMIT 64");
	while(list($groupid, $group_name, $group_desc) = $db->sql_fetchrow($res))
  {
		$cmpt++;
		formw();
		echo "<input type=\"hidden\" name=\"cmd\" value=\"admin\">\n"
			."<input type=\"hidden\" name=\"menu\" value=\"usergroups\">\n<input type=\"hidden\" name=\"act\" value=\"modify\">\n"
			."<input type=\"hidden\" name=\"id\" value=\"".$userid."\">\n<input type=\"hidden\" name=\"grpid\" value=\"".$groupid."\">\n";
		echo "<tr>\n<td align=\"left\" bgcolor=\"".$bgc."\">\n<a href=\"index.php?cmd=admin&menu=groups&act=edit&id=".$groupid."\">\n".$group_name."</a>\n";
		echo "</td>\n<td align=\"left\" bgcolor=\"".$bgc."\">\n".$group_desc;
		echo "</td>\n<td align=\"center\" bgcolor=\"".$bgc."\" width=\"16\">\n";
		if (user_is_member($group_name, $userid)) {$ck = " checked";} else {$ck = "";}
		echo "<input type=\"checkbox\" name=\"member\" value=\"chk\"".$ck.">\n</td>\n";
		echo "<td align=\"center\" bgcolor=\"".$bgc."\" width=\"60\">\n<input type=\"submit\" value=\"".TXT_SAVE."\">\n</td>\n</tr>\n</form>\n";
		if ($bgc == $tab_bgcolor) {$bgc = dechex(hexdec($tab_bgcolor) - hexdec("111111"));} else {$bgc = $tab_bgcolor;}		
	}
	if ($cmpt == 0) {echo "<tr>\n<td colspan=\"4\">\n".ADM_GROUPS_NOGRP."</td>\n</tr>\n";}
  echo "</table>\n";
}

function pannels_shift() {
	// ========================================================
	// Shift all panels after [$posy] to top
	// ========================================================
	global $posy;

	if ($posy >\n 0) {
		$res = $db->sql_query("SELECT panelid, posy FROM ".$db_prefix."_panels WHERE posx='$posx' AND posy>\n'$posy' ORDER BY posy");
		while(list($panelid, $posy) = $db->sql_fetchrow($res)) {
			$npos = $posy - 1;
			$db->sql_query("UPDATE ".$db_prefix."_panels SET posy='$npos' WHERE panelid='$panelid'");
		}
	}
	// ========================================================
}

?>
