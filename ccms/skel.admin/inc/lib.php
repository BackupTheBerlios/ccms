<?/* $Id: lib.php,v 1.1 2003/09/17 12:40:54 terraces Exp $
Copyright (C) 2001,2002, Makina Corpus, http://makinacorpus.org
This file is a component of CCMS <http://makinacorpus.org/index.php/ccms>
Created and maintained by mose <mose@makinacorpus.org>
Released under GPL version 2 or later see LICENSE file
or http://www.gnu.org/copyleft/gpl.html
*/

include "user_functions.php";

function c_iscached($path,$q='') {//{{{
	global $cm_cachedir;
	if ($q) $q = "/$q";
	$where = "$cm_cachedir/".urlencode($path.$q).".php";
	if (NOCACHE or !@is_file($where)) {
		return FALSE;
	} else {
		return $where;
	}
}//}}}
function c_writecache($feed,$path,$q='') {//{{{
	global $cm_cachedir;
	if ($q) $q = "/$q";
	$where = "$cm_cachedir/".urlencode($path.$q).".php";
	$fp = fopen($where,'w');
	fputs($fp,$feed);
	fclose($fp);
	return $where;
}//}}}
function c_readcache($path,$q='') {//{{{
	global $cm_cachedir;
	if ($q) $q = "/$q";
	readfile("$cm_cachedir/".urlencode($path.$q).".php");
	return TRUE;
}//}}}

function c_inc($skin,$type="") {//{{{
	global $page;
	$skvar = "cm_skin_$skin";
	$outp = $GLOBALS[$skvar];
	$search = array("/\(\[([A-Za-z][_a-z0-9]+)\]\)/e",
					"/\(\[([A-Z]+)\]\)/e",
					"/\(\[_([a-z]+)(\.([^\]]*))?\]\)/e");
	$replace = array("\$GLOBALS['cm_'.strtolower('\\1')]",
					"\$page['\\1']",
					"user_\\1(\"\\3\")");
	//$o = preg_replace($search, $replace, $outp);
	//echo "<pre>";die(print_r(get_defined_vars()));
	return preg_replace($search, $replace, $outp);
}//}}}

function c_xinc($skin) {//{{{
	$skvar = "cm_skin_$skin";
	$outp = $GLOBALS[$skvar];
    $search = array("/\(\[([A-Za-z][_a-z0-9]+)\]\)/e");
    $replace = array("\$GLOBALS['cm_'.strtolower('\\1')]");
    return preg_replace($search, $replace, $outp);
}//}}}


function cm_buildlang($array,$lang,$nav="lang") {//{{{
	foreach ($array as $n) {
		$GLOBALS['cm_urlpage'] = '/'.$n.'/'.$GLOBALS['cm_dir'].'/'.$GLOBALS['cm_file'];
		$GLOBALS['cm_lg'] = $n;
		if (is_file($GLOBALS['cm_datadir'].'/'.$GLOBALS['cm_data'].'/'.$n.'/'.$GLOBALS['cm_dir'].'/'.$GLOBALS['cm_file'])) {
		if ($lang == $n) { 
			$back.= trim(c_inc($nav.'on'));
		} else {
			$back.= trim(c_inc($nav));
		}
		}
	}
	return $back;
}//}}}

function cm_buildlist($array,$skin='list') {//{{{
	$back = '';
	foreach ($array as $GLOBALS['cm_lfile']=>$n) {
		$GLOBALS['cm_ldate'] = $n[date];
		$GLOBALS['cm_ltitre'] = $n[titre];
		$GLOBALS['cm_ltitredivers'] = $n[titredivers];
		$back.= trim(c_inc($skin));
	}
	return $back;
}//}}}

function cm_log($name,$type) {//{{{
	global $cm_logdir;
	$log = date("Y/m/d - H:i:s")." - ";
	$log.= getenv('PHP_SELF')." - $type - ";
	$log.= getenv('REMOTE_ADDR')." - ";
	$log.= getenv('HTTP_X_FORWARDED_FOR')." - ";
	$log.= getenv('HTTP_USER_AGENT')."\n";
  $fp = fopen("$cm_logdir/.$name.log", "a+");
	while (!flock($fp,2) and ($i < 30)) { sleep(2); $i++; }
	@fputs($fp,$log);
	flock($fp,3);
	@fclose($fp);
}//}}}

/* wrapper for cm_log() function specific to auth logs */
function authlog($type) {
  cm_log('auth',$type);
}

function c_buildconf($path,$type,$prefix='cm',$page='') {
	global $cf_datadir, $cf_format, $p;
	$cache_file = c_iscached($path,"conf_$prefix");
	$parts = split('/',$path);
	if (@is_file($cf_datadir.$path)) {
		$lfile = array_pop($parts);
	}
	if (!$cache_file) {
		while (count($parts)) {
			$i = implode('/',$parts);
			if(file_exists("$cf_datadir$i/_conf/${type}_$page")) {
				$type.="_$page";
			}
			if ($prefix=='skin') {
				$feed[] = c_parseskin($cf_datadir, "$i/_conf/$type");
				$output[] = "$i/_conf/$type";
			} else {
				$feed[] = c_parse("$i/_conf/$type",$prefix,$cf_format[$type]);
			}
			$part = array_pop($parts);
		}
		$f = array_reverse($feed);
		$cache_file = c_writecache("<?\n".implode('',$f)."\n?>",$path,"conf_$prefix");
  }
  return $cache_file;
}

function c_parse($p,$prefix,$format) {
	global $cf_datadir;
	$back = '';
	if (@is_file($cf_datadir.$p)) {
		$meat = @implode('', file($cf_datadir.$p));
  	$res = ereg("<CONTENU>(.*)</CONTENU>",$meat,$r);
  	$body = split("\n",trim($r[1]));
		$for = split('\.',$format[CONTENU]);
		if ($for[0] == 'm') {
			if ($for[1] == '2') {
				foreach ($body as $a=>$b) {
					$mine = split(":::",$b);
					$back.= "\$".$prefix."_".trim($mine[0])." = '".str_replace("'","\\'",trim($mine[1]))."';\n";
				}
			} elseif ($for[1] == '3') {
				foreach ($body as $a=>$b) {
					$mine = split(":::",$b);
					$back.= "\$".$prefix."_".trim($mine[0])."['".str_replace("'","\\'",trim($mine[1]))."'] = '".str_replace("'","\\'",trim($mine[2]))."';\n";
				}
			} elseif ($for[1] == '4') {
				foreach ($body as $a=>$b) {
					$mine = split(":::",$b);
					if (trim($mine[1]) and trim($mine[2])) {
						$back.= "\$".$prefix."[] = array(".trim($mine[1]).",".trim($mine[2]).");\n";
					}
				}
			}
		}
	}
	//echo "<pre>";die(print_r(get_defined_vars()));
	return $back;
}

function c_parseskin($datadir,$path) {
		if (@is_file($datadir.$path)) {
				$meat = preg_replace("/\n|\r/","",trim(implode('', file($datadir.$path))));
				preg_match_all("|<SKIN_([^>]+)>(.*)</SKIN_\\1>|",$meat,$out);
				for ($i=0; $i< count($out[0]); $i++) {
					if (trim($out[2][$i])) {
						$back.= "\$cm_skin_".$out[1][$i]." =<<<__END__\n".trim($out[2][$i])."\n__END__;\n\n";
					}
				}
		}
	//echo "<pre>";die(print_r(get_defined_vars()));
	return $back;
}

function parse_conf($file,$prefix) {//{{{
  $meat = @implode('', file($file));
  $res = ereg("<CONTENU>(.*)</CONTENU>",$meat,$r);
  $body = split("\n",trim($r[1]));
  foreach ($body as $b) {
      $item = split(":::",$b);
      $left = $right = '';
			
			if (substr($item[0],0,1) == '#') {
			  $left = $right = '';
      } elseif ($item[0] == '*') {
        $left = '$'.$prefix.'[]';
        $right = 'array('.trim($item[1]).','.trim($item[2]).',"'.trim($item[3]).'")';
      } elseif ($item[3]) {
        $left = '$'.$prefix.'_'.strtr($item[0],"/","_").'[]';
        $right = 'array("'.trim(strtr($item[1],"/","_")).'","'.trim($item[2]).'","'.trim($item[3]).'","'.trim($item[4]).'","'.trim($item[5]).'")';
      } elseif ($item[2]) {
        $left = '$'.$prefix.'_'.$item[0].'[]';
        $right = 'array("'.trim($item[1]).'","'.trim($item[2]).'","'.trim($itstr[0]).'")';
      } else {
        $left = '$'.$prefix.'_'.$item[0];
        $right = "'".addslashes(trim($item[1]))."'";
      }
      if ($left and $right) {
        $feed .= $left." = ".$right.";\n";
      }
   unset($itstr);
  }
    return $feed;
}//}}}

function parse_conf_t($file,$prefix) {//{{{
  $meat = @implode('', file($file));
  $res = ereg("<CONTENU>(.*)</CONTENU>",$meat,$r);
  $body = split("\n",trim($r[1]));
  foreach ($body as $b) {
      $item = split(":::",$b);
      $left = $right = $itstr = $itpass = '';
			if (ereg("^[-_\.a-zA-Z0-9]+/[-_\.a-zA-Z0-9]+$",$item[1])) {
				$itstr = split('/',$item[1]);
				$itpass = $itstr[1];
			} else {
				$itpass = $item[1];
			}
      if ($item[0] == '*') {
        $left = '$'.$prefix.'[]';
        $right = 'array('.trim($item[1]).','.trim($item[2]).','.trim($item[3]).')';
      } elseif ($item[3]) {
        $left = '$'.$prefix.'_'.$item[0].'[]';
        $right = 'array("'.trim($itpass).'","'.trim($item[2]).'","'.trim($itstr[0]).'","'.trim($item[3]).'","'.trim($item[4]).'")';
      } elseif ($item[2]) {
        $left = '$'.$prefix.'_'.$item[0].'[]';
        $right = 'array("'.trim($itpass).'","'.trim($item[2]).'","'.trim($itstr[0]).'")';
      } else { 
        $left = '$'.$prefix.'_'.$item[0];
        $right = "'".addslashes(trim($itpass))."'";
      } 
      if ($left and $right) {
        $feed .= $left." = ".$right.";\n";
      } 
  }   
    return $feed;
}//}}}

/* function that reads file */
function cm_getarray($p,$format) {
	global $cf_datadir, $cf_deffile;
	$bk = array();
	if (!@is_file($cf_datadir.$p)) {
		if (@is_file($cf_datadir.$p."/".$cf_deffile)) {
			$p.= "/".$cf_deffile;
		} else {
	    $cm_errmess = "$p not found, ".$cf_datadir.$p."/".$cf_deffile;
				//echo "<pre>";die(print_r(get_defined_vars()));
  		include 'inc/error.php';
		}
	}
	$meat = trim(implode('', file($cf_datadir.$p)));
	foreach ($format as $f) {
	    unset($r);
	    $res = ereg("<$f>(.*)</$f>",$meat,$r);
	    $bk["$f"] = trim($r[1]);
	}
	return $bk;
}

function cm_richtext($text) {
	global $filter;
	if (is_array($filter)) {
		foreach ($filter as $f) {
			// "\n$text\n" au cas ou une pattern se trouve en debut de ligne
			// la fin de ligne est souvent utilisée dans les filtres ....
			if ((substr($f[1],0,1) == "_") and (function_exists("user".$f[1]))) {
				$userfunction = "user".$f[1];
				$text = trim(ereg_replace($f[0],$userfunction(),"\n$text\n"));
			} else {
				$text = trim(ereg_replace($f[0],$f[1],"\n$text\n"));
			}
		}
	}
	return c_sanitize($text);
}

/* function unused for now. useful for automatic publication */
function rebuild_index($path) {
	global $cf_datadir,$mformatidx;
	$query[] = "*";
	$args[] = '-l';
	$ls = c_ls($cf_datadir.$path, '', $query, $args);
	if ($ls) {
		foreach ($ls as $it) {
			$res = '';
			$bit = basename($it);
			if (is_dir($it)) {
				$ourtt[] = "DIR|$bit|\n";
			} elseif (eregi("(\.gif|\.png|\.jpg|\.jpeg)$",$it)) {
				$bb = GetImageSize("$cf_datadir$path/$it");
				$ourtt[] = "IMG|$bit|$bb[3]|\n";
			} else {
				//echo "<pre>";
				//die(print_r(get_defined_vars()));
				$res = cm_getarray("$path/$bit",$mformatidx);
				$ourtt[] = trim($res[DATE])."|".$bit."|".urlencode(trim($res[TITRE]))."|".urlencode(trim($res[AUTEUR]))."|\n";
			}
		}
	} else {
		$ourtt = array();
	}
	if (is_array($ourtt)) {
		sort($ourtt);
		$outp = implode('',$ourtt);
  		$fp = fopen("$cf_datadir$path/.idx","w+");
  		fputs($fp,$outp);

  		fclose($fp);
  		return "liste reconstruite";
	} else {
		return "aucun fichier";
	}
}

function browse_index($path,$limit=0,$range=100) {
	global $cf_datadir;
  $count = 1;
  if (!@is_file("$cf_datadir$path/.idx")) rebuild_index($path);
  $fl = array_reverse(file("$cf_datadir$path/.idx"));
  foreach ($fl as $f) {
		if (($count > $limit) and ($count < ($limit + $range))) {
			unset($line);
			$line = split('\|',$f);
			$item["$line[1]"] = array("date"=>$line[0],"titre"=>urldecode($line[2]),"auteur"=>urldecode($line[3]));
		}
		$count++;
  }
  return $item;
}

/* function unused for now. useful for automatic publication */
function find_last($data,$dossier,$theme,$type='id',$also='') {
	global $cm_datadir;
  if (!is_file("$cf_datadir/$data/$dossier/$theme/.idx")) return '*** no .idx';
  $list = file("$cf_datadir/$data/$dossier/$theme/.idx");
  sort($list);
  end($list);
  $el = split('\|',current($list));
  if ($type == 'id') {
		return $el[0];
  } else {
		return $el;
  }
}

/* function unused for now. useful for automatic publication */
function find_antilast($dossier,$theme,$type='id') {
	global $cf_datadir;
  if (!is_file("$cf_datadir/$data/$dossier/$theme/.idx")) return '*** no .idx';
  $list = file("$cf_datadir/$data/$dossier/$theme/.idx");
	sort($list);
  end($list);
  $el = split('\|',prev($list));
  if ($type == 'id') {
		return $el[0];
  } else {
		return $el;
  }
}

function c_bread($path) {
	global $SCRIPT_NAME;
	$pt = split('/',$path);
	while ($last = array_pop($pt)) {
		$qp = implode('/',$pt)."/$last";
		$out = "<a href=$SCRIPT_NAME?p=".urlencode($qp)." class=bmenu>$last</a>/".$out;
	}
	return "::<a href=$SCRIPT_NAME?p=/ class=bmenu>CCMS</a>/".$out;
}

function c_sanitize($text) {
  $xs = array("/".chr(octdec(223))."/","/".chr(octdec(224))."/","/".chr(octdec(221))."/","/".chr(octdec(222))."/");
  $xr = array("\"","\"","'","'");
  $out = preg_replace($xs, $xr, $text);
  return $out;
}

?>
