<? /* $Id: image.php,v 1.1 2003/09/17 12:40:53 terraces Exp $
Copyright (C) 2001,2002, Makina Corpus, http://makinacorpus.org
This file is a component of CCMS <http://makinacorpus.org/index.php/ccms>
Created and maintained by mose <mose@makinacorpus.org>
Released under GPL version 2 or later see LICENSE file
or http://www.gnu.org/copyleft/gpl.html
*/

function c_trpath($p,$m) {
	$list = split('/',$p);
	$one = '';
	array_shift($list);
	array_shift($list);
	while (is_array($list)) {
		if ($list[0] == '_'.$m) {
			array_shift($list);
			return "$m$one/".implode('/',$list);
			break;
		}
		$one.= array_shift($list);
	}
	return "bad : $p $one";
}
$max_size = $cf_uploadlimit;
if ($action == 'ren') {
  echo "<form action='$SCRIPT_NAME?p=".urlencode($p)."' method=post>";
	echo "Renommer <b>$p</b>.<br><br><ul>";
  echo "<input type=text size=18 name=nname value=".basename($p).">";
	echo "<input type=hidden name=action value=rnm>";
	echo "<input type=submit class=submit name=go value=Renommer></ul></form>";
} elseif ($action == 'arc') {
  echo "<form action='$SCRIPT_NAME?p=".urlencode($p)."' method=post>";
	echo "<B>Etes-vous sur</B> de vouloir effacer <b>$p</b> ?<br>";
	echo "<input type=hidden name=action value=del>";
	echo "<input type=submit class=submit name=annuler value='heu ... non !'>";
	echo "<input type=submit class=submit name=effacer value='oui oui'> ";
} elseif ($action == 'upl') {
	echo "<FORM ENCTYPE=multipart/form-data ACTION='$SCRIPT_NAME?p=".urlencode($p)."' METHOD=POST>\n";
	echo "<INPUT TYPE=hidden name=MAX_FILE_SIZE value=$max_size>";
	echo "<INPUT NAME=new TYPE=file size=30 class=input><br>";
	echo "<input type=hidden name=action value=load>";
	echo "<INPUT TYPE=submit name=up VALUE=uploader class=submit>";
	echo "</form>";
} elseif (@is_file($cf_datadir.$p)) {
	if (strstr($p,'_images')) {
		$impath = c_trpath($p,'images');
		echo "<img src=/$impath border=0>";
	} elseif (strstr($p,'_docs')) {
		$docpath = c_trpath($p,'docs');
	} else {
		echo "bah";
	}	
} else {
	$index = browse_index($p);
	echo "<div class=menu>Contenu de <b>$p</b></div>";
	echo "<table cellpadding=0 cellspacing=0 border=0>".adm_index($index,$p)."</table>";	
}
?>
