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
// | index.php : V 0.0.4
// ==================================================================



// ========================================================
// Init the variables
// ========================================================
//
$timezone = date("Z");          // Time in second of the time shift
$nbr_barstype = 4;              // A new bars type can be add
// ========================================================



// ========================================================
// Checking for necessary files
// ========================================================
// kernel/cfg.php         	: Contains all system variables.
// kernel/db/[$db_type].php	: Contains the class database fonctions in the [$db_type] format.
//
$err_desc = "<b>Error :</b> One or more files was missing or the <b>cfg.php</b> file contain some bad variables...";
if (@file_exists("kernel/cfg/cfg.php")) {require_once("kernel/cfg/cfg.php");} else {die($err_desc);}
if (@file_exists("kernel/db/$db_type.php")) {require_once("kernel/db/".strtolower($db_type).".php");} else {die($err_desc);}
// ========================================================



// ========================================================
// Creating the database class and get the config values
// ========================================================
// Connect to the database with the class modules of phpBB Group...
//
$err_desc = "<b>Error :</b> Can't connect to the database... Check if the database is valid and the <b>cfg.php</b> file...";
$db = new sql_db($db_url, $db_user, $db_password, $db_name);
if(!$db) {die($err_desc);}
$vrq = "site_title, site_author, site_copyright, site_description, site_keywords, site_logo, site_appearance, site_bgcolor_1, site_bgcolor_2, "
	  ."site_border_size, site_width, language, col_left_width, col_right_width, contents_bgcolor, "
	  ."contents_width, email_support, header_banner, header_bglogo, panels_bgcolor, panels_hspacing, panels_vspacing, "
	  ."panels_title_bgcolor, secur_code, tab_bgcolor, tab_title_bgcolor, tab_title_bgcolor2, theme_edge, theme_style, "
	  ."bars_001, bars_002, bars_003";
$res = $db->sql_query("SELECT ".$vrq." FROM ".$db_prefix."_cfg LIMIT 1");
list($site_title,$site_author,$site_copyright,$site_description,$site_keywords,$site_logo,$site_appearance,$site_bgcolor_1,$site_bgcolor_2,$site_border_size,$site_width, $language,$col_left_width,$col_right_width,$contents_bgcolor,$contents_width,$email_support,$header_banner, $header_bglogo,$panels_bgcolor,$panels_hspacing,$panels_vspacing,$panels_title_bgcolor,$secur_code,$tab_bgcolor,$tab_title_bgcolor,$tab_title_bgcolor2, $theme_edge,$theme_style,$bars_001,$bars_002,$bars_003) = $db->sql_fetchrow($res);
// Check and correct some values...
if ($col_right_width < 0) {$col_right_width = 0;} elseif ($col_right_width > 300) {$col_right_width = 300;}
if ($col_left_width < 0) {$col_left_width = 0;} elseif ($col_left_width > 300) {$col_left_width = 300;}
if ($contents_width < 0) {$contents_width = 0;} elseif ($contents_width > 100) {$contents_width = 100;}
if ($site_width < 20) {$site_width = 20;} elseif ($site_width > 100) {$site_width = 100;}
if ($bars_001 < 0) {$bars_001 = 0;} elseif ($bars_001 > $nbr_barstype) {$bars_001 = $nbr_barstype;}
if ($bars_002 < 0) {$bars_002 = 0;} elseif ($bars_002 > $nbr_barstype) {$bars_002 = $nbr_barstype;}
if ($bars_003 < 0) {$bars_003 = 0;} elseif ($bars_003 > $nbr_barstype) {$bars_003 = $nbr_barstype;}
// ========================================================



// ========================================================
// Detremine the best level for security code generation in the current server
// ========================================================
//
if (extension_loaded("gd")) {
	if (function_exists("imagejpeg")) {
		$secur_display = 1;
	} elseif (function_exists("imagepng")) {
		$secur_display = 2;
	} else {
		$secur_display = 0;	
	}
} else {
	$secur_display = 0;
}
// ========================================================



// ========================================================
// Checking the next necessary files
// ========================================================
// kernel/locale/err.[$language].php   : Contains all error messages in the [$lang_main] language.
// kernel/locale/text.[$language].php  : Contains all text used by UrPCMS.
// kernel/urkernel.php                 : Contains all primary functions.
//
$language = strtolower($language);
$err_desc = "<b>Error :</b> One or more files was missing or <b>database</b> contain some bad data in table <b><i>$db_prefix</i>_cfg</b>...";
if (@file_exists("kernel/locale/err.$language.php")) {require_once("kernel/locale/err.$language.php");} else {die("File Not Found : kernel/locale/txt.$language.php");}
if (@file_exists("kernel/locale/txt.$language.php")) {require_once("kernel/locale/txt.$language.php");} else {die(ERR_FILENOTFOUND." : kernel/locale/txt.$language.php");}
if (@file_exists("kernel/urkernel.php")) {require_once("kernel/urkernel.php");} else {die(ERR_FILENOTFOUND." : kernel/urkernel.php");}
if (@file_exists("kernel/adm.php")) {} else {die(ERR_FILENOTFOUND." : kernel/adm.php");}
if (@file_exists("themes/borders/$theme_edge/cfg.php")) {include("themes/borders/$theme_edge/cfg.php");}
if (@file_exists("themes/texts/$theme_style/cfg.php")) {include("themes/texts/$theme_style/cfg.php");}
// ========================================================



// ========================================================
// Declar all Objects
// ========================================================
//
$kernel = new ur_kernel();
$user = new ur_user();
$page = new html_page();
$theme = new ur_theme();
// ========================================================



// ========================================================
// TAKE THE ADDONS LIST FROM DATABASE
// ========================================================
// If the Add-On is not present in [add-ons] directory, it will be ignored.
// The data base update to remove the orphan entries, is only called when
// the add-ons administartion menu is loaded.
//
$res = $db->sql_query("SELECT addonid, addon_name, groups_view, groups_use, groups_admin FROM ".$db_prefix."_addons ORDER BY addon_name");	
while(list($addonid, $addon_name, $groups_view, $groups_use, $groups_admin) = $db->sql_fetchrow($res)) {
	if (@file_exists("add-ons/".$addon_name)) {
		$addon_list_id[] = $addonid;
		$addon_list_name[] = $addon_name;
		$addon_list_groups[][0] = $groups_view;
		$addon_list_groups[][1] = $groups_use;
		$addon_list_groups[][2] = $groups_admin;
	} else {
		$addon_remove_id[] = $addonid;
	}
}
// ========================================================



// ========================================================
// First, we look the Master account presence
// ========================================================
//
if ($cmd != "create_master") {
	$res = $db->sql_query("SELECT user_name FROM ".$db_prefix."_".$acc_prefix."_users WHERE user_groups LIKE '%master%'");	
	list($user_name) = $db->sql_fetchrow($res);
	if (!$user_name) {
		// NO MASTER ACCOUNT FOUND !!!
		$code = make_code();
		$ccode = encrypt_data($code);
		setcookie("session_code", $ccode, time() + $timezone + 3600);
		$page->html_header(TXT_LOG_ADDMASTER);
		$page->title_page(ERR_NOMASTERACC, 2);
		$page->form_createlogin($code, ERR_MASTERIMP);
		$page->html_foot();
		die();
	} 
}
// ========================================================



// ========================================================
// SWITCH OF [cmd] VARIABLE
// ========================================================
// 
switch ($cmd) {

	// ========================================================
	// Master account creation
	// ========================================================
	// 
	case "create_master":
	$user->create_account("Master");
	break;

	// ========================================================
	// User Login
	// ========================================================
	//
	case "login":
	if ($act == "login") {
		$user->login();
	} else {
		// CONNECTING FORM...
		//
		$code = make_code();
		$ccode = encrypt_data($code);
		setcookie("session_code", $ccode, time() + $timezone + 3600);
		$page->html_header(TXT_LOG_LOGINTITLE);
		$page->title_page(TXT_LOG_LOGINTITLE, 2);
		$page->form_login($code, "", TXT_LOG_TXTACCESS);
		$page->html_foot();
		die();	    
	}
	break;

	case "logout":
	$user->logout();
	break;

	// ========================================================
	// Administration Module
	// ========================================================
	//
	case "admin":
		// ADMIN PAGE
		//
		include("kernel/adm.php");
		$adm = new ur_adm();

		if ($menu) {
			switch ($act) {
				default:
				$adm->form($menu);
				break;

				case "add":
				$adm->add($menu);
				break;

				case "modify":
				$adm->modify($menu);
				break;

				case "delete":
				$adm->del($menu, $id);
				break;

				case "edit":
				$adm->edit($menu, $id);
				break;

				case "movedown":
				$adm->panels_move($obj, $posx, $ref, 1);
				break;

				case "moveup":
				$adm->panels_move($obj, $posx, $ref, -1);
				break;
			}
		} else {
			$page->html_header(ADM_TITLE_ADMIN);
			$page->title_page(ADM_TITLE_ADMIN, 2);
			$adm->main_menu();
		}

		$page->html_foot();
	break;
	
	// ========================================================
	// DISPLAY ADD-ONS
	// ========================================================
	//	
	case "addon";
		$page->html_header($site_title);
		$current_colunm = 0;
		
		if ($name) {$page->build_panel(1, "", "", 0, 0, $name);}
		$page->html_foot();
	break;
	
	// ========================================================
	// Other value of [cmd]
	// ========================================================
	//
	default:
	$page->html_header($site_title);
	// Display the center panels
	$res = $db->sql_query("SELECT panel_title, panel_text, posy FROM ".$db_prefix."_panels WHERE posx<'1' AND posy>'0' ORDER BY posy");
	while(list($panel_title, $panel_text, $posy) = $db->sql_fetchrow($result)) {

	}
	$page->html_foot();
	break;

}	// END OF SWITCH

?>
