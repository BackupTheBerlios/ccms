<? /* $id: index.fr.php,v 1.3 2002/09/25 10:54:24 apa exp $
Copyright (C) 2001, 2002, Makina Corpus, http://makinacorpus.org
This file is a componenet of CCMS <http://makinacorpus.org/index.php/ccms>
Created and maintained by mose (mose@makinacorpus.org)
Released under GPL version 2 or later, see LICENSE file
or http://www.gnu.org/copyleft/gpl.html
*/

$msg[1] = "Veuillez compléter les champs suivants afin d'initialiser votre installation";
$msg[2] = "Merci de compléter les champs suivants, relatifs à l'authentification du back-office.";

$no_cfgrights = "Le serveur web n'a pas les droits d'écriture dans le répertoire de configuration";
$no_rights = "Le serveur web n'a pas les droits d'écriture dans le répertoire.";

$tpath = "Le répertoire";
$no_path = "n'existe pas, veuillez le créer ou indiquer un répertoire existant";
$already = "existe déjà.  Merci de choisir un autre répertoire / nom de CCMS.";

$err_ip = "Cette adresse I.P. n'est pas valide.";
$err_name = "Veuillez indiquer un nom de CCMS valide.";
$err_domain = "Veuillez indiquer un nom de domaine valide.";
$err_lang = "Veuillez indiquer une définition de langues valide.";
$err_doms = "Veuillez indiquer une définition de domaines valide.";
$err_admlog = "Veuillez indiquer un login d'administration valide";
$err_admpass = "Veuillez indiquer un mot de passe d'administration valide.";
$err_pass = "Les 2 mots de passe d'administration ne correspondent pas.";
$err_tpass = "Les 2 mots de passe de test ne correspondent pas.";
$err_sql = "Veuillez indiquer un serveur MySQL valide";
$err_sqllog = "Veuilles indiquer un login Mysql valide.";
$err_sqlpass = "Veuillez indiquer un mot de passe MySQL valide.";
$err_sqlchk =  "Les 2 mots de passe MySQL ne correspondent pas.";
$err_ldap = "Veuillez indiquer un serveur LDAP valide";
$err_ldport = "Veuillez indiquer un port LDAP valide.";
$err_ldbase = "Veuillez indiquer un BaseBind LDAP valide.";
$err_ldatt = "Veuillez indiquer un attribut LDAP valide.";
$err_ldgbase = "Veuillez indiquer un GroupBase LDAP valide";
$err_ldgrp = "Veuillez indiquer un Group LDAP valide";
$err_ldbdn = "Veuillez indiquer un BaseDN LDAP valide.";
$err_nodb = "Connection à la base de données impossible:";

$ap_inc1 = "Vous devez inclure les fichiers apache.conf, apache-test.conf et apache-admin.conf situés dans";
$ap_inc2 = "dans votre fichier httpd.conf" ;
$ap_inc3 = "Vous devez également inclure la directive 'NameVirtualHost ";
$ht_inc1 = "Vous pouvez éventuellement inclure";
$ht_inc2 = "/hosts dans votre /etc/hosts pour résoudre vos domaines";
$ht_inc3 = "Le login/password pour accéder au site de test est:";

$mod_file = "Pour modifier l'authentification mise en place, utilisez la commande: ";
$set_db = "Utilisez les commandes suivantes pour mettre en place votre authentification MySQL: ";
$set_ldap = "Vérifiez que votre serveur LDAP est bien configuré afin d'utiliser l'authentification mise en place.";

$first_idx = "Vous devez lancer une première synchronisation du site afin d'indexer le moteur de recherche";

$h_head = "Installation";
$h_fs = "Système de fichiers";
$h_path = "Chemin d'accès";
$hd_path = "Le chemin dans lequel vont être installés les différents répertoires du CCMS.";
$h_cname = "Nom du CCMS";
$hd_cname = "Le nom du CCMS, servira de préfixe aux différents répertoires.";
$h_dname = "Nom de domaine (e-mail)";
$hd_dname = "Le nom de domaine associé pour l'envoi d'e-mails depuis le CCMS. Pensez à créer une adresse du type contact@domain.com.";
$h_serv = "Serveur";
$h_ip = "I.P.";
$hd_ip = "L'adress I.P. du serveur telle que vue sur le réseau.";
$h_lg = "Langues";
$h_langs = "Langues et sous-domaines associés";
$hd_langs = "Les langues utilisées sur votre site et les sous-domaines associés. Les indications ci-contre précisent que les sous domaines 'fr' et 'www' pointeront vers le site de langue 'fr', '-' indiquant qu'il s'agit de la langue par défaut.";
$h_dms = "Domaines";
$hd_dms = "Les domaines gérés par ce CCMS, le contenu pouvant varier d'un site à l'autre, le CCMS étant multi-domaines. Ici, domain1 correspond aux sites domain1.net et domain1.org";
$h_test = "Site de test";
$h_tlog = "Login de test";
$hd_tlog = "Le login utilisé pour accéder à l'interface de test.";
$h_tpass = "Mot de passe de test";
$h_auth = "Authentification";
$h_choixe = "Choix de l'authentification";
$hd_choice = "Choisissez ici le type d'authentification back-office que vous souhaitez utiliser. En fonction du type d'authentification choisi, seule la partie concernée sera à renseigner.";
$h_log = "Login d'administration";
$hd_log = "Le login utilisé pour accéder à l'interface d'administration";
$h_pass = "Mot de passe d'Administration";
$hd_pass = "Le mot de passe associé.";
$hd_vpass = "Pour éviter toute confusion issue d'une faute de frappe, veuillez saisir deux fois le mot de passe de façon identique.";
$h_serv = "Serveur";
$hd_serv = "L'adresse du serveur MySQL.";
$h_mbase = "Base MySQL";
$hd_mbase = "Le nom de la base MySQL utilisée.<br> Veuillez la créer avant de valider cette étape.";
$h_mlog = "Login";
$hd_mlog = "Le login utilisé pour la connection à la base MySQL.<br> Cet utilisateur doit déja exister dans la base.";
$h_mpass = "Mot de passe";
$hd_lserv = "L'adresse du serveur LDAP.";
$h_port = "Port";
$hd_port = "Le port du serveur LDAP.";

$h_click = "Une fois les champs remplis, cliquez sur ce bouton pour";
$h_cont = "continuer";
$h_end = "terminer";
$h_ins = "l'installation automatique :";
$h_ended = "Installation terminée";

?>
