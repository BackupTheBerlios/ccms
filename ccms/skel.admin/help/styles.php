<? /* $Id: styles.php,v 1.1 2003/09/17 12:40:49 terraces Exp $
Copyright (C) 2001,2002, Makina Corpus, http://makinacorpus.org
This file is a component of CCMS <http://makinacorpus.org/index.php/ccms>
Created and maintained by mose <mose@makinacorpus.org>
Released under GPL version 2 or later, see LICENSE file
or http://www.gnu.org/copyleft/gpl.html
*/

echo "<table border=0 cellpadding=0 cellspacing=3>";
echo "<tr><td colspan=2 bgcolor='#336699'><img src=/img/0.png height=1 width=0 hspace=0 vspace=0 alt='' border=0></tD></tr>";
if (is_file("$cf_datadir/$domdir/_conf/filter")) {
	$meat = implode('', file("$cf_datadir/$domdir/_conf/filter"));
	$res = ereg("<CONTENU>(.*)</CONTENU>",$meat,$r);
	$body = split("\n",trim($r[1]));
	foreach ($body as $a=>$b) {
		$mine = split(":::",$b);
		if (trim($mine[1]) and trim($mine[2])) {
			echo "<tr><td valign=top><font size=2>Regexp</font></td>";
			echo "<td><font color='#336699'>".htmlentities($mine[1])."</font><br></td></tr>\n";
			echo "<tr><td valign=top><font size=2>HTML</font></td>";
			echo "<td><font color='#666666'>".htmlentities($mine[2])."</font><br></td></tr>\n";
			echo "<tr><td valign=top><font size=2>Explication</font></tD>";
			echo "<td bgcolor='#FFFFFF'>$mine[3]</td></tr>";
			echo "<tr><td colspan=2 bgcolor='#336699'><img src=/img/0.png height=1 width=0 hspace=0 vspace=0 alt='' border=0></tD></tr>";
		}
	}
}
echo "</table>";
/*
echo "<pre>".print_r(get_defined_vars())."</pre>";
*/
?>

