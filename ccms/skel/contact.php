<?/* $Id: contact.php,v 1.1 2003/09/17 12:40:47 terraces Exp $
Copyright (C) 2001, Makina Corpus, http://makinacorpus.org
Created and maintained by mose <mose@makinacorpus.org>
Released under GPL version 2. see LICENSE file
or http://www.gnu.org/copyleft/gpl.html
*/
include 'inc/conf.php';
include 'inc/format.php';
$topdomain = $cm_maildomain;

if (is_array($outo)) {
	foreach ($outo as $kmandato=>$mandato) {
		if (!trim($mandato)) {
			$required[] = $kmandato;
		}
	}
}

if ((!$required) and ($action == "go")) {
	$form = $cm_mailsent;
} else {
	$form = "<center><form action=$PHP_SELF method=post>";
	$form.= "<table cellpadding=20 cellspacing=0 border=1 bordercolor=#00A0C6><tr><td bgcolor=#FFFFFF><table border=0>";
	if (is_array($outo)) {
		foreach ($outo as $koutto=>$outto) {
			if ((is_array($required)) and (in_array($koutto,$required))) {
				$form.= "<tr><td align=right><b class=goodo>".strtr($koutto,"'"," ")."</b><br></td>";
				$form.= "<td><input type=text size=42 name=\"outo[$koutto]\" value=''></td></tr>\n";
			} else {
				$form.= "<input type=hidden name=\"outo[$koutto]\" value=\"$outto\">\n";
				$form.= "<tr><td align=right><b class=good>".strtr($koutto,"'"," ")."</b><br></td><td>$outto</td></tr>\n";
			}
		}
	}
	if (is_array($out)) {
		foreach ($out as $kouttp => $outtp) {
			$form.= "<input type=hidden name=\"out[$kouttp]\" value=\"$outtp\">\n";
			$form.= "<tr><td align=right><b class=good>".strtr($kouttp,"'"," ")."</b><br></td><td>$outtp</td></tr>\n";
		}
	}
	$form.= "<tr><td colspan=2 align=center><input type=hidden name=action value=go>\n";
	$form.= "<input type=reset name=action value=$cm_startover onclick=\"javascript:history.go(-1);\">\n";
	$form.= "<input type=submit name=submit value=$cm_send>";
	$form.= "</td></tr></table></td></tr></table></form></center>";
}

if ($form==$cm_mailsent) {
	foreach ($outo as $k=>$v) {
		$outp.= str_replace("'","",$k)." :\n$v\n\n";
	}
	foreach ($out as $k=>$v) {
		$outp.= str_replace("'","",$k)." :\n$v\n\n";
	}
	$origine = $out["'origine'"];
	$email = $outo["'Email '"];
	$to = $out["'to'"]."@".$topdomain;
	$mime = "MIME-Version: 1.0\nContent-Type: text/plain; charset=ISO-8859-1\nContent-Transfer-Encoding: 8bit";
	if ($email) {
	 	$plus = "From: $email\nCc: $email\n$mime";
	} else {
	 	$plus = "From: $nom ( $REMOTE_ADDR $X_HTTP_FORWARDED_FOR)\n$mime";
	}
	mail($to,"[$cm_site] from $nom $REMOTE_ADDR $X_HTTP_FORWARDED_FOR ","$outp", $plus);
	$feed = $cm_mailsent;
}

$SCRIPT_NAME='/index.php';
echo c_inc('head');
echo $form;
echo c_inc('foot');
?>
<?/* 
<pre><?print_r(get_defined_vars())?></pre> 
*/?>
