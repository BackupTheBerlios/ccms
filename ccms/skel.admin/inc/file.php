<? /* $Id: file.php,v 1.1 2003/09/17 12:40:53 terraces Exp $
Copyright (C) 2001,2002, Makina Corpus, http://makinacorpus.org
This file is a component of CCMS <http://makinacorpus.org/index.php/ccms>
Created and maintained by mose <mose@makinacorpus.org>
Released under GPL version 2 or later see LICENSE file
or http://www.gnu.org/copyleft/gpl.html
*/

if ($action == 'new') {
	echo "<form action='$SCRIPT_NAME?p=".urlencode($p)."' method=post>";
	echo "Quel sera le nom du nouveau fichier ?<br><br><ul>";
	echo "<input type=text size=18 name=nname value=''>";
	echo "<input type=hidden name=action value=cre>";
	echo "<input type=submit class=submit name=go value=Créer></ul></form>";
} elseif ($action == 'ren') {
  echo "<form action='$SCRIPT_NAME?p=".urlencode($p)."' method=post>";
	echo "Renommer <b>$p</b>.<br><br><ul>";
	echo "<input type=text size=18 name=nname value=".basename($p).">";
	echo "<input type=hidden name=action value=rnm>";
	echo "<input type=submit class=submit name=go value=Renommer></ul></form>";
} elseif ($action == 'mkd') {
	echo "<form action='$SCRIPT_NAME?p=".urlencode($p)."' method=post>";
	echo "Nouveau dossier dans <b>$p</b>.<br><br><ul>";
	echo "<input type=text size=18 name=ndir>";
	echo "<input type=hidden name=action value=cdr>";
	echo "<input type=submit class=submit name=go value=Créer></ul></form>";
} elseif ($action == 'arc') {
	echo "<form action='$SCRIPT_NAME?p=".urlencode($p)."' method=post>";
	echo "Vous vous appretez a archiver $p. ";
	echo "Votre confirmation est nécessaire pour valider cette opération.<br><br>";
	echo "<input type=submit class=submit name=action value=Annuler> ";
	echo "<input type=submit class=submit name=action value=del> ";
} elseif (@is_file($cf_datadir.$p)) {
	if ($ofile) {
		$page = cm_getarray($ofile,$format);
	} else {
		$page = cm_getarray($p,$format);
	}
	if ($action == 'dix') {
		echo "<form action='$SCRIPT_NAME?p=".urlencode($p)."' method=post>";
		echo "<table border=0 cellpadding=1 cellspacing=0>";
		echo "<tr><td bgcolor=#FFFFFF>Nom&nbsp;du&nbsp;fichier:</tD>";
		echo "<td bgcolor=#FFFFFF><div class=bmenu>".basename($p)."</div></tD></tr>";
		echo "<tr><td>&nbsp;</td><td>";
		echo "<input type=reset class=submit name=go value=Recommencer>";
		echo "<input type=submit class=submit name=go value='Enregistrer et Rester'>";
		echo "<input type=submit class=submit name=go value='Enregistrer'></td></tr>";
		echo a_edix($p);
		echo "<tr><td><input type=hidden name=action value=savx></td><td>";
		echo "<input type=reset class=submit name=go value=Recommencer>";
		echo "<input type=submit class=submit name=go value='Enregistrer et Rester'>";
		echo "<input type=submit class=submit name=go value='Enregistrer'></td></tr></table></form>";
	} elseif ($action == 'dit') {
		echo "<form action='$SCRIPT_NAME?p=".urlencode($p)."' method=post>";
		echo "<table border=0 cellpadding=1 cellspacing=0>";
		echo "<tr><td bgcolor=#FFFFFF>Nom&nbsp;du&nbsp;fichier:</tD>";
		echo "<td bgcolor=#FFFFFF><div class=bmenu>".basename($p)."</div></tD></tr>";
		echo "<tr><td>&nbsp;</td><td>";
		echo "<input type=reset class=submit name=go value=Recommencer>";
  	echo "<input type=submit class=submit name=go value='Enregistrer et Rester'>";
		echo "<input type=submit class=submit name=go value='Enregistrer'></td></tr>";
		echo a_edit($page);
		echo "<tr><td><input type=hidden name=action value=sav></td><td>";
		echo "<input type=reset class=submit name=go value=Recommencer>";
		echo "<input type=submit class=submit name=go value='Enregistrer et Rester'>";
  	echo "<input type=submit class=submit name=go value='Enregistrer'></td></tr></table></form>";
	} else {
		echo "<div class=menu>Fichier <b>$p</b></div>";
  	echo "<table border=0>".adm_browse($page,$p)."</table>";
	}
} else {
	$index = browse_index($p);
	echo "<div class=menu>Contenu de <b>$p</b></div>";
	echo "<table cellpadding=0 cellspacing=0 border=0>".adm_index($index,$p)."</table>";
}
?>
