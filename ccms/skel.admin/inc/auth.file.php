<? /* $Id: auth.file.php,v 1.1 2003/09/17 12:40:52 terraces Exp $
Copyright (C) 2001,2002, Makina Corpus, http://makinacorpus.org
This file is a component of CCMS <http://makinacorpus.org/index.php/ccms>
Maintained by mose <mose@makinacorpus.org>
Released under GPL version 2 or later, see LICENSE file
or http://www.gnu.org/copyleft/gpl.html
*/

$auth = FALSE;
$showform = TRUE;
session_start();
$logged_in = $HTTP_SESSION_VARS[me];

if (!$logged_in or $logout) {
  if ($logout) {
  	authlog("Logout - $me");
  	session_destroy();
  	// session have been destroyed
  	$output = "Votre session a été detruite.";
  } elseif ($login && $pass) {
	  if (!is_file("$cf_etcroot/users")) {
	    authlog("FileFailure for $login");
	    die ("Impossible d'accéder au fichier d'authentification.");
		}
		$query = file("$cf_etcroot/users");
		foreach ($query as $hq) {
			if ($login == strtok($hq,":")) {
				$cpass = trim(strtok(":"));
				$authflag = strtok(":");
			}
		}

		if (!$cpass) {
			$output = "Le login $login n'existe pas !";
			authlog("BadLogin - $login");
		} else {
			$seed = substr($cpass, 0, 2);
			$psd = crypt($pass, $seed);
			if ($psd != $cpass) {
				$output = "Mot de passe invalide pour $login !";
				authlog("BadPasswd - $login");
				$auth = FALSE;
			} else {
				if ($authflag == 'wait') {
					$output = "Désolé.<bR><br>\nVotre Login n'a pas encore été activé.\n";
					$output.= "<br><br>\n";
					authlog("NoRight - $login");
					$showform = FALSE;
				} else {
				  $auth = TRUE;
				 	$me = $login;
					session_register('me','flag');
					authlog("Login - $login");
				}
    	} 
		}
  }
} else {
  $auth = TRUE;
} 

if (($closed and !$auth) or $identify) { ?>
<html><head><title>Authentification required</title></head>
<body bgcolor=#336699 text=#FFFFFF onLoad="document.authform.login.focus()" AUTOCOMPLETE="off">
<table width=100% height=100% border=0><tr><td align=center valign=middle>
<TABLE CELLPADDING=3 BORDER=0>
<? if ($output) { echo "<tr><td colspan=2><table border=0 cellpadding=4 width=100%><tr><td bgcolor=#993300 align=center><font size=4 color=#ffffff><b>$output</b></font></td></tr></table></td></tr>\n"; } ?>
<? // displays output message if it exists
if ($showform) {
    echo "<FORM  NAME=authform ACTION=$PHP_SELF METHOD=post>\n" ?>
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
