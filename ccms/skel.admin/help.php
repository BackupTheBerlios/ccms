<? /* $Id: help.php,v 1.1 2003/09/17 12:40:47 terraces Exp $
Copyright (C) 2001,2002, Makina Corpus, http://makinacorpus.org
This file is a component of CCMS <http://makinacorpus.org/index.php/ccms>
Created and maintained by mose <mose@makinacorpus.org>
Released under GPL version 2 or later, see LICENSE file
or http://www.gnu.org/copyleft/gpl.html
*/

include 'inc/conf.php';
$closed = TRUE;
include 'inc/auth.php';
include 'inc/admin.php';
$cm_cn = $cn;
include "html/head.php";
?>
<table border=0 cellspacing=0 cellpadding=2>
<tr><td bgcolor=#6699CC>
Attention !  La pr�sente documentation est en cours de r�daction. certains liens peuvent �tre cass�s.
</tD></tr></table>
<table border=0 cellspacing=0 cellpadding=0>
<tr><td valign=top>
<table border=0 cellspacing=0 cellpadding=2>
<tr><td bgcolor=#6699CC>
<font size=2>
<? 
$handle = opendir("./help");
while ($it = readdir($handle)) {
	if ((substr($it,0,1) != '.') and ($it != 'CVS')) {
		echo "<a href=$SCRIPT_NAME/help/$it class=menu>$it</a><br>";
	}
}
closedir($handle);
?>
</font></td></tr></table>
</td>
<td bgcolor=#6699CC valign=top width=100%>
<table border=0 cellspacing=2 cellpadding=15 width=100% height=100%>
<tr><td bgcolor=#dedede width=100% height=100%>
<? 
if ($PATH_INFO) {
	echo "<h2><font color=#336699>CCMS �</font> ".strtr($PATH_INFO,'/',' ')."</h2>";
	if (strstr($PATH_INFO,".php")) {
		#include "help$PATH_INFO";
	} else {
		echo "<pre>";
		echo htmlentities(implode('',file("help$PATH_INFO")));
		echo "</pre>";
	}
} else { ?>
<h2>� <font color=#336699>C.C.M.S.</font> � <font size=2><br>Collaborative Content Management System</font></h2>

<h3>Introduction</h3>

Le CCMS est un outil en ligne de gestion de contenu bas� sur un mod�le XML rudimentaire. Son usage doit permettre de faciliter la mise en ligne et la mise � jour de site web, au moyen d'interface simples et compl�tes.<br>

La version en cours de d�veloppement est le fruit d'une capitalisation de d�veloppement sur une demi-douzaine de sites complexes, c'est donc un produit jeune dont il faudra tol�rer les imperfection (et en reporter le d�tail bien entendu).

<? } ?>
</td></tr></table>
</td></tr></table>

<?
include "html/foot.php";
?>
