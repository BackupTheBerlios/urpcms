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
// | This file contain the whole of the principal functions, for a
// | good working of UrPCMS.
// |
// | urkernel.php : V 0.0.4
// ******************************************************************

if (eregi("urkernel.php", $_SERVER['PHP_SELF'])) {Header("Location:../index.php");die();}

// ******************************************************************
// * MISC FUNCTIONS                                                 *
// ******************************************************************

function make_code($base=1000000, $nbr_digits=6) {
	// ========================================================
	// Generat a random code
	// ========================================================
	//
	list($usec, $sec) = explode(' ', microtime());
	$rnd = (float) $sec + ((float) $usec * $base);
	mt_srand($rnd);
	$cd = mt_rand();
	return substr($cd, strlen($cd) - $nbr_digits, $nbr_digits);
}

function encrypt_data($data) {
	$data = md5($data);
	$data = base64_encode(md5(make_code(1000000000000, 12)).";".$data.";".md5(make_code(1000000000000, 12)));
	return $data;
}

function decrypt_data($data) {
	$ctmp = base64_decode($data);
	$ctmp = explode(";", $ctmp);
	return $ctmp[1];
}

function please_respect_copyright() {
	global $kernel;
	
	echo "<center><table><tr><td bgcolor=\"#FFFFFF\"><img border=\"0\" src=\"kernel/pics/pwurpcms.gif\"></td><td align=\"center\">"
		."Ce site utilise UrPCM ".$kernel->version.".".$kernel->revision.".".$kernel->build." - (C) 2004 Bruno Cluizel - UrStudios.Net<br>"
		."Tous les Logos et Marques sitées sont la propriété de leurs propriétaires respectifs.</td><td><img src=\"kernel/pics/php2.gif\">"
		."</td></tr></table></center>";
}

function user_is_member($group, $userid) {
	// ========================================================
	// Return [True] if user [$userid] is a member of [$group]
	// ========================================================
	//
	global $db, $db_prefix, $acc_prefix;

	// GET USER'S GROUPS	
	$res = $db->sql_query("SELECT user_groups FROM ".$db_prefix."_".$acc_prefix."_users WHERE userid='$userid' LIMIT 1");
	list($user_groups) = $db->sql_fetchrow($res);
	// This is not fastest method but the most reliable.
	$arr = explode(",", $user_groups);	
	$cmpt = count($arr);
	$ret = false;
	for ($v=0; $v<$cmpt; $v++) {	
		if (strtolower($arr[$v]) == strtolower($group)) {return true;}
	}
	return $ret;
}

// ******************************************************************



// ******************************************************************
// * KERNEL CLASS                                                   *
// ******************************************************************
// * This class contains all kernel informations.                   * 
// ******************************************************************
class ur_kernel {
	var $version;
	var $revision;
	var $build;
	
	function ur_kernel() {
		$this->version = "0";
		$this->revision = "0";
		$this->build = "4";
	}
}
// ******************************************************************



// ******************************************************************
// * USER CLASS                                                     *
// ******************************************************************
// * This class contains the user Object's properties. When it's    *
// * initialized the NAME and the GROUPS properties was available.  * 
// ******************************************************************
class ur_user {
	var $name;
	var $groups;
	var $db_id;

	function ur_user() {
		global $page, $timezone;

    $ccook = $_COOKIE["session_grp"];
		$cook = explode(";", $ccook);
		$this->name = $cook[0];
		$this->groups = $cook[1];
		$this->db_id = $cook[2];
		// Renew the cookie if an user is logged
		if ($this->name != "") {setcookie("session_grp", $ccook, time() + $timezone  + 3600);}
	}

	function login() {
		// ========================================================
		// User Login
		// ========================================================
		//
		global $db, $db_prefix, $acc_prefix, $back, $timezone, $secur_code;

    // GET THE POSTED VALUES
    $usr_name = $_POST["usr_name"];
    $usr_pass = $_POST["usr_pass"];
    $sec = $_POST["sec"];		

    if ($back == "") {$lk = "Location: index.php";} else {$lk = "Location: index.php?cmd=".$back;}
		$ref = $_COOKIE["session_code"];
		$cref = decrypt_data($ref);
		$sec = md5($sec);

    // First, we check the security code if needed...
		if (($sec == $cref) OR ($secur_code == 0)) {
			if ($usr_name != "") {
				$res = $db->sql_query("SELECT userid, user_pass, user_groups FROM ".$db_prefix."_".$acc_prefix."_users WHERE user_name LIKE '$usr_name'");
				if (list($userid, $user_pass, $user_groups) = $db->sql_fetchrow($res)) {
					$user_pass = decrypt_data($user_pass);
					$usr_pass = md5($usr_pass);
					if ($user_pass == $usr_pass) {
						// Update class values
						$this->name = $usr_name;
						$this->groups = $user_groups;
						$this->db_id = $userid;
						// Create session cookie
						setcookie("session_grp", $usr_name.";".$user_groups.";".$userid , time() + $timezone  + 3600);
						header($lk);
						die();
					} else {$tmp_msg = ERR_LOG_PASSWASDINV;}
				} else {$tmp_msg = ERR_LOG_IN;}
			} else {$tmp_msg = ERR_ACC_NEEDANAME;}
		} else {$tmp_msg = ERR_LOG_CODEWASDIFF;}

		// ========================================================
		// ERROR MESSAGE
		// ========================================================
		global $page;
		$page->html_header(TXT_LOG_ADDMASTER);
		$page->title_page(TXT_LOG_LOGINTITLE, 2);
		$page->MsgBox($tmp_msg, TXT_OK);
		$page->html_foot();
		die();
		// ========================================================
	}

	function logout() {
		// ========================================================
		// User Logout
		// ========================================================
		//
		global $timezone;
		setcookie("session_grp", "" , time() + $timezone  - 3600);
		header("Location: index.php");
		die();
	}

	function create_account($groups) {
		// ========================================================
		// Creating an account
		// ========================================================
		//
		global $db, $db_prefix, $acc_prefix, $timezone;

    // GET THE POSTED VALUES
    $account_name = $_POST["account_name"];
    $pass1 = $_POST["pass1"];
    $pass2 = $_POST["pass2"];
    $sec = $_POST["sec"];
    
		$ref = $_COOKIE["session_code"];
		$cref = decrypt_data($ref);
		$sec = md5($sec);
		
		if ($sec == $cref) {
			if ($account_name != "") {
				if ($pass1 != "") {
					if ($pass1 == $pass2) {
						unset($pass2);
						$pass = encrypt_data($pass1);
						$rq = "INSERT INTO ".$db_prefix."_".$acc_prefix."_users (userid, user_name, user_pass, user_groups) VALUES ('NULL', '$account_name', '$pass', '$groups')";
						$rt = $db->sql_query($rq);
						if ($rt) {
							// The account was created...
							setcookie("session_code", "", time() + $timezone  - 3600);
							header("Location: index.php");
							die();
						} else {$tmp_msg = ERR_ACC_CANTCREATE1;}
					} else {$tmp_msg = ERR_ACC_PASSWASDIFF;}
				} else {$tmp_msg = ERR_ACC_NEEDAPASS;}
			} else {$tmp_msg = ERR_ACC_NEEDANAME;}
		} else {$tmp_msg = ERR_ACC_CANTCREATE2;}

		// ========================================================
		// ERROR MESSAGE
		// ========================================================
		global $page;
		$page->html_header(TXT_LOG_ADDMASTER);
		$page->title_page(ERR_NOMASTERACC, 2);
		$page->MsgBox($tmp_msg, TXT_OK);
		$page->html_foot();
		die();
		// ========================================================
	}

	function is_admin() {
		// ========================================================
		// Return [True] if the LOGGED user have at least, one admin right
		// ========================================================
		// Returned values :
		// -----------------
		// - false	: Current user have no admin right
		// - true	  : current user have at least one admin right
		// ========================================================
		global $db, $db_prefix, $addon_list_name, $addon_list_groups;
	
		// If user is not at least a member of one group, he can't manage...
		if ($this->groups != "") {	
			// Build user's groups list in array
			$grp = explode(",", $this->groups);
			$cnt_grp = count($grp);
			$cnt_addons = count($addon_list_name);
	
			// Scan all user groups
			for ($v=0; $v<$cnt_grp; $v++) {
				// Check for master account (All right)
				if (strtolower($grp[$v]) == "master") {return true;}
				// Check for internal admin functions
				$res = $db->sql_query("SELECT adm_list FROM ".$db_prefix."_admins");	
				while(list($adm_list) = $db->sql_fetchrow($res)) {
					$adm_grp = explode(",", $adm_list);
					$cnt_admgrp = count($adm_grp);			
					for ($g=0; $g<$cnt_admgrp; $g++) {				
						if (strtolower($adm_grp[$g]) == strtolower($grp[$v])) {return true;}
					}
				}
				// Check add-ons rights
				for ($a=0; $a<$cnt_addons; $a++) {			
					// Build addon admin's groups list
					$adm_grp = explode(",", $addon_list_groups[$a][2]);
					$cnt_admgrp = count($adm_grp);				
					for ($g=0; $g<$cnt_admgrp; $g++) {	
						if (strtolower($adm_grp[$g]) == strtolower($grp[$v])) {return true;}
					}				
				}
			}
		}	
		return false;
	}
	
	function have_nativ_right($adm_func) {
		// ========================================================
		// Return [True] if the LOGGED is at least, member of one admin group
		// ========================================================
		// Entry variables :
		// -----------------
		// - $adm_func  : String > Name of nativ function
		// ========================================================
		// Returned values :
		// -----------------
		// - false	: Current user have no admin right
		// - true	  : current user have at least one admin right
		// ========================================================		
    global $db, $db_prefix;
    
    $res = $db->sql_query("SELECT adm_list FROM ".$db_prefix."_admins WHERE adm_function='$adm_func'");
    if ($res) {
  		// Build user's groups list in array
  		$grp = explode(",", $this->groups);
  		$cnt_grp = count($grp);

      // Build admin groups for the function [$adm_func]
      list($adm_list) = $db->sql_fetchrow($res);
  		$arr = explode(",", $adm_list);
  		
  		// Check if the user is member of at least one admin group
			for($v=0;$v<count($arr);$v++) {
        for($g=0;$g<$cnt_grp;$g++) {
          if (strtolower($grp[$g]) == "master") {return true;}
          if (strtolower($arr[$v]) == strtolower($grp[$g])) {return true;}
        }
      }
    }
    return false;
  }
	
	function addon_right($addonid) {
		// ========================================================
		// Return right level of LOGGED user for the [$addonid] Add-On
		// ========================================================
		// Returned values :
		// -----------------
		// 0 = User have no right
		// 1 = User can view the add-on	
		// 2 = User can use the add-on
		// 3 = User can manage the add-on
		// ========================================================
		global $db, $db_prefix;
		
		$rtval = 0;
		// Build user's groups list in array
		$grp = explode(",", $this->groups);
		$cnt_grp = count($grp);			
		//
		$res = $db->sql_query("SELECT groups_admin, groups_use, groups_view FROM ".$db_prefix."_addons WHERE addonid='$addonid' LIMIT 1");
		if ($res) {
			list($groups_admin, $groups_use, $groups_view) = $db->sql_fetchrow($res);
			for ($a=0; $a<3; $a++) {
				// Build the add-on groups list
				if ($a == 0) {$t_grp = explode(",", $groups_view);}
				if ($a == 1) {$t_grp = explode(",", $groups_use);}
				if ($a == 2) {$t_grp = explode(",", $groups_admin);}
				$cnt_tgrp = count($t_grp);				
				// Scanning user's group list  			
				for ($v=0; $v<$cnt_grp; $v++) {
					// If user was a MASTER, we get out immediately
					if (strtolower($grp[$v]) == "master") {return 3;}
					// Scanning add-on's group list  
					for ($g=0; $g<$cnt_tgrp; $g++) {				
						if ($t_grp[$g] != "") {				
						    if (strtolower($t_grp[$g]) == strtolower($grp[$v])) {
								// If Addon-On group list contain an user's group
						        $rtval = $a + 1;
						    } 
							if (strtolower($t_grp[$g]) == "all") {
								// If Addon-On group list contain the group ALL
								$rtval = $a + 1;
							} 
						}
					}			
				}
			}
		}
		return $rtval;
	}	
}

// ******************************************************************



// ******************************************************************
// * HTML FUNCTIONS                                                 *
// ******************************************************************
class html_page {

	function MsgBox($txt_msg, $butt1, $butt2='', $flag=0, $post_lnk="javascript:history.go(-1)") {
		// ========================================================
		// Dispaly a message box with 1 or 2 buttons and 1 icon
		// ========================================================
		// - $txt_msg     : Message text. You can include HTML code
		// - $butt1       : Text for the button 1
		// - $butt2       : Text for the button 2
		// - $flag        : The flag argument define the type of the
		//                  message. The icon will be adapted :
		//                  0 = Alert msg (Warning icon)
		//                  1 = Question msg (Question mark icon)
		// - $post_lnk    : Post link for the form
		// ========================================================
		global $contents_bgcolor;

		if (!$contents_bgcolor) {$contents_bgcolor = "FFFFFF";}

		if ($flag == 1) {
			// Question
			$errpic = "kernel/pics/ic-48/warning.gif";
			$tab1 = "<table align=\"center\" border\"1\" width=\"200\"><tr><td align=\"center\">";
			$tab2 = "</td><td align=\"center\">";
			$tab3 = "</td></tr></table>";
			$button1 = "<form action=\"".$post_lnk."\" method=\"post\"><input type=\"hidden\" name=\"confirm\" value=\"".true."\"><input type=\"submit\" value=\"".$butt1."\" style=\"width: 120\"></form>";
			$button2 = "<form action=\"javascript:history.go(-1)\" method=\"post\"><input type=\"submit\" value=\"".$butt2."\" style=\"width: 120\"></form>";

		} elseif ($flag == 2) {

		} else {
			// DEFAULT OR OTHER : Simple message with [OK] button
			$errpic = "kernel/pics/ic-48/warning.gif";
			$tab1 = "";
			$tab2 = "";
			$tab3 = "";
			$button1 = "<form action=\"".$post_lnk."\" method=\"post\"><input type=\"submit\" value=\"".$butt1."\" style=\"width: 120\"></form>";
			$button2 = "";
		}

		echo "<table align=\"center\" border=\"4\" style=\"border-collapse: collapse\" bordercolor=\"#FF0000\" width=\"60%\" bgcolor=\"#".$contents_bgcolor."\"><tr><td>"
			."<table align=\"center\" border=\"0\" bgcolor=\"#".$contents_bgcolor."\"><tr><td align=\"center\"><img src=\"".$errpic."\" height=\"48\" width=\"48\"></td>"
			."<td align=\"center\" valign=\"middle\"><font class=\"text_big_dark\">".$txt_msg."</font></td></tr><tr><td colspan=\"2\" align=\"center\"><br><br>"
			.$tab1.$button1.$tab2.$button2.$tab3."</td></tr></table></td></tr></table>";
	}

	function html_header($pagetitle) {
		// ========================================================
		// Make the HTML page header
		// ========================================================
		// - $pagetitle    : Title of the page (Will be diplayed in browser title bars)
		// ========================================================
		global $site_title, $site_author, $site_copyright, $site_description, $site_keywords, $site_bgcolor_1, $site_bgcolor_2, $site_txtcolor;
		global $site_border_size, $site_width, $site_border_color, $col_left_width, $panels_hspacing, $panels_vspacing, $header_banner, $header_bglogo;
		global $theme_style, $urpcms_version, $contents_width, $bars_001, $bars_002, $bars_003;
		global $page, $user, $db, $db_prefix;

		if ($pagetitle != "") {$pagetitle = " - ".$pagetitle;}
		echo "<html>\n<head>\n"
			// ***********************************************
			// * THIS COPYRIGHT CAN'T BE REMOVED. SEE THE    *
			// * GNU/GPL LICENSE FOR MORE INFORMATIONS...    *
			// ***********************************************
			."<meta name=\"GENERATOR\" content=\"UrPCMS ".$urpcms_version." - Copyright 2003 UrStudios.Net : http://www.urstudios.net\">\n"
			// ***********************************************
			// * The following line contains your copyright. *
			// * There, you can write what you want....      *
			// ***********************************************
			."<meta name=\"AUTHOR\" content=\"".$site_author."\">\n<meta name=\"COPYRIGHT\" content=\"".$site_copyright."\">\n"
			."<meta name=\"DESCRIPTION\" content=\"".$site_description."\">\n<meta name=\"RESOURCE-TYPE\" content=\"DOCUMENT\">\n"
			."<meta name=\"KEYWORDS\" content=\"".$site_keywords."\">\n"
			// Search engines meta flags...
			."<meta name=\"ROBOTS\" content=\"INDEX, FOLLOW\">\n<meta name=\"REVISIT-AFTER\" content=\"1 DAYS\">\n"
			."<meta http-equiv=\"Content-Type\" content=\"text/html; charset=ISO-8859-1\">\n"
			."<link rel=\"StyleSheet\" href=\"themes/texts/".$theme_style."/index.css\" TYPE=\"text/css\">\n"
			."<title>".$site_title.$pagetitle."</title>\n"
			."</head>\n\n\n";
		echo "<body bgcolor=\"#".$site_bgcolor_1."\" text=\"#".$site_txtcolor."\" topmargin=\"0\" leftmargin=\"0\">\n";
		echo "<table border=\"".$site_border_size."\" cellpadding=\"0\" cellspacing=\"0\" align=\"center\" bordercolor=\"#".$site_border_color."\""
			." bgcolor=\"#".$site_bgcolor_2."\" width=\"".$site_width."%\" style=\"border-collapse: collapse\" height=\"100%\">\n"
			."<tr><td width=\"100%\" valign=\"top\">\n";

		// ========================================================
		// TITLE BANNER : 3 bars and title header
		// ========================================================
		echo "<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" align=\"center\" width=\"".$contents_width."%\"><tr><td>";

		// FIRST BARS
		if ($bars_001 == 1) {
			$llnk = "<a href=\"\" class=\"lnk_light\"><b>".TXT_HOME."</b></a>";
			$rlnk = "<a href=\"\" class=\"lnk_light\"><b>".TXT_SUPPORT."</b></a>&nbsp;|&nbsp;"
				  ."<a href=\"mailto:\" class=\"lnk_light\"><b>".TXT_CONTACT."</b></a>&nbsp;";
			$page->bars_string($llnk, $rlnk, 22);
		} elseif ($bars_001 == 2) {
			$page->bars_index();
		} elseif ($bars_001 == 3) {
			$page->bars_search();
		} elseif ($bars_001 > 3) {
			$page->bars_user(22);
		}

		// TITLE HEADER
		echo "<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" width=\"100%\">"
			."<tr><td colspan=\"3\" bgcolor=\"#000000\" height=\"1\"></td></tr>\n"
			."<td width=\"300\" background=\"".$header_bglogo."\" bgcolor=\"#FFFFFF\" height=\"80\">&nbsp;</td>"
			."<td bgcolor=\"#FFFFFF\" height=\"80\">&nbsp;</td>"
			."<td width=\"400\" bgcolor=\"#FFFFFF\" height=\"80\"><img src=\"".$header_banner."\"></td></tr>\n</table>\n";

		// SECOND BARS
		if ($bars_002 == 1) {
			$llnk = "<a href=\"\" class=\"lnk_light\"><b>".TXT_HOME."</b></a>";
			$rlnk = "<a href=\"\" class=\"lnk_light\"><b>".TXT_SUPPORT."</b></a>&nbsp;|&nbsp;"
				  ."<a href=\"mailto:\" class=\"lnk_light\"><b>".TXT_CONTACT."</b></a>&nbsp;";
			$page->bars_string($llnk, $rlnk, 22);
		} elseif ($bars_002 == 2) {
			$page->bars_index();
		} elseif ($bars_002 == 3) {
			$page->bars_search();
		} elseif ($bars_002 > 3) {
			$page->bars_user(22);
		}

		// THIRD BARS
		if ($bars_003 == 1) {
			$llnk = "<a href=\"\" class=\"lnk_light\"><b>".TXT_HOME."</b></a>";
			$rlnk = "<a href=\"\" class=\"lnk_light\"><b>".TXT_SUPPORT."</b></a>&nbsp;|&nbsp;"
				  ."<a href=\"mailto:\" class=\"lnk_light\"><b>".TXT_CONTACT."</b></a>&nbsp;";
			$page->bars_string($llnk, $rlnk, 22);
		} elseif ($bars_003 == 2) {
			$page->bars_index();
		} elseif ($bars_003 == 3) {
			$page->bars_search();
		} elseif ($bars_003 > 3) {
			$page->bars_user(22);
		}
		// ========================================================
		// END OF TITLE BANNER
		// ========================================================

		// ========================================================
		// CONTENTS TABLE
		// ========================================================
		echo "<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" align=\"center\""
			." bgcolor=\"#".$site_bgcolor_2."\" width=\"100%\" style=\"border-collapse: collapse\">\n"
			."<tr><td valign=\"top\">\n";

		// ========================================================
		// Left Column
		if ($col_left_width > 0) {
			echo "\n</td><td width=\"".$panels_hspacing."\"><img src=\"pics\kernel\blank.gif\" width=\"".$panels_hspacing."\" height=\"1\"></td>";
			echo "\n<td valign=\"top\" width=\"".$col_left_width."\">";
			if ($panels_vspacing > 0) {echo "\n<table><tr><td height=\"".$panels_vspacing."\"></td></tr>\n</table>\n";}
			// Display the left panels
			global $current_colunm;
			$res = $db->sql_query("SELECT panel_addon, panel_title, panel_text, panel_type, posy FROM ".$db_prefix."_panels WHERE posx='1' AND posy>'0' ORDER BY posy");
			while(list($panel_addon, $panel_title, $panel_text, $panel_type, $posy) = $db->sql_fetchrow($res)) {		
				$current_colunm = 1;
				$this->build_panel($panel_type, $panel_title, $panel_text, $col_left_width, $panel_addon);
			}			
			echo "\n<td width=\"".$panels_hspacing."\"><img src=\"pics\kernel\blank.gif\" width=\"".$panels_hspacing."\" height=\"1\"></td>";
			echo "<td valign=\"top\">\n";
		}

		if ($panels_vspacing > 0) {echo "\n<table><tr><td height=\"".$panels_vspacing."\"></td></tr>\n</table>\n";}
	}

	function html_foot() {
		// ========================================================
		// Make the HTML page foot
		// ========================================================
		global $db, $db_prefix, $col_right_width, $panels_hspacing, $panels_vspacing;

		// ========================================================
		// Right Column
		if ($col_right_width > 0) {
			echo "\n</td><td width=\"".$panels_hspacing."\"><img src=\"pics\kernel\blank.gif\" width=\"".$panels_hspacing."\" height=\"1\"></td>";
			echo "\n<td  valign=\"top\" width=\"".$col_right_width."\">";
			if ($panels_vspacing > 0) {echo "\n<table><tr><td height=\"".$panels_vspacing."\"></td></tr>\n</table>\n";}
			// Display the right panels
			global $current_colunm;
			$res = $db->sql_query("SELECT panel_addon, panel_title, panel_text, panel_type, posy FROM ".$db_prefix."_panels WHERE posx>'1' AND posy>'0' ORDER BY posy");
			while(list($panel_addon, $panel_title, $panel_text, $panel_type, $posy) = $db->sql_fetchrow($res)) {
				$current_colunm = 2;
				$this->build_panel($panel_type, $panel_title, $panel_text, $col_right_width, $panel_addon);
			}
			echo "\n<td width=\"".$panels_hspacing."\"><img src=\"pics\kernel\blank.gif\" width=\"".$panels_hspacing."\" height=\"1\">";
		}

		// ========================================================
		// END OF THE PAGE
		echo "</td></tr></table>\n\n";

		echo "\n</td></tr></table>\n";

		echo "</td></tr><tr><td valign=\"bottom\">\n";
		please_respect_copyright();
		echo "\n</td></tr></table>\n\n";
		echo "</body>\n</html>";
	}

	function html_table_start($centred=false) {
		global $contents_width, $contents_bgcolor;
		global $theme, $theme_edge;

		if ($centred) {$compl = "center";} else {$compl = "left";}

		$tpath = "themes/borders/$theme_edge/";

		echo "\n<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" width=\"100%\"><tr>"
			."<td background=\"".$tpath.$theme->edge_pic[4][0]."\" width=\"".$theme->edge_size[1][0]."\" height=\"".$theme->edge_size[0][0]."\"></td>"
			."<td background=\"".$tpath.$theme->edge_pic[0][0]."\" height=\"".$theme->edge_size[0][0]."\"></td>"
			."<td background=\"".$tpath.$theme->edge_pic[5][0]."\" width=\"".$theme->edge_size[2][0]."\" height=\"".$theme->edge_size[0][0]."\"></td></tr><tr>";
		echo "<td background=\"".$tpath.$theme->edge_pic[1][0]."\" width=\"".$theme->edge_size[1][0]."\"><img src=\"".$tpath.$theme->edge_pic[1][0]."\" width=\"".$theme->edge_size[1][0]."\"></td>"
			."<td bgcolor=\"#".$contents_bgcolor."\" align=\"".$compl."\" valign=\"top\">\n";

		echo "<table align=\"".$compl."\" border=\"0\" cellpadding=\"0\" cellspacing=\"0\" width=\"100%\"><tr><td>";
	}

	function html_table_end() {
		global $panels_vspacing, $theme, $theme_edge;

		$tpath = "themes/borders/$theme_edge/";

		echo "</td></tr>\n</table>\n";

		echo "</td><td background=\"".$tpath.$theme->edge_pic[2][0]."\" width=\"".$theme->edge_size[2][0]."\"><img src=\"".$tpath.$theme->edge_pic[2][0]."\" width=\"".$theme->edge_size[2][0]."\"></td>";
		echo "</tr><tr>";
		echo "<td background=\"".$tpath.$theme->edge_pic[6][0]."\" width=\"".$theme->edge_size[1][0]."\" height=\"".$theme->edge_size[3][0]."\"></td>"
			."<td background=\"".$tpath.$theme->edge_pic[3][0]."\"></td>"
			."<td background=\"".$tpath.$theme->edge_pic[7][0]."\" width=\"".$theme->edge_size[2][0]."\" height=\"".$theme->edge_size[2][0]."\"></td>";

		echo "</tr></table>";

		if ($panels_vspacing > 0) {echo "\n<table><tr><td height=\"".$panels_vspacing."\"></td></tr>\n</table>\n";}
	}

	function build_panel($type, $title, $text, $width, $addon=0, $addonname="") {
		// ========================================================
		// BUILD A PANEL
		// ========================================================
		// $type      	: 0=HTML panel, >0=Add-On panel
		// $title     	: Panel Title
		// $text      	: Panel text. (The string can contain html code.)
		// $width     	: Panel width
		//
		// - Si $type est supérieur à 0, il faut afficher un Add-On dans le panneau.
		//   Il y a 2 façons de retrouver l'Add-On dans la base de données : Par index ou par nom.
		//   Si [$addonname] est vide, la recherche par index est utilisée. 
		//
		// [$addon]   	: Par index : Addon-On index in database.
		// [$addonname] : Par nom   : Nom du Add-on à afficher.
		// ========================================================
		global $db, $page, $db_prefix, $current_colunm, $language, $addon_right;
		global $user, $theme, $line_blocktitle, $panels_bgcolor, $panels_title_bgcolor, $panels_vspacing, $theme_edge;

		$tpath = "themes/borders/$theme_edge/";
		$p = $current_colunm;
		if ($p == 0) {
			$ww = "100%";
		} else {
			$p = 1;
			$ww = $width;
		}
		// Check the rights if it's an add-on
		$addon_right = 2;	// DEFAULT FORCED TEMP !!!!!!!!!		
		if ($type > 0) {
			if ($addonname != "") {
				// Search By Name (Get the index for rights checking) 
				$rp = $db->sql_query("SELECT addonid FROM ".$db_prefix."_addons WHERE addon_name LIKE '$addonname' LIMIT 1");
				list($addonid) = $db->sql_fetchrow($rp);
				$addon = $addonid;			
			}
			$addon_right = $user->addon_right($addon);
		}
		if ($addon_right > 0) {
			// BUILD HTML TITLE
			if ($theme->title_display[$p]) {
				if ($theme->title_bold[$p]) {$title = "<b>".$title."</b>";}
				$title = "<font color=\"".$theme->title_color[$p]."\">".$title."</font>";
			} else {
				$title = "";
			}
			// DISPLAY
			echo "\n<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" width=\"".$ww."\">";
			$titt = $theme->tbar_size[$p];
			if ($titt > 0) {
				// DISPLAY UPPER TITLE BAR
				echo "<tr><td colspan=\"3\" background=\"".$tpath.$theme->tbar_pic[$p]."\" bgcolor=\"#".$panels_title_bgcolor."\" height=\"".$titt."\">&nbsp;".$title;
				$titt = $theme->tbar_offset[$p];
				if ($titt > 0) {echo "</td></tr><tr><td colspan=\"3\" height=\"".$titt."\"></td></tr>";}
			} else {
				// NO UPPER TITLE BAR
				
			}
	
			echo "<tr><td width=\"".$theme->edge_size[1][$p]."\"><img src=\"".$tpath.$theme->edge_pic[4][$p]."\" height=\"".$theme->edge_size[0][$p]."\" width=\"".$theme->edge_size[1][$p]."\"></td>"
				."<td background=\"".$tpath.$theme->edge_pic[0][$p]."\" height=\"".$theme->edge_size[0][$p]."\" width=\"".$theme->edge_size[1][$p]."\"></td>"
				."<td width=\"".$theme->edge_size[2][$p]."\"><img src=\"".$tpath.$theme->edge_pic[5][$p]."\" height=\"".$theme->edge_size[0][$p]."\" width=\"".$theme->edge_size[2][$p]."\"></td></tr>";
			echo "<tr><td background=\"".$tpath.$theme->edge_pic[1][$p]."\" width=\"".$theme->edge_size[1][$p]."\"><img src=\"".$tpath.$theme->edge_pic[1][$p]."\" width=\"".$theme->edge_size[1][$p]."\"></td><td>\n";
			
			if ($type == 0) {
				// ========================================================
				// DISPLAY HTML CODE
				if ($text == "") {$text = "&nbsp;";}
				echo "<table border=\"0\" cellpadding=\"4\" cellspacing=\"0\" width=\"100%\"><tr><td bgcolor=\"#".$panels_bgcolor."\">".$text."</td></tr>\n</table>\n";
			} else {
				// ========================================================
				// DISPLAY ADD-ON
				echo "<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" width=\"100%\"><tr><td bgcolor=\"#".$panels_bgcolor."\">";
				// Get the Add-On from database
				if ($addonname == "") {
					// Search By Index
					$rp = $db->sql_query("SELECT addon_name FROM ".$db_prefix."_addons WHERE addonid='$addon' LIMIT 1");
					list($addon_name) = $db->sql_fetchrow($rp);
				} else {			
					// Search By Name
					$addon_name = $addonname;
				}
				// PUT ADD-ON CONTENTS IN PANEL
				if ($addon_name == "") {
					echo ERR_DB_CANTLOAD;
				} else {
					if (@file_exists("add-ons/".$addon_name."/locale/txt.$language.php")) {@require_once("add-ons/".$addon_name."/locale/txt.$language.php");}
					if ($current_colunm == 0) {
						@require_once("add-ons/".$addon_name."/body-center.php");
					} else {
						@require_once("add-ons/".$addon_name."/body-side.php");
					}
//if ($current_colunm == 0) {die("$current_colunm");}
				}
				echo "</td></tr></table>";
			}
			
			echo "</td><td width=\"".$theme->edge_size[2][$p]."\" background=\"".$tpath.$theme->edge_pic[2][$p]."\"><img src=\"".$tpath.$theme->edge_pic[2][$p]."\" width=\"".$theme->edge_size[2][$p]."\"></td></tr>";
			echo "<tr><td width=\"".$theme->edge_size[1][$p]."\"><img src=\"".$tpath.$theme->edge_pic[6][$p]."\" height=\"".$theme->edge_size[3][$p]."\" width=\"".$theme->edge_size[1][$p]."\"></td>"
				."<td background=\"".$tpath.$theme->edge_pic[3][$p]."\" height=\"".$theme->edge_size[3][$p]."\"></td>"
				."<td width=\"".$theme->edge_size[2][$p]."\"><img src=\"".$tpath.$theme->edge_pic[7][$p]."\" height=\"".$theme->edge_size[3][$p]."\" width=\"".$theme->edge_size[2][$p]."\">";
			echo "</td></tr>\n</table>\n\n";
			
			// Vertical spacing between panels
			if ($panels_vspacing > 0) {echo "\n<table><tr><td height=\"".$panels_vspacing."\"></td></tr>\n</table>\n";}
		}
	}

	function icon($pic_path, $text, $link, $maxsize="") {
		echo "<table width=\"".$maxsize."\">";
		echo "<tr><td align=\"center\"><a href=\"".$link."\"><img border=\"0\" src=\"".$pic_path."\" alt=\"".$text."\"></a></td></tr>";
		echo "<tr><td align=\"center\"><a href=\"".$link."\">".$text."</a></td></tr>";
		echo "</table>";
	}

	// ******************************************************************

	// ******************************************************************
	// * BARS FUNCTIONS                                                 *
	// ******************************************************************
	
	function bars_string($left_str, $right_str="", $height=18, $bgcolor="#000000", $color="#EEEEEE", $h_border=1) {
		// ========================================================
		// Make a toolbar
		// ========================================================
		echo "<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" width=\"100%\">\n"
			."<tr><td bgcolor=\"".$color."\" colspan=\"2\" height=\"".$h_border."\"></td></tr>";
		echo "<tr><td bgcolor=\"".$bgcolor."\" align=\"left\" height=\"".$height."\"><font color=\"".$color."\">".$left_str."</font></td>"
			."<td bgcolor=\"".$bgcolor."\" align=\"right\" height=\"".$height."\"><font color=\"".$color."\">".$right_str."</font></td></tr>";
		echo "<tr><td bgcolor=\"".$color."\" colspan=\"2\" height=\"".$h_border."\"></td></tr>\n</table>\n";
	}

	function bars_index($color="#EEEEEE", $h_border=1) {
		// ========================================================
		// Make an Index Bar
		// ========================================================
		echo "<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" width=\"100%\">\n"
			."<tr><td bgcolor=\"".$color."\" height=\"".$h_border."\"></td></tr>";

		echo "<tr><td bgcolor=\"".$color."\" height=\"".$h_border."\"></td></tr>\n</table>\n";			
	}

	function bars_search($color="#EEEEEE", $h_border=1) {
		// ========================================================
		// Make a Search Bar
		// ========================================================
		echo "<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" width=\"100%\">\n"
			."<tr><td bgcolor=\"".$color."\" height=\"".$h_border."\"></td></tr>";

		echo "<tr><td bgcolor=\"".$color."\" height=\"".$h_border."\"></td></tr>\n</table>\n";			
	}

	function bars_user($height=18, $bgcolor="#000000", $color="#EEEEEE", $h_border=1) {
		// ========================================================
		// Make a User Bar
		// ========================================================
		global $page, $user;
		
		if ($user->name == "") {
		    $llnk = "<table><tr><td><a href=\"index.php?cmd=login\" class=\"lnk_light\">"
				."<img src=\"kernel/pics/ic-16/plug1.gif\" alt=\"".TXT_CONNECT."\" border=\"0\" width=\"16\" height=\"16\"></a></td>"
				."<td valign=\"center\"><a href=\"index.php?cmd=login\" class=\"lnk_light\"><b>".TXT_CONNECT."</b></a></td></tr></table>";
			$rlnk = "";
		} else {
			$llnk = "<table><tr><td><a href=\"index.php?cmd=logout\" class=\"lnk_light\">"
				."<img src=\"kernel/pics/ic-16/plug2.gif\" alt=\"".TXT_DISCONNECT."\" border=\"0\" width=\"16\" height=\"16\"></a></td>"
				."<td valign=\"center\"><a href=\"index.php?cmd=logout\" class=\"lnk_light\"><b>".TXT_DISCONNECT."</b></a></td></tr></table>";
			$rlnk = "<table><tr><td><a href=\"index.php?cmd=home\" class=\"lnk_light\">"
				."<img src=\"kernel/pics/ic-16/user1.gif\" alt=\"".TXT_LOG_USER."\" border=\"0\" width=\"16\" height=\"16\"></a></td>"
				."<td valign=\"center\"><a href=\"index.php?cmd=home\" class=\"lnk_light\"><b>".$user->name."</b></a></td>"
				."<td valign=\"center\">&nbsp;|&nbsp;</td><td><a href=\"index.php?cmd=admin\" class=\"lnk_light\">"
				."<img src=\"kernel/pics/ic-16/keys1.gif\" alt=\"".TXT_ADMINISTRATION."\" border=\"0\" width=\"16\" height=\"16\"></a></td>"
				."<td valign=\"center\"><a href=\"index.php?cmd=admin\" class=\"lnk_light\"><b>".TXT_ADMINISTRATION."</b></a></td>"
				."</td></tr></table>";
		}
		$page->bars_string($llnk, $rlnk, 22);
	}	
	
	// ******************************************************************

	// ******************************************************************
	// * TITLES FUNCTIONS                                               *
	// ******************************************************************

	function title_page($txt_title, $style=1) {
		switch($style) {
			// No back picture		
			case 0:
				$sfont = "<font class=\"title_big_dark\">";
				break;
			// DEFAULT : With back picture...
			case 1:
				$sfont = "<font class=\"title_page\">";
				break;
			// Back picture and start/end table
			case 2:
				$sfont = "<font class=\"title_page\">";
				$this->html_table_start();
				break;
		}

		echo "<center>".$sfont."<br>".$txt_title."</font></center>";

		if ($style == 2) {
			$this->html_table_end();
		}
	}

	function title_chapter($txt_title, $style=1) {
		switch($style) {
			// No back picture
			case 0:
				$sfont = "<font class=\"title_big_dark\">";
				break;
			// DEFAULT : With back picture...
			case 1:
				$sfont = "<font class=\"title_chapter\">";
				break;
			// Back picture with info icon
			case 2:
				$sfont = "<font class=\"title_chapter_inf\">";
				break;
			// Bback picture with config icon
			case 3:
				$sfont = "<font class=\"title_chapter_cfg\">";
				break;
			// Back picture with keys icon
			case 4:
				$sfont = "<font class=\"title_chapter_key\">";
				break;
			// Back picture with warning icon
			case 5:
				$sfont = "<font class=\"title_chapter_pb\">";
				break;
		}

		echo "<table><tr><td align=\"left\">".$sfont."<br>&nbsp;&nbsp;".$txt_title."</font></td></tr></table>";
	}

	function title_content($txt_title, $picture='', $style=0) {
		global $titles_contents_light, $titles_contents_dark;

		switch($style) {
			// DEFAULT : With back picture...
			case 0:
				$button = "";
				break;
			// With Back Button
			case 1:
				$button = "<form action=\"javascript:history.go(-1)\" method=\"post\"><input type=\"submit\" value=\"<\"></form>";
				break;			
		}

		echo "\n<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" bgcolor=\"".$titles_contents_light."\" width=\"100%\">"
			."<tr><td align=\"left\" valign=\"top\" bgcolor=\"".$titles_contents_dark."\">".$button;
		echo "</td><td align=\"left\" valign=\"top\" width=\"95%\"><font class=\"title_contents\">&nbsp;".$txt_title."</font></td>"
			."<td width=\"48\"><img src=\"".$picture."\"></td></tr></table>\n";				
	}

	// ******************************************************************

	// ******************************************************************
	// * FORMS FUNCTIONS                                                *
	// ******************************************************************

	function form_login($code, $cmd_back='', $txt='') {
		global $secur_code, $secur_display, $email_support;

		$this->html_table_start(true);
		echo "<br><center>";
		$this->title_chapter(TXT_IDENTIFICATION, 4);
		if ($txt) {echo "<font class=\"title_dark\"><br>".TXT_LOG_TXTACCESS."</font>";}
		echo "<br><form action=\"index.php\" method=\"post\"><input type=\"hidden\" name=\"cmd\" value=\"login\">"
			."<input type=\"hidden\" name=\"back\" value=\"".$cmd_back."\"><input type=\"hidden\" name=\"act\" value=\"login\">"
			."<table border=\"0\">"
			."<tr><td>".TXT_LOG_YOURUSERNAME."</td><td><input type=\"text\" name=\"usr_name\" size=\"30\" maxlength=\"24\"></td></tr>"
			."<tr><td>".TXT_LOG_YOURUSERPASS."</td><td><input type=\"password\" name=\"usr_pass\" maxlength=\"12\" size=\"15\"></td></tr>";
		if ($secur_code > 0) {
      echo "<tr><td>".TXT_LOG_RETYPESECCODE."</td><td><input type=\"text\" name=\"sec\" maxlength=\"6\" size=\"15\"></td></tr>";
  		echo "<tr><td>".TXT_LOG_SECURCODE."</td>";
  		if ($secur_display == 0) {
  			echo "<td><font class=\"title_dark\">".$code."</font></td>";
  		} elseif ($secur_display == 1) {
  			echo "<td><img src=\"kernel/img-maker.php?op=jpgcode&amp;txt=$code\" border=\"0\" alt=\"".TXT_LOG_SECURCODE."\" width=\"106\" height=\"19\"></td>";
  		} elseif ($secur_display == 2) {
  			echo "<td><img src=\"kernel/img-maker.php?op=pngcode&amp;txt=$code\" border=\"0\" alt=\"".TXT_LOG_SECURCODE."\" width=\"106\" height=\"19\"></td>";
  		}
    }
		echo "<tr><td align=\"center\" colspan=\"2\"><br><input type=\"submit\" value=\"".TXT_OK."\" style=\"width: 120\"></td></tr>"
			."</table>";
		echo "<br></form>";

		$this->title_chapter(TXT_SUPPORT, 2);
		echo "<br>".TXT_SUPP_WHANTSUB."<br>".TXT_SUPP_PASWORDLOST."<br><a href=\"mailto:".$email_support."\"><b>".TXT_SUPP_CONTACT."</b></a><br><br>";

		$this->html_table_end();
		echo "</center>";
	}

	function form_createlogin($code, $txt='') {
		$this->html_table_start(true);
		echo "<br><center>";
		$this->title_chapter(TXT_LOG_ADDMASTER, 4);
		if ($txt) {echo "<center><font class=\"alert\"><br>".$txt."<br><br></font></center>";}
		echo "<form action=\"index.php\" method=\"post\"><input type=\"hidden\" name=\"cmd\" value=\"create_master\">\n"
			."<input type=\"hidden\" name=\"sec\" value=\"".$code."\"><table border=\"0\">\n"
			."<tr><td>".TXT_LOG_YOURUSERNAME."</td><td><input type=\"text\" name=\"account_name\" size=\"30\" maxlength=\"24\"></td></tr>"
			."<tr><td>".TXT_LOG_YOURUSERPASS."</td><td><input type=\"password\" name=\"pass1\" maxlength=\"12\" size=\"15\"></td></tr>"
			."<tr><td>".TXT_LOG_CONFIRMPASS."</td><td><input type=\"password\" name=\"pass2\" maxlength=\"12\" size=\"15\"></td></tr>"
			."<tr><td align=\"center\" colspan=\"2\"><br><input type=\"submit\" value=\"".TXT_CREATE."\" style=\"width: 120\"></td></tr>"
			."</table>\n</form><br><br>";
		$this->html_table_end();
		echo "</center>";
	}

	// ******************************************************************

}
?>
