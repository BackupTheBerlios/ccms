<? /* $Id: index.en.php,v 1.1 2003/09/17 12:40:44 terraces Exp $
Copyright (C) 2001, 2002, Makina Corpus, http://makinacorpus.org
This file is a componenet of CCMS <http://makinacorpus.org/index.php/ccms>
Created and maintained by mose (mose@makinacorpus.org)
Released under GPL version 2 or later, see LICENSE file
or http://www.gnu.org/copyleft/gpl.html
*/

$msg[1]="Please complete the following fields in order to start the install procedure";
$msg[2]="Please complete the following fields in order to configure back-office authentication.";

$no_cfgrights = "Web Server doesn't have writing acces on the configuration folder";
$no_rights = "Web Server doesn't have writing acces on folder:";
$tpath = "The folder";
$no_path = "Unable to create";
$already = "already exists. Please choose another folder or CCMS name.";

$err_ip = "This I.P. address is not valid.";
$err_name = "Please enter a valid CCMS name.";
$err_domain = "Please enter a valid domain name.";
$err_lang = "Please enter a valid language definition.";
$err_doms = "Please enter a valid domain definition.";
$err_admlog = "Please enter a valid administration login";
$err_admpass = "Please enter a valid administration password.";
$err_pass = "Passwords don't match.";
$err_tpass = "Test passwords don't match.";
$err_sql = "Please enter a valid MySQL server";
$err_sqllog = "Please enter a valid Mysql login.";
$err_sqlpass = "Please enter a valid MySQL password.";
$err_sqlchk = "MySQL passwords don't match.";
$err_ldap = "Please enter a valid LDAP server";
$err_ldport = "Please enter a valid LDAP port.";
$err_ldbase = "Please enter a valid LDAP BaseBind.";
$err_ldatt = "Please enter a valid LDAP attribute.";
$err_ldgbase = "Please enter a valid LDAP GroupBase";
$err_ldgrp = "Please enter a valid LDAP Group";
$err_ldbdn = "Please enter a valid LDAP BaseDN.";
$errçnodb = "Unable to connect to database:";

$ap_inc1 = "You must include the apache.conf, apache-test.conf and apache-admin.conf files from ";
$ap_inc2 = "in your httpd.conf file";
$ap_inc2 = "You must also include the directive 'NameVirtualHost";
$ht_inc1 = "You could include";
$ht_inc2 = "/hosts in your /etc/hosts file for local domain name resolution";
$ht_inc3 = "The login/password to access test website are:";

$mod_file = "To modify the installed authentication, please use : htpasswd ";
$set_db = "Do the following in order to install your MySQL authentication: 'mysqladmin create"; 
$set_ldap = "Verify that your LDAP server is correctly configured in order to use the installed authentication.";

$first_idx = "You must launch a first website synchronisation in order to index the search engine";

$h_head = "Install";
$h_fs = "File system";
$h_path = "Acces path";
$hd_path = "The path where CCMS folders are installed.";
$h_cname = "CCMS Name";
$hd_cname = "The CCMS name is used as a prefix for the every folder.";
$h_dname = "Domain name (e-mail)";
$hd_dname = "The domain name associated for sending emails from the CCMS. You might create an address like contact@domain.com";
$h_serv = "Server";
$h_ip = "I.P.";
$hd_ip = "The server I.P. as seen externaly on the network";
$h_lg = "Languages";
$h_langs = "Languages and associated hosts / subdomains";
$hd_langs = "The languages used for your website and associated sub domains or hosts. These lines indicate that the 'fr' and 'www' subdomains will point to the 'fr' language site. The '-' at the beginning of the line indicates the default language.";
$h_dms = "Domains";
$hd_dms = "The domains managed by this CCMS. As the CCMS is a multi-domains CMS, the content may change among sites. Here, 'domain1' concerns domains domain1.net and domain1.org";
$h_test = "Test website";
$h_tlog = "Test login";
$hd_tlog = "Login used to access the test website";
$h_tpass = "Test password";
$h_auth = "Authentication";
$h_choixe = "Authentification method";
$hd_choice = "Choose the authentication method you would like to use for the back office. Fill only the fields corresponding to the chosen authentication method.";
$h_log = "Administration login";
$hd_log = "Login used to acces the administration interface";
$h_pass = "Administration password";
$hd_pass = "The password associated with the Administration login name.";
$hd_vpass = "Enter the same password twice in order to avoid typing errors.";
$h_serv = "Server";
$hd_serv = "MySQL server address.";
$h_mbase = "MySQL Database";
$hd_mbase = "MySQL database name.<br> You must create it before completing this step.";
$h_mlog = "Login";
$hd_mlog = "MySQL login name for connection to the database.<br> This user must already exist in the database.";
$h_mpass = "Password";
$hd_serv = "LDAP server address.";
$h_port = "Port";
$hd_port = "LDAP server port.";

$h_click = "When all fields are completed click this button to";
$h_cont = "continue";
$h_end = "finish";
$h_ins = "the automatic install :";
$h_ended = "Install complete";

?>
