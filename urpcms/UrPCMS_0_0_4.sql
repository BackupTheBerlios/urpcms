# phpMyAdmin SQL Dump
# version 2.5.3
# http://www.phpmyadmin.net
#
# Serveur: localhost
# Généré le : Lundi 16 Février 2004 à 23:20
# Version du serveur: 4.0.15
# Version de PHP: 4.3.3
# 
# Base de données: `urpcms`
# 

# --------------------------------------------------------

#
# Structure de la table `urpcms_account_groups`
#

CREATE TABLE `urpcms_account_groups` (
  `groupid` int(11) NOT NULL auto_increment,
  `group_name` varchar(32) NOT NULL default '',
  `group_desc` varchar(128) NOT NULL default '',
  PRIMARY KEY  (`groupid`),
  UNIQUE KEY `grp_name` (`group_name`),
  KEY `grpid` (`groupid`)
) TYPE=MyISAM;

#
# Contenu de la table `urpcms_account_groups`
#


# --------------------------------------------------------

#
# Structure de la table `urpcms_account_users`
#

CREATE TABLE `urpcms_account_users` (
  `userid` int(11) NOT NULL auto_increment,
  `user_name` varchar(24) NOT NULL default '',
  `user_pass` varchar(255) NOT NULL default '',
  `user_email` varchar(128) NOT NULL default '',
  `user_url` varchar(255) NOT NULL default '',
  `user_groups` set('Master','All') NOT NULL default '',
  `user_lastlog` varchar(16) NOT NULL default '',
  PRIMARY KEY  (`userid`),
  UNIQUE KEY `user_name` (`user_name`),
  KEY `userid` (`userid`)
) TYPE=MyISAM;

#
# Contenu de la table `urpcms_account_users`
#


# --------------------------------------------------------

#
# Structure de la table `urpcms_addons`
#

CREATE TABLE `urpcms_addons` (
  `addonid` int(11) NOT NULL auto_increment,
  `addon_name` varchar(255) NOT NULL default '',
  `groups_admin` set('Master','All') NOT NULL default '',
  `groups_use` set('Master','All') NOT NULL default '',
  `groups_view` set('Master','All') NOT NULL default '',
  PRIMARY KEY  (`addonid`),
  KEY `accpid` (`addonid`)
) TYPE=MyISAM;

#
# Contenu de la table `urpcms_addons`
#


# --------------------------------------------------------

#
# Structure de la table `urpcms_admins`
#

CREATE TABLE `urpcms_admins` (
  `adm_function` varchar(8) NOT NULL default '',
  `adm_list` set('Master','All') NOT NULL default ''
) TYPE=MyISAM;

#
# Contenu de la table `urpcms_admins`
#

INSERT INTO `urpcms_admins` VALUES ('addons', '');
INSERT INTO `urpcms_admins` VALUES ('aspect', '');
INSERT INTO `urpcms_admins` VALUES ('colors', '');
INSERT INTO `urpcms_admins` VALUES ('groups', '');
INSERT INTO `urpcms_admins` VALUES ('index', '');
INSERT INTO `urpcms_admins` VALUES ('panels', '');
INSERT INTO `urpcms_admins` VALUES ('security', '');
INSERT INTO `urpcms_admins` VALUES ('users', '');

# --------------------------------------------------------

#
# Structure de la table `urpcms_cfg`
#

CREATE TABLE `urpcms_cfg` (
  `site_title` varchar(255) NOT NULL default '',
  `site_author` varchar(255) NOT NULL default '',
  `site_copyright` varchar(255) NOT NULL default '',
  `site_description` varchar(255) NOT NULL default '',
  `site_keywords` varchar(255) NOT NULL default '',
  `site_logo` varchar(255) NOT NULL default '',
  `site_appearance` int(1) NOT NULL default '2',
  `site_bgcolor_1` varchar(6) NOT NULL default '000000',
  `site_bgcolor_2` varchar(6) NOT NULL default '777779',
  `site_txtcolor` varchar(6) NOT NULL default '000000',
  `site_border_size` int(1) NOT NULL default '0',
  `site_width` int(1) NOT NULL default '100',
  `language` char(2) NOT NULL default 'fr',
  `col_left_width` int(1) NOT NULL default '180',
  `col_right_width` int(1) NOT NULL default '180',
  `contents_bgcolor` varchar(6) NOT NULL default 'FFFFFF',
  `contents_width` int(1) NOT NULL default '100',
  `email_support` varchar(128) NOT NULL default '',
  `header_banner` varchar(255) NOT NULL default 'kernel/pics/banner.jpg',
  `header_bglogo` varchar(255) NOT NULL default 'kernel/pics/bglogo.jpg',
  `panels_bgcolor` varchar(6) NOT NULL default 'FFFFFF',
  `panels_hspacing` int(1) NOT NULL default '5',
  `panels_vspacing` int(1) NOT NULL default '5',
  `panels_title_bgcolor` varchar(6) NOT NULL default '777779',
  `secur_code` int(1) NOT NULL default '1',
  `tab_bgcolor` varchar(6) NOT NULL default 'FFFFFF',
  `tab_title_bgcolor` varchar(6) NOT NULL default 'A9A9BE',
  `tab_title_bgcolor2` varchar(6) NOT NULL default 'EEEEE2',
  `theme_edge` varchar(128) NOT NULL default '_default',
  `theme_style` varchar(128) NOT NULL default '_default.css',
  `theme_titles` varchar(128) NOT NULL default '_default',
  `line_blocktitle` int(1) NOT NULL default '0',
  `bars_001` int(1) NOT NULL default '1',
  `bars_002` int(1) NOT NULL default '4',
  `bars_003` int(1) NOT NULL default '0'
) TYPE=MyISAM;

#
# Contenu de la table `urpcms_cfg`
#

INSERT INTO `urpcms_cfg` VALUES ('', '', '', '', '', '', 2, '000000', '777779', '000000', 0, 100, 'fr', 180, 180, 'FFFFFF', 100, '', 'kernel/pics/banner.jpg', 'kernel/pics/bglogo.jpg', 'FFFFFF', 5, 5, '777779', 1, 'FFFFFF', 'A9A9BE', 'EEEEE2', '_default', '_default', '_default', 0, 1, 4, 0);

# --------------------------------------------------------

#
# Structure de la table `urpcms_panels`
#

CREATE TABLE `urpcms_panels` (
  `panelid` int(11) NOT NULL auto_increment,
  `panel_addon` int(11) NOT NULL default '0',
  `panel_title` varchar(64) NOT NULL default '',
  `panel_text` text NOT NULL,
  `panel_type` int(1) NOT NULL default '0',
  `posx` int(1) NOT NULL default '0',
  `posy` int(1) NOT NULL default '0',
  PRIMARY KEY  (`panelid`),
  KEY `panelid` (`panelid`)
) TYPE=MyISAM;

#
# Contenu de la table `urpcms_panels`
#

    
