<? /* $Id: auth.ldap.php,v 1.1 2003/09/17 12:40:52 terraces Exp $
Copyright (C) 2001,2002, Makina Corpus, http://makinacorpus.org
This file is a component of CCMS <http://makinacorpus.org/index.php/ccms>
Created and maintained by mose <mose@makinacorpus.org>
Released under GPL version 2 or later, see LICENSE file
or http://www.gnu.org/copyleft/gpl.html
*/

if ($SERVER_PORT != '443') {
  header("Location: https://$cm_adminurl");
	exit;
}

$auth = FALSE;
$showform = TRUE;
session_start();
// $logged_in = session_is_registered('me');
$logged_in = $HTTP_SESSION_VARS[me];

if (!$logged_in or $logout) {
  if ($logout) {
  	authlog("Logout - $me");
  	session_destroy();
  	// session have been destroyed
  	$output = "Votre session a été detruite.";
  } elseif ($login && $pass) {
		$conn = ldap_connect($ldap_host,$ldap_port) or die("noway<hr>GULP!");
			if ((@ldap_bind($conn, $ldap_attr1.'='.$login.','.$ldap_basebind1,$pass)) or 
				  (@ldap_bind($conn, $ldap_attr2.'='.$login.','.$ldap_basebind2,$pass))) {
				$sr = ldap_search($conn, $ldap_groupbase,"cn=$ldap_group",array('cn'));
				if (ldap_count_entries($conn,$sr) > 0) {
				
					$i    = ldap_search($conn,$ldap_basedn,"uid=$login",array('cn','ou'));
					$ii   = ldap_first_entry($conn,$i);
					$ccn   = @ldap_get_values($conn,$ii,'cn');
					$oou   = @ldap_get_values($conn,$ii,'ou');
					$cn = $ccn[0];
					$ou = $oou[0];
					$auth = TRUE;
					$me = $login;
					session_register('me','cn','ou');
					authlog("Login - $login");
				} else {
					$auth = FALSE;
					$output = "Désolé vous n'avez pas accès a cet espace.";
					authlog("BadRight - $login");
				}
			} else {
      	$auth = FALSE;
    		$output = "Mot de passe invalide pour $login !";
    		authlog("BadPasswd - $login");
    	} 
  	@ldap_unbind($conn);
  	@ldap_close($conn);
  }
} else {
  $auth = TRUE;
} 

if (($closed and !$auth) or $identify) { ?>
<html><head><title>Authentification required</title></head>
<body bgcolor=#336699 text=#FFFFFF onLoad="document.authform.login.focus()" AUTOCOMPLETE="off">
<table width=100% height=100% border=0><tr><td align=center valign=middle>
<? // displays output message if it exists
if ($showform) {
    echo "<FORM  NAME=authform ACTION=$PHP_SELF METHOD=post>\n" ?>
<TABLE CELLPADDING=3 BORDER=0>
<? if ($output) { echo "<tr><td colspan=2><table border=0 cellpadding=4 width=100%><tr><td bgcolor=#993300 align=center><font size=4 color=#ffffff><b>$output</b></font></td></tr></table></td></tr>\n"; } ?>
<tr><td align=right><b><font color=#6699CC>Identification </font><font color=#88BBEE><?=$site?></font></b></td></tr>
<TR><TD ALIGN="right">login <INPUT TYPE="text" NAME="login" VALUE="<?=$login?>"><BR>password <INPUT TYPE="password" NAME="pass"><BR></TD></TR>
<TR><TD ALIGN="right"><INPUT TYPE="submit" NAME="action" VALUE="login"></TD></TR></TABLE></form>
<? } ?> 
</td></tr></table>
<? /* <pre><? print_r(get_defined_vars()); ?></pre> */ ?>
</body></html>
<? // break it up
exit; } 
// then if we didn't exit, we go on displaying the page...
?>
