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
// | adm.[$langue].php : V 0.0.2
// ==================================================================

define("ADM_TITLE_ADMIN","Administration du Site");
define("ADM_TITLE_ADMINOF","Administration de ");
define("ADM_TITLE_ADDGROUP","Ajouter un Groupe");
define("ADM_TITLE_ADDONS","Gestion des Add-Ons");
define("ADM_TITLE_ADDONRIGHT","Droits du Add-On");
define("ADM_TITLE_ADDPANEL","Ajouter un Panneau");
define("ADM_TITLE_ADDUSER","Ajouter un Utilisateur");
define("ADM_TITLE_ASPECT","Aspect du Site");
define("ADM_TITLE_EDITGROUP","Editer un Groupe");
define("ADM_TITLE_EDITPANEL","Editer un Panneau");
define("ADM_TITLE_EDITUSER","Editer un Utilisateur");
define("ADM_TITLE_GROUPS","Gestion des Groupes");
define("ADM_TITLE_GROUPRIGHT","Droits du Groupe");
define("ADM_TITLE_INDEX","Index du Site");
define("ADM_TITLE_COLORS","Couleurs du Site");
define("ADM_TITLE_PANELS","Gestion des Panneaux");
define("ADM_TITLE_USERS","Gestion des Utilisateurs");
define("ADM_TITLE_SECURITY","Gestion de la s�curit�");
//ADD-ONS
define("ADM_ADDONS_COLUMN1","Taille de la Colonne de Gauche");
define("ADM_ADDONS_NAME","Nom du Add-on");
//ASPECT
define("ADM_ASPECT_COLUMN1","Taille de la Colonne de Gauche");
define("ADM_ASPECT_COLUMN2","Taille de la Colonne de Droite");
define("ADM_ASPECT_COLUMN3","Espacement Horizontal");
define("ADM_ASPECT_COLUMNCOM","0=Colonne Cach�e");
define("ADM_ASPECT_WIDTH","Largeur G�n�rale");
define("ADM_ASPECT_CWIDTH","Largeur du Contenu");
define("ADM_ASPECT_HWIDTH","Largeur de l'En-t�te");
define("ADM_ASPECT_VSPACE","Espacement vertical");
define("ADM_ASPECT_BUP","Barre : Au Dessus de l'En-t�te");
define("ADM_ASPECT_BDOWN","Barre : Au Dessous de l'En-t�te");
define("ADM_ASPECT_BMOREDOWN","Barre : Plus En Dessous");
define("ADM_ASPECT_LOGO","Logo du Site");
define("ADM_ASPECT_BGLOGO","Fond du Logo");
define("ADM_ASPECT_THGFXTITLES","Th�me Graphique : Titres");
define("ADM_ASPECT_THGFXCONTENTS","Th�me Graphique : Bords du Contenu");
define("ADM_ASPECT_THGFXPANELS","Th�me Graphique : Bords des Panneaux");
define("ADM_ASPECT_THSTYLE","Th�me Graphique : Feuille de Styles");
define("ADM_ASPECT_BANNHEIGHT","Hauteur de l'En-t�te");
define("ADM_ASPECT_RIGHTBANNER","Banni�re de Droite");
define("ADM_ASPECT_ERR_BWIDTH","La taille des colonnes doit �tre comprise entre 0 et 300 pixels");
define("ADM_ASPECT_ERR_CWIDTH","La largeur du contenu doit �tre comprise entre 20% et 100%");
define("ADM_ASPECT_ERR_SWIDTH","La largeur du site doit �tre comprise entre 20% et 100%");
define("ADM_ASPECT_ERR_SPACING","L'espacement doit �tre compris entre 0 et 100");
define("ADM_ASPECT_ERR_BLOCK","Le th�me de bordures indiqu� n'existe pas...");
define("ADM_ASPECT_ERR_STYLE","La feuille de style indiqu�e n'existe pas...");
// COLORS
define("ADM_COLORS_TITLE1","Fonds du Site");
define("ADM_COLORS_TITLE2","Fonds des ");
define("ADM_COLORS_BGCOLSITE","Fond du Site");
define("ADM_COLORS_BGCOLSECOND","Second Fond");
define("ADM_COLORS_BGCOLTITLEPANEL","Fond du Titre des Panneaux");
define("ADM_COLORS_BGCOLPANEL","Fond des Panneaux");
define("ADM_COLORS_BGCOLCONTENTS","Fond du Contenu");
define("ADM_COLORS_BGCOLTABLE1","Fond des Tableaux");
define("ADM_COLORS_BGCOLTITLETABLE","Fond des Titres Principaux des Tableaux");
define("ADM_COLORS_BGCOLTITLETABLE2","Fond des Titres Secondaires des Tableaux");
// GROUPS
define("ADM_GROUPS_NAME","Nom du Groupe");
define("ADM_GROUPS_MEMBERS","Liste des Membres");
define("ADM_GROUPS_DESC","Description du Groupe");
define("ADM_GROUPS_ERR_UPDTRIGHT","Au moins un des menus, n'a pas �t� mis � jour au niveau des droits.");
define("ADM_GROUPS_CAUTION1","Attention : Si vous donnez des droits d'administration � ce groupe, tous ses membres auront acc�s aux menus permettant de configurer le site pour tous les utilisateurs.");
define("ADM_GROUPS_CAUTION2","N'h�sitez pas � cr�er un groupe sp�cifique pour accorder de tels privil�ges. La modification de certains param�tres pourrait entra�ner de graves probl�mes.");
// PANELS
define("ADM_PANELS_LPANELS","Panneaux Gauches");
define("ADM_PANELS_CPANELS","Panneaux Centr�s");
define("ADM_PANELS_RPANELS","Panneaux Droits");
define("ADM_PANELS_TITLE","Titre du Panneau");
define("ADM_PANELS_BODY","Corps du Panneau");
define("ADM_PANELS_POSX","Position Horizontale");
define("ADM_PANELS_TYPE1","Afficher le code HTML suivant...");
define("ADM_PANELS_TYPE2","Afficher le Add-On suivant...");
define("ADM_PANELS_ERR_POSX","Position Horizontale Incorrecte");
// SECURITY
define("ADM_SECUR_USECODE","Utiliser un code de s�curit� au login");
// USERS
define("ADM_USER_MEMBEROF","Membre de...");
define("ADM_USERS_NAME","Nom de l'Utilisateur");
define("ADM_USERS_EMAIL","Adresse E-Mail");
define("ADM_USERS_URL","Page Personnelle");
define("ADM_USERS_PASS","Mot de Passe");
define("ADM_USERS_ERR_DENIED","Vous n'�tes pas autoris� � cr�er un compte...");
// QUESTIONS
define("ADM_Q_REALYDEL","Voulez-vous Vraiment Effacer cet El�ment ?");
// DESCRIPTIONS
define("ADM_DESC_MASTER","Compte du Propri�taire. (Tous les Droits)");
define("ADM_DESC_GUEST","Compte par d�faut. (Tous le Monde)");
// GENERAL ERROR
define("ADM_ERR_ADDONCOMNOTEXIST","Cette commande n'existe pas pour cet Add-On");
define("ADM_ERR_NOEMPTYNAME","Le nom ne peut pas �tre vide...");

?>
