<? /* $Id: admin.php,v 1.1 2003/09/17 12:40:52 terraces Exp $
Copyright (C) 2001,2002, Makina Corpus, http://makinacorpus.org
This file is a component of CCMS <http://makinacorpus.org/index.php/ccms>
Created and maintained by mose <mose@makinacorpus.org>
Released under GPL version 2 or later, see LICENSE file
or http://www.gnu.org/copyleft/gpl.html
*/
//echo "<pre>";
//print_r(get_defined_vars());
//echo "</pre>";


function a_listfiles_hist($p,$t,$getall=0) {
	$rootdir = dirname($p);
	$rootfile = basename($p);
	$query[] = "*";
	$args[] = '-r';
	$li = c_ls($rootdir,$rootfile,$query,$args);
	if (!is_array($li)) return FALSE;
	foreach ($li as $l) {
		$hit = split('\.',basename($l));
		if (strstr($hit[3],'#')) {
			$it = split("\#",$hit[3]);
			$new = "$rootdir/".$it[1];
			if ($getall) $back[] = str_replace('#','\#',$l);
			$back = array_merge($back, a_listfiles_hist("$rootdir/$it[1]",1));
			break;
		} else {
			$back[] = $l;
		}
	}
	return $back;
}


function diff_date($liste,$to,$dom) {
	if(!is_array($liste)) return FALSE;
	while(list($k,$v) = each($liste)) {
		$file = @stristr($v,$dom);
		if (@is_file("$to/$file")) {
			if (filemtime($v) > filemtime("$to/$file")) {
				$res[] = $v;
			}
		} else 
		{
			$res[] = $v;
		}
	}
	return $res;
}

function a_ltree($page) {//{{{
	global $cf_datadir,$cf_adminroot,$SCRIPT_NAME,$domdir;
	$c = "$cf_adminroot/$cf_datadir";
	$loc = $GLOBALS[np][0];
	if (is_file($page)) {
		$res[] = "$page";
		return $res;
	} 
	if (is_dir($page)) {
		$query[] = "*";
		$args[] = '1';
		$args[] = '-r';
		$ls = c_ls($page,'',$query,$args);
		if (!is_array($ls)) return FALSE;
		foreach ($ls as $it) {
				$res[] = "$page/$it";
				if (is_dir($page.'/'.$it)) {
					array_pop($res);
					$tmp = a_ltree("$page/$it");
					if (is_array($tmp)) $res = array_merge($res,$tmp);
				}
		}
		return $res;
	} else {
		return FALSE;
	}
}//}}}

function c_ls($rootdir,$rootfile,$query,$args='') {
	if (!is_dir($rootdir)) return FALSE;
	if ($handle = opendir("$rootdir")) {
		for($i=-2;$file = readdir($handle);$i++) {
		if (empty($file)) return FALSE;
			if (($file != '.') && ($file != '..') && ($file != '.idx')) {
				$ls[$i][name] = $file;
				$ls[$i][lastmod] = filemtime($rootdir.'/'.$file);
			}
		}
			closedir($handle); 
			if (!is_array($ls)) return FALSE;
	}
		if (is_array($query)) {
		for($i=0;$i<sizeof($query);$i++) {
			$query[$i] = ereg_replace('\.','\\.',$query[$i]);
			$query[$i] = ereg_replace('\*','.*',$query[$i]);
		}
	}
	while (list($k,$v) = each($ls)) {
		for($i=0;$i<sizeof($query);$i++) {
			if(preg_match("/$query[$i]/",$v[name]))
			$match[] = $rootdir.'/'.$v[name];
		}
	}
	if (!is_array($match)) return FALSE;
	if (!is_array($res)) $res = array();
	if (is_array($args)) {
		while (list($k,$v) = each($args)) {
			switch($v) {
				case "-r":
				rsort($match);
				if (empty($res)) $res = $match;
				break;
				case "1":
				while (list($e,$f) = each($match)) {
					if (substr(basename($f),0,1) != '.')
					$res[$e] = basename($f);
				}
				break;
				default:
				sort($match);
				$res = array_merge($res,$match);
			}
		}
	} else {
		$res = usort($match);
	}
	return $res;
}

function a_listfiles($p,$t,$getall=0) {
	$rootdir = dirname($p);
	$rootfile = basename($p);
	$query[] = ".*.*.$rootfile";
	$query[] = ".*.*.$rootfile#*";
	$args[] = '-r';
	$li = c_ls($rootdir,'',$query,$args);
	if (!is_array($li)) return FALSE;
	foreach ($li as $l) {
		$hit = split('\.',basename($l));
		if (strstr($hit[3],'#')) {
			$it = split("\#",$hit[3]);
			$new = "$rootdir/".$it[1];
			if ($getall) $back[] = str_replace('#','\#',$l);
				$back = array_merge($back, a_listfiles("$rootdir/$it[1]",1));
				break;
		} else {
			$back[] = $l;
		}
	}
	return $back;
}

function a_listhistory($p,$action) {
	global $cf_datadir, $SCRIPT_NAME, $more;
	$nbdisp = 7;
	$hist = a_listfiles("$cf_datadir$p",time());
	if ($more == 'his') {
		$his = $hist;
	} else {
		if (is_array($hist)) {
			$his = array_slice($hist,0,$nbdisp);
			$more = 'no';
		}
	}
	if (is_array($his)) {
		foreach ($his as $h) {
			$hit = split('\.',basename($h));
			$h = strstr($h,'/');
			$when = date("d/m/y H:i", $hit[1]);
			if ($ofile == $h) { 
				$back.= "<a href=$SCRIPT_NAME?p=".urlencode($p)."&action=dit&ofile=".urlencode($h)."&more=$more class=menu>";
				$back.= "<img src=/picto/deadfile.png width=8 height=8 vspace=0 hspace=3 border=0 alt=''>";
				$back.= "$when</a> ($hit[2])<br>\n";
			} else {
				$back.= "<a href=$SCRIPT_NAME?p=".urlencode($p)."&ofile=".urlencode($h)."&more=$more class=bmenu>";
				$back.= "<img src=/picto/newfile.png width=8 height=8 hspace=3 vspace=0 border=0 alt=''></a>";
				$back.= "<a href=$SCRIPT_NAME?p=".urlencode($p)."&action=dit&ofile=".urlencode($h)."&more=$more class=bmenu>";
				$back.= "$when</a> ($hit[2])<br>\n";
			}
		}
	}
	if (sizeof($hist) > $nbdisp) {
		if ($more == 'his') { $rmore = 'no'; $nb = sizeof($hist); $sign = "-"; }
		if ($more == 'no') { $rmore = 'his'; $nb = $nbdisp; $sign = "+"; }
		$back.= "<a href=$SCRIPT_NAME?p=".urlencode($p)."&more=$rmore class=bmenu>$sign</a> $nb/";
	} else {
		$back.= "= ";
	}	
	$back.= sizeof($hist)." old items<br>\n";
	return $back;
}

function a_flow($path='',$niv=0) {//{{{
  global $cf_datadir,$cf_adminroot,$SCRIPT_NAME;
  $c = "$cf_adminroot/$cf_datadir";
  if (is_dir("$c$path")) {
    $class = "dir";
		$query[] = "*";
		$args[] = '1';
		$args[] = '-r';
		$ls = c_ls($c.$path,'',$query,$args);
		if ($path == $p) { $class = "bdir"; }
    $cm_navfile.= "<div id=nopad1>";
    $cm_navfile.=  "<a href=index.php?p=".urlencode("$path");
    $cm_navfile.=  " class=$class>".str_repeat("&nbsp;",$niv*4).a_icondirtree(basename($path)).basename($path)."/</a></div>\n";
    $nivmore = $niv + 1;
    foreach ($ls as $it) {
      $cm_navfile.= a_flow("$path/$it",$nivmore);
    }
  } else {
    $class = "file";
    if ($path == $p) { $class = "bfile"; }
    $cm_navfile.=  "<div id=nopad>";
    $cm_navfile.= "<a href=index.php?p=".urlencode("$path");
    $cm_navfile.=  " class=$class>".str_repeat("&nbsp;",$niv*4).a_iconfiletree(basename($path)).basename($path)."</a></div>\n";
  } 
	return $cm_navfile;
}//}}}


function a_edix($p) {
	global $fwa, $fw, $cf_datadir;
	$back = "<tr><td valign=top align=right colspan=2>";
	$back.= "<textarea wrap=soft cols=$fwa rows=21 name='contentx'>";
	$back.= htmlentities(implode('',file("$cf_datadir$p")));
	$back.= "</textarea></td></tr>";	
	return $back;
}

function a_edit($page) {//{{{
	global $mformat, $format, $fwa, $fw;
	$back = "";
	while (list($p,$v) = each($page)) {
		$spec = split('\.',$mformat[$p]);
		if ($spec[0] == '-') {
			$back.= "<tr><td valign=top align=right>$p</td><td valign=top align=left>$v</td></tr>\n";
		} elseif ($spec[0] == 't') {
			$back.= "<tr><td valign=middle align=right>$p</td>";
      $back.= "<td valign=top align=left><input type=text size=$spec[1] name=aform[$p] value=\"".htmlspecialchars($v)."\"></td></tr>\n";
		} elseif (strtolower($spec[0]) == 'a') {
			$back.= "<tr><td valign=top align=right><br>$p</td>";
      $back.= "<td valign=top align=left><textarea wrap=soft cols=$spec[1] rows=$spec[2] name=aform[$p]>".htmlspecialchars($v)."</textarea></td></tr>\n";
		} elseif ($spec[0] == "m") {
			$it = split("\n",$v);
			$y = 0;
			$back.= "<tr><td valign=top align=right>$p</td><td valign=top align=left>\n";
			$back.= "<table border=0 bordercolor=#666666 callpadding=1 cellspacing=0>";
			foreach ($it as $t) {
				if (trim($t) == '') {
					$back.= "<tr><td colspan=".$spec[1]." bgcolor=#999999 align=right>";
					$back.= "<img src=/img/0.png width=1 height=1 hspace=0 vspace=0 border=0 alt=''>";
					$back.= "<input type=checkbox name='erase[$p][$y]' value=erase class=checkbox>";
					$back.= "<img src=/picto/kill.png width=8 height=8 vspace=3 hspace=1 border=0 alt='' align=baseline>";
					$back.= "</td></tr>\n";
				} elseif (substr(trim($t),0,1) == "#") {
					$back.= "<tr><td nowrap>";
					$back.= "<input type=text size=$fw name='aform[$p][$y]' value=\"".htmlspecialchars($t)."\">";
					$back.= "<input type=checkbox name='erase[$p][$y]' value=erase class=checkbox>";
					$back.= "<img src=/picto/kill.png width=8 height=8 vspace=3 hspace=1 border=0 alt='' align=baseline>";
					$back.= "</td></tr>\n";
				} else {
					$g = split(':::',$t);
					$gt = split(',',$spec[2]);
					$back.= "<tr><td valign=top nowrap>";
					for ($u=0;$u < $spec[1];$u++) {	
						if ($gt[$u] == 't') {
							$back.= "<br><input type=text name='aform[$p][$y][$u]' size=$fw value=\"".htmlentities(trim($g[$u]))."\">";
						} elseif ($gt[$u] == '-') {
							$back.= "<input type=hidden name='aform[$p][$y][$u]' value=\"".htmlentities(trim($g[$u]))."\">";
						} elseif ($gt[$u] == 'a') {
							$back.= "<br><textarea wrap=soft cols=$fwa rows=3 name='aform[$p][$y][$u]'>";
							$back.= htmlentities(trim($g[$u]))."</textarea>";
						}
					}
					$back.= "<input type=checkbox name='erase[$p][$y]' value=erase class=checkbox>";
					$back.= "<img src=/picto/kill.png width=8 height=8 vspace=3 hspace=1 border=0 alt='' align=baseline>";
					$back.= "</td></tr>";
				}
				$y++;
			}
			$back.= "<tr><td valign=top nowrap><br><div class=bmenu>Nouveau</div>";
			for ($u=0;$u < $spec[1];$u++) {	
				if ($gt[$u] == 't') {
					$back.= "<input type=text name='aform[$p][$y][$u]' size=$fw value=\"\"><br>";
				} elseif ($gt[$u] == '-') {
					$back.= "<input type=hidden name='aform[$p][$y][$u]' value=\"\"><br>";
				} elseif ($gt[$u] == 'a') {	
				$back.= "<textarea wrap=soft cols=$fwa rows=3 name='aform[$p][$y][$u]'></textarea><br>";
				}
			}
			$back.= "</td></tr></table>\n";
		} else {
			$back.= "<tr><td valign=top align=right>no</td><td valign=top align=left>no</td></tr>\n";
		}
	}
	return $back;
}//}}}

function adm_browse($page,$path) {//{{{
	global $mformat,$filter;
	$back = "";
	while (list($p,$v) = @each($page)) {
		$spec = split('\.',$mformat[$p]);
		if ($spec[0] == 'A') {	
			$back.= "<tr><td valign=top align=right><b class=file>$p</b><bR></td><td valign=top align=left>";
			include c_buildconf($path,"filter","filter");
			//$back.= htmlentities(cm_richtext($v));
			$back.= cm_richtext($v);
			$back.= "</td></tr>\n";
		} elseif ($spec[0] == "m") {
			$it = split("\n",$v);
			$back.= "<tr><td valign=top align=right><b class=file>$p</b><br></td><td valign=top align=left>";
			$back.= "<table border=0 cellpadding=0 cellspacing=0><tr><td valign=top align=left bgcolor=#999999>";
			$back.= "<table border=0 cellpadding=3 cellspacing=1>";
			foreach ($it as $t) {
				if (trim($t) == '') {
					$back.= "<tr><td colspan=".$spec[1]." bgcolor=#dedede>";
					$back.= "<img src=/img/0.png width=1 height=1 hspace=0 vspace=0 border=0 alt=''></td></tr>\n";
				} elseif (substr(trim($t),0,1) == "#") {
					$back.= "<tr><td colspan=".$spec[1]." bgcolor=#dedede>$t</td></tr>\n";
				} else {
					$g = split(':::',$t);
					$back.= "<tr>";
					for ($u=0;$u < $spec[1];$u++) {
						$back.= "<td valign=top bgcolor=#ffffff>".htmlentities(trim($g[$u]))."</td>";
						//$back.= "<td valign=top bgcolor=#ffffff>".trim($g[$u])."</td>";
					}
					$back.= "</tr>";
				}
			}
			$back.= "</table></td></tr>\n";
			$back.= "</table></td></tr>\n";
		} else {
			$back.= "<tr><td valign=top align=right><b class=file>$p</b><br></td><td valign=top align=left>".str_replace("\r\n","<br>",htmlentities($v))."</td></tr>\n";
		}
	}
	return $back;
}//}}}

function adm_index($index,$path) {
	global $SCRIPT_NAME;
	$back = "";
	if (is_array($index)) {
		foreach ($index as $k=>$v) {
			if ($k[0]!='.') {
				$back.= "<tr ";
				$back.= "onmouseover=\"this.style.background='#FFFFFF';\" onmouseout=\"this.style.background='none';\" ";
				$back.= "onclick=\"document.location='$SCRIPT_NAME?p=".urlencode("$path/$k")."';\"";
				$back.= "><td><a href=$SCRIPT_NAME?p=".urlencode("$path/$k")." class=bmenu";
				$back.= ">$k</a></td>";
				$back.= "<td class=file>$v[titre]</td><td class=dot2>$v[auteur]<br></td><td class=dot1>$v[date]</tD></tr>";
			}	
		}
	} else {
		$back.= "<tr><td>Ce dossier est vide<br></td></tr>";
	}
	return $back;
}

function lockfile($path) {
  global $me;
  @touch("/tmp/:lock:".$me.".".time().":".urlencode($path));
  return true;
}

function unlockfile($path) {
  $cmd = "rm -f /tmp/:lock:*:".urlencode($path);
  exec($cmd, $li);
  return true;
}

function checklock($path) {
  $ls = "ls -d1 /tmp/:lock:*:".urlencode($path)." | cut -d: -f3";
  exec($ls, $l);
  if ($l) {
		$lm = split('\.',$l);
		$res[who] = $lm[0];
		$res[when] = date('\le d \a H:i:s',$lm[1]);
		return $res;
  } else {
		return false;	
  }
}

function a_icondirtree($it) {
	global $cf_langs, $cf_doms;
	if ($it and in_array($it,$cf_langs)) {
		return "<img src=/picto/".$it."_ic.png width=8 height=8 vspace=0 hspace=4 border=0 alt='' align=baseline>";
	} elseif ($it and in_array($it,$cf_doms)) {
		return "<img src=/picto/ball.png width=8 height=8 vspace=0 hspace=4 border=0 alt='' align=baseline>";
	} else {
		return "<img src=/picto/folder2.png width=8 height=8 vspace=0 hspace=4 border=0 alt='' align=baseline>";
	}
}

function a_iconfiletree($it) {
	global $cf_langs, $cf_doms;
	if (eregi("\.(gif|jpe?g|png)$",$it)) {
		return "<img src=/picto/image.png width=8 height=8 vspace=0 hspace=4 border=0 alt='' align=baseline>";
	} else {
		return "<img src=/picto/file.png width=8 height=8 vspace=0 hspace=4 border=0 alt='' align=baseline>";
	}
}

function a_rectree($p,$path='/',$niv=0) {//{{{
	global $cf_datadir,$cf_adminroot,$SCRIPT_NAME,$cm_navfile;
	$c = "$cf_adminroot/$cf_datadir";
	if (is_dir("$c$path")) {
		$class = "dir";
		$query[] = "^[^.]*";
		$args[] = '1';
		$args[] = '-r';
		$ls = c_ls($c.$path,'',$query,$args);
		if ($path == $p) { $class = "bdir"; }
		$cm_navfile.= "<div id=nopad1 onmouseover=\"this.style.background='#FFFFFF';\" onmouseout=\"this.style.background='none';\">";
		$cm_navfile.=  "<a href=$SCRIPT_NAME?p=".urlencode("$path")." class=$class>";
		$cm_navfile.= "<img src=/picto/0.png width=1 height=1 hspace=".(1+(5*$niv))." vspace=0 border=0 alt=''>";
		$cm_navfile.= a_icondirtree(basename($path)).basename($path)."/</a></div>\n";
		$nivmore = $niv + 1;
		if (!is_array($ls)) return FALSE;
		foreach ($ls as $it) {
			if ($p and strstr($p,$path)) {
				a_rectree($p,"$path/$it",$nivmore);
			}
		}
	} else {
		$class = "file";
		if ($path == $p) { $class = "bfile"; }
		$cm_navfile.=  "<div id=nopad onmouseover=\"this.style.background='#FFFFFF';\" onmouseout=\"this.style.background='none';\">";
		$cm_navfile.= "<a href=$SCRIPT_NAME?p=".urlencode("$path");
		$cm_navfile.=  " class=$class>".str_repeat("&nbsp;",$niv*4).a_iconfiletree(basename($path)).basename($path)."</a></div>\n";
	}
}//}}}

function a_buildlang($p) {
	global $cf_datadir,$cf_langds,$langdir,$SCRIPT_NAME;
	if (is_file("$cf_datadir$p")) {
		$back = '<br><div class=menu>';
		foreach ($cf_langds as $l) {
			if (ereg("/$l/",$p,$res)) {
				$langdir = $l;
				break;
			}
		}
		if (is_array($cf_langdir)) {
			reset($cf_langdir);
		}
		foreach ($cf_langds as $l) {
			if ($l != $langdir) {
				$dp = str_replace($langdir,$l,"$cf_datadir$p");
				if (is_file($dp)) {
					$back.= "<a href=$SCRIPT_NAME?p=".urlencode(strstr($dp,'/'))." class=bmenu>";
					$back.= "<img src=/picto/{$l}_ic.png width=8 height=8 vspace=3 hspace=3 border=0 alt='$l' align=left>";
					$back.= "Version ".strtoupper($l)."</a><br clear=all>";
				} else {
					$back.= "<a href=$SCRIPT_NAME?p=".urlencode(strstr(dirname($dp),'/'))."&action=cre&nname=".basename($p)." class=file>";
					$back.= "<img src=/picto/{$l}_ic.png width=8 height=8 vspace=3 hspace=3 border=0 alt='$l' align=left>";
					$back.= "Traduire en ".strtoupper($l)."</a><br clear=all>";
				}
			} else {
				$back.= "<a href=$SCRIPT_NAME?p=".urlencode(strstr($dp,'/'))." class=menu>";
				$back.= "<img src=/picto/{$l}_ic.png width=8 height=8 vspace=3 hspace=3 border=0 alt='$l' align=left>";
				$back.= "Version ".strtoupper($l)."</a><br clear=all>";
			}
		}
		$back.= "</div>";
	}
	return $back;
}


function a_control($p) {//{{{
	global $cf_datadir,$SCRIPT_NAME, $action;
	if (is_file("$cf_datadir$p")) {
		$r = checklock($p);
		if ($r) {
			$cm_nav.= "<div class=menu>";
			$cm_nav.= "<img src=/picto/locked.png width=8 height=8 vspace=2 hspace=3 border=0 alt='' align=left>";
			$cm_nav.= "Fichier Vérouillé<br clear=all></div><div class=file><b>$r[who]</b><br>$r[when]</div>\n";
			$cm_nav.= "<a href='$SCRIPT_NAME?p=".urlencode($p)."&action=ulk' class=bmenu>";
			$cm_nav.= "<img src=/picto/unlocked.png width=8 height=8 vspace=2 hspace=3 border=0 alt='' align=left>Dévérouiller</a><br clear=all>";
			$cm_nav.= "</div><br><div class=file>";
			$cm_nav.= a_listhistory($p,'his');
			$cm_nav.= "</div>";
		} else {
			$cm_nav = "<div class=menu>";
			$cm_nav.= "<a href='$SCRIPT_NAME?p=".urlencode($p)."&action=ren' class=bmenu>";
			$cm_nav.= "<img src=/picto/pencil.png width=8 height=8 vspace=2 hspace=3 border=0 alt='' align=left>Renommer</a><br clear=all>";
			if (((strstr($p,'_images')) or (strstr($p,'_docs'))) && (!is_file("$cf_datadir$p"))){
				$cm_nav.= "<a href='$SCRIPT_NAME?p=".urlencode($p)."&action=upl' class=bmenu>";
				$cm_nav.= "<img src=/picto/newfile.png width=8 height=8 vspace=2 hspace=3 border=0 alt='' align=left>Uploader</a><br clear=all>";
			} elseif ((!strstr($p,'_images')) && (!strstr($p,'_docs'))) {
				$cm_nav.= "<a href='$SCRIPT_NAME?p=".urlencode($p)."&action=dit' class=bmenu>";
				$cm_nav.= "<img src=/picto/pencil.png width=8 height=8 vspace=2 hspace=3 border=0 alt='' align=left>Editer</a><br clear=all>";
				$cm_nav.= "<a href='$SCRIPT_NAME?p=".urlencode($p)."&action=dix' class=bmenu>";
				$cm_nav.= "<img src=/picto/pencil.png width=8 height=8 vspace=2 hspace=3 border=0 alt='' align=left>Editer (brut)</a><br clear=all>";
			}
			$cm_nav.= "</div>";
			$cm_nav.= "<div class=menu>";
			$cm_nav.= "<a href='$SCRIPT_NAME?p=".urlencode($p)."&action=arc' class=bmenu>";
			$cm_nav.= "<img src=/picto/kill.png width=8 height=8 vspace=2 hspace=3 border=0 alt='' align=left>Effacer</a><br clear=all>";
			$cm_nav.= "</div><br><div class=file>";
			$cm_nav.= a_listhistory($p,'his');
			$cm_nav.= "</div>";
		}
	} elseif (is_dir("$cf_datadir$p")) {
			$cm_nav = "<div class=menu>";
			$cm_nav.= "<a href='$SCRIPT_NAME?p=".urlencode($p)."&action=ren' class=bmenu>";
			$cm_nav.= "<img src=/picto/pencil.png width=8 height=8 vspace=2 hspace=3 border=0 alt='' align=left>Renommer <u>".basename($p)."</u></a><br clear=all>";
			$cm_nav.= "<a href='$SCRIPT_NAME?p=".urlencode($p)."&action=arc' class=bmenu>";
			$cm_nav.= "<img src=/picto/kill.png width=8 height=8 vspace=2 hspace=3 border=0 alt='' align=left>Effacer <u>".basename($p)."</u></a><br clear=all>";
			$cm_nav.= "</div>";
			$cm_nav.= "<div class=menu>";
			$cm_nav.= "<a href='$SCRIPT_NAME?p=".urlencode($p)."&action=mkd' class=bmenu>";
			$cm_nav.= "<img src=/picto/newfolder2.png width=8 height=8 vspace=2 hspace=3 border=0 alt='' align=left>Nouveau Dossier</a><br clear=all>";
			if ((strstr($p,'_images')) or (strstr($p,'_docs'))) {
				$cm_nav.= "<a href='$SCRIPT_NAME?p=".urlencode($p)."&action=upl' class=bmenu>";
				$cm_nav.= "<img src=/picto/newfile.png width=8 height=8 vspace=2 hspace=3 border=0 alt='' align=left>Uploader</a><br clear=all>";
			} else {
				$cm_nav.= "<a href='$SCRIPT_NAME?p=".urlencode($p)."&action=new' class=bmenu>";
				$cm_nav.= "<img src=/picto/newfile.png width=8 height=8 vspace=2 hspace=3 border=0 alt='' align=left>Nouveau Fichier</a><br clear=all>";
			}
			$cm_nav.= "<a href='$SCRIPT_NAME?p=".urlencode($p)."&action=idx' class=bmenu>";
			$cm_nav.= "<img src=/picto/regen.png width=8 height=8 vspace=2 hspace=3 border=0 alt='' align=left>Recalculer</a><br clear=all>";
			$cm_nav.= "</div>";
	} elseif ($action == "new") {
			$cm_nav = "Nouveau Fichier";
	} else {
			$cm_nav = "error";
	}
	return $cm_nav;
}//}}}

function mk_dir($p) {//{{{
	$parts = split('/',$p);
	foreach ($parts as $t) {
		if (!is_dir($p)) {
			if (!is_dir(dirname($p))) mk_dir(dirname($p));
			mkdir($p,0770);
		}
	}	
}//}}}

// ================================================================================

if ($action == 'new') {
	$file = '';
}

if ($action == 'cdr') {
	mk_dir($cf_datadir.$p.'/'.$ndir,0770);
	$p = $p.'/'.$ndir;
}

if ($action == 'load') {
	$max_size = $cf_uploadlimit;
	if ($MAX_FILE_SIZE != 0) {
		if ($new_size == 0) {
			$output[] = "Vous n'avez pas désigné de fichier à uploader. $new";
		} elseif ($new_size > $max_size) {
			 $output[] = "Le fichier $newicon_name dépasse les $max_size, c'est beaucoup trop gros !";
		} else {
			if (copy("$new","$cf_datadir$p/$new_name")) {
				chmod("$cf_datadir$p/$new_name",0660);
				$output[] = "Nickel : $new_size bytes transferred.";
				$postaction = 'idx';
				$p = "$p/$new_name";
			} else {
				$output[] = "coin coin : $cf_datadir$p/$new_name";
			}
		}
	}
} elseif ($action == "sav") {
	$abody = '';
	if (is_file("$cf_datadir$p")) {
		$arcfile = "$cf_datadir".dirname($p)."/.".time().".$me.".basename($p);
		rename("$cf_datadir$p",$arcfile);
	}
	//$p = dirname($p)."/".$nfile;
	if (touch("$cf_datadir$p")) {
		$ftime = date(time(),"d/m/Y H:M");
		foreach ($aform as $kaform=>$vaform) {
			if (is_array($vaform)) {
				$abody.= "<$kaform>\n";
				foreach ($vaform as $vl=>$vline) {
					if (is_array($vline) and ($vline[0]) and !($erase[$kaform][$vl])) {
						$abody.= c_sanitize(implode(':::',$vline))."\n";
					} elseif (!is_array($vline) and trim($vline)) {
						$abody.= "$vline";
					}
				}
				$abody.= "</$kaform>\n";
			} else {
				$abody.= "<$kaform>\n".c_sanitize(trim(stripslashes($vaform)))."\n</$kaform>\n";
			}
		}
		$abody.= "<AUTEUR>\n$me\n</AUTEUR>\n";
		$abody.= "<DATE>\n".date("d/m/Y, H\hi",time())."\n</DATE>\n";
		$fp = fopen("$cf_datadir$p","w+");
		fputs($fp,$abody);
		fclose($fp);
		if ($go == 'Enregistrer et Rester') {
			$action = 'dit';
			$postaction = 'idx';
		}
		$output[] = "Fichier $p enregistré.";
		if ($go != 'Enregistrer et Rester') {
			$action = 'ulk';
			$postaction = 'idx';
		}
	} else {
		$output[] = "Impossible de créer $p";
	}
} elseif ($action == 'savx') {
	if (is_file("$cf_datadir$p")) {
		$arcfile = "$cf_datadir".dirname($p)."/.".time().".$me.".basename($p);
		rename("$cf_datadir$p",$arcfile);
	}
	//$p = dirname($p)."/".$nfile;
	if (touch("$cf_datadir$p")) {
		$fp = fopen("$cf_datadir$p","w+");
		fputs($fp,$contentx);
		fclose($fp);
		if ($go == 'Enregistrer et Rester') {
		  $action = 'dix';
			$postaction = 'idx';
		}
		$output[] = "Fichier $p enregistré.";
		if ($go != 'Enregistrer et Rester') {
			$action = 'ulk';
			$postaction = 'idx';
		}
	}
} elseif ($action == 'rnm') {
	if (ereg("^[-_\.a-zA-Z0-9]+$",$nname)) {
		if (is_file("$cf_datadir$p")) {
			$np = dirname($p)."/".$nname;
			if (is_file("$cf_datadir$np")) {
				$output[] = "$np existe deja.";
			} else {
				rename("$cf_datadir$p","$cf_datadir$np");
				touch("$cf_datadir".dirname($p)."/.".time().".".$me.".".basename($np)."#".basename($p));
				$output[] = "Le fichier ".basename($p)." a été renommé en ".basename($np).".";
				$p = $np;
				$postaction = 'idx';
			}
		} elseif (is_dir("$cf_datadir$p")) {
			$np = dirname($p)."/".$nname;
			if (is_file("$cf_datadir$np")) {
			  $output[] = "$np existe deja.";
			} else {
				rename("$cf_datadir$p","$cf_datadir$np");
				touch("$cf_datadir".dirname($p)."/.".time().".$me.".basename($p)."#".$nname);
				$output[] = "Le dossier ".basename($p)." a été renommé en ".basename($np).".";
				$p = $np;
				$postaction = 'idx';
			}
		} else {
			$output[] = "Erreur $cf_datadir$p.";
		}
	} else {
		$output[] = "Vous devez spécifier un nom composé de -_.a-zA-Z0-9";
	}
} elseif ($action == 'cre') {
	if (ereg("^[-_\.a-z0-9]+$",$nname)) {
	  if (is_file("$cf_datadir$p/$nname")) {
			$output[] = "$np existe deja.";
		} else {
			mk_dir("$cf_datadir$p");
			touch("$cf_datadir$p/$nname");
			$output[] = "$nname a été créé dans $p.";
			$p = "$p/$nname";
			$action = "dit";
		}
	} else {
		$output[] = "Vous devez spécifier un nom composé de -_.a-z0-9.";
	}
}

if ($action == 'del') {
	if (is_dir("$cf_datadir$p")) {
		mk_dir("data_bak".dirname($p));
		if (is_dir("data_bak$p")) {
			rename("data_bak$p","data_bak".$p."_".time());
		}
		if (rename("$cf_datadir$p","data_bak$p")) {
			$output[] = "Dossier $p archivé.";
			$action = "idx";
			$file = '';
			$p = dirname($p);
		} else {
			$output[] = "Impossible d'archiver $p";
		}
	} elseif (is_file("$cf_datadir$p")) {
		mk_dir("data_bak".dirname($p));
		if (is_file("data_bak$p")) {
			rename("data_bak$p","data_bak".$p."_".time());
		}
		if (is_dir("data_bak$p")) {
			rename("data_bak$p","data_bak".$p."_".time());
		}
		if (rename("$cf_datadir$p","data_bak$p")) {
			$toarch = a_listfiles_hist("$cf_datadir$p",1);
			if(is_array($toarch)) {
				while(list($k,$v) = each($toarch)) {
					if((is_file($v)) && (is_dir("data_bak".dirname($p)))) {
					
					}
				}
			}
			//exec("mv ".implode(' ',$toarch)." --target-directory=data_bak".dirname($p),$mvd);
			$output[] = "Fichier $p archivé. (".count($toarch)." sauvegardes archivées)";
				$action = "idx";
			$file = '';
			$p = dirname($p);
		} else {
			$output[] = "Impossible d'archiver $p";
		}
	} else {
		$output[] = "$p n'existe pas.";
	}
}

if (($action == "idx") or ($postaction == "idx")) {
	if (@is_dir($cf_datadir.$p)) {
      	$rebuilt = rebuild_index($p);
	} else {
		$rebuilt = rebuild_index(dirname($p));
	}
	$output[] = ".idx $p reconstruit.";
}

if ($p and ($action == "dit")) {
  $locked = lockfile("$p");
	$output[] = "Fichier $p en mode edition.";
} elseif ($p and ($action == "dix")) {
  $locked = lockfile("$p");
	$output[] = "Fichier $p en mode edition brute.";
} elseif ($action == "ulk") {
  unlockfile("$p");
	$output[] = "Fichier $p dévérouillé.";
} elseif ($action == "lok") {
  lockfile("$p");
	$output[] = "Fichier $p vérouillé.";
}
?>
