<? /* $Id: flow.php,v 1.1 2003/09/17 12:40:47 terraces Exp $
Copyright (C) 2001,2002, Makina Corpus, http://makinacorpus.org
This file is a component of CCMS <http://makinacorpus.org/index.php/ccms>
Created and maintained by mose <mose@makinacorpus.org>
Released under GPL version 2 or later, see LICENSE file
or http://www.gnu.org/copyleft/gpl.html
*/

include 'inc/conf.php';
define(NOCACHE,true);
$closed = TRUE;
include 'inc/auth.php';
include 'inc/admin.php';

function a_mkdir($p, $m=0755) {
	$d = dirname($p);
	if (!is_dir($d)) { 
		a_mkdir($d); 
		@mkdir($d, $m);
	}
}

function indexSearch() {
    global $HTTP_HOST, $cf_webroot, $cf_datadir, $cf_domds, $cf_langds; 
    foreach($cf_domds as $dom) {
    	foreach($cf_langds as $lng) {
            $source = "$cf_webroot/$dom/$cf_datadir/$dom/$lng";
            $dbs = "$dom$lng";
            $cgi = "$HTTP_HOST/cgi-bin/index.pl?dbs=$dbs&&src=$source";
            if (substr($cgi,0,4)!='http') {
                $cgi="http://$cgi";
            }
            file($cgi);
    	}
    }
}

function a_synctree($p,$o='VIEW') {
	global $cf_adminroot, $cf_webroot, $cf_datadir;
	$dom = explode('/',$p);
	if ($o == 'FULL') {
		$to = "$cf_webroot/$dom[1]/$cf_datadir/$file";
		$from = "$cf_adminroot/$cf_datadir".$p;
		$file = @stristr($from,$dom[1]);
		$back = a_ltree($from);
		$back = diff_date($back,$to,$dom[1]);
		if (is_array($back)) {
			while (list($k,$v) = each($back)) {
				$from = $v;
				$file = @stristr($from,$dom[1]);
				$to = "$cf_webroot/$dom[1]/$cf_datadir/$file";
				if (is_file($from)) { 
					a_mkdir($to);
					@copy($from,$to);
				} 
			}
		}
	} elseif ($o == 'LIST') {
		if (is_array($p)) {
			foreach ($p as $q) {
				$from = $q;
				$file = str_replace('data','',stristr($q,$cf_datadir));
				$dom = explode('/',$file);
				$to = "$cf_webroot/$dom[1]/$cf_datadir$file";
				$back = a_ltree($from);
				$back = diff_date($back,$to,$dom[1]);
				if (is_file($from)) {
					a_mkdir($to);
					copy($from, $to);
				} 
			}
		}
	} else {
		$to = "$cf_webroot/$dom[1]/$cf_datadir";
		$from = "$cf_adminroot/$cf_datadir".$p;
		$back = a_ltree($from);
		$back = diff_date($back,$to,$dom[1]);
	}
	return $back;
}

function a_synclist($list,$i=0) {
	global $cf_datadir,$p,$domdir;
	if (!is_array($list)) return FALSE;
	foreach ($list as $v) {
		$w = split('/',$v);
		$stop = 0;
		$back.= "<div onmouseover=\"this.style.background='#FFFFFF';\" ";
		$back.= "onmouseout=\"this.style.background='none';\" ";
		$back.= "onclick=\"document.syncform.elements[$i].checked=!document.syncform.elements[$i].checked;\" div=sstitre>";
		$back.= "<input type=checkbox name=out[] value='$v' class=checkbox onclick='this.checked=!this.checked;'> ";
		$back.= "<a href=/index.php?p=".urlencode('/'.strstr($v,$domdir))." class=bnav>[ view ]</a> ";
		foreach ($w as $k=>$q) {
			if (!$stop and ($rol[$k] == $p[$k]) and ($go == 1)) {
				$back.= "<font class=dot2>/ $q </font>";
			} elseif ($go == 1){
				$back.= "/ $q ";
				$stop = 1;
			}
				$go = (stristr($q,$domdir)) ? 1 : $go ;
		}
		unset($go);
		$rol = $w;
		$back.= "</div>\n";
		$i++;
	}
	return $back;
}

if ($syncnow == 1) {
	if ($action == 'FULL Synchro') {
		$output = a_synctree($p,'FULL');
	} else {
		$output = a_synctree($out,'LIST');
	}
    indexSearch();
}

include "html/head.php";

echo "<form name=syncform method=get><blockquote><div class=titre>Synchro $p</div>\n";
echo "<div class=sstire>La pauvreté fonctionnelle de cette interface est provisoire, merci pour votre patience.</div><br>";

if (is_array($output)) {
	echo "<table cellpadding=1 cellspacing=0 border=0><tr><td bgcolor=#999999>";
	echo "<table cellpadding=5 cellspacing=0 border=0><tr><td bgcolor=#FFFFFF>";
	echo implode("<br>",$output);
	echo "</td></tr></table></td></tr></table>";
}

echo "<input type=reset name=rizette value='Aucun' class=submit>";
echo "<input type=reset name=rizette value='Tous' class=submit>";
echo "&nbsp;&nbsp;&nbsp;&nbsp;<input type=submit name=action value=Synchro class=submit><br><br>";

$sync = a_synctree($p);
if (sizeof($sync)) {
	echo a_synclist($sync,3);
} else {
	echo "<div class=sstitre>All files sync. Use 'FULL synchro' if you think it has not been done ";
	echo "for long (it syncs invisible files, like .idx).</div>";
}

echo "<input type=hidden name=p value='$p'>";
echo "<input type=hidden name=syncnow value='1'>";
echo "<br><input type=reset name=rizette value='Aucun' class=submit>";
echo "<input type=reset name=rizette value='Tous' class=submit>";
echo "&nbsp;&nbsp;&nbsp;&nbsp;<input type=submit name=action value=Synchro class=submit>";
echo "&nbsp;&nbsp;&nbsp;&nbsp;<input type=submit name=action value='FULL Synchro' class=submit><b>=></b><b class=tsubmit>$p</b><br><br>";
echo "</form></blockquote>\n";

include "html/foot.php";
?>
  
<? /* 
<pre><?print_r(get_defined_vars())?></pre> 
*/ ?>

