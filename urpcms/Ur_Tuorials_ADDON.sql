# phpMyAdmin MySQL-Dump
# version 2.2.6
# http://phpwizard.net/phpMyAdmin/
# http://www.phpmyadmin.net/ (download page)
#
# Serveur: localhost
# Généré le : Mercredi 21 Janvier 2004 à 23:03
# Version du serveur: 3.23.49
# Version de PHP: 4.2.0
# Base de données: `urcms`
# --------------------------------------------------------

#
# Structure de la table `urpcms_addon_urtutorials`
#

CREATE TABLE urpcms_addon_urtutorials (
  tutid int(11) NOT NULL auto_increment,
  tut_title varchar(64) NOT NULL default '',
  PRIMARY KEY  (tutid),
  KEY tutid (tutid)
) TYPE=MyISAM;
