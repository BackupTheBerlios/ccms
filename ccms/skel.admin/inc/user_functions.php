<?/* $Id: user_functions.php,v 1.1 2003/09/17 12:40:54 terraces Exp $ 
Copyright (C) 2001,2002, Makina Corpus, http://makinacorpus.org
This file is a component of CCMS <http://makinacorpus.org/index.php/ccms>
Created and maintained by mose <mose@makinacorpus.org>
Released under GPL version 2 or later see LICENSE file
or http://www.gnu.org/copyleft/gpl.html
*/

function user_makdnav($item) {
	global $SCRIPT_NAME,$myp,$domdir;
	$menu = $GLOBALS["menu_$item"];
	if (is_array($menu)) {
		foreach($menu as $k => $v) {
			if (substr($k,0,4) == 'http') {
				$GLOBALS['cm_urlpage'] = "$k";
			} else {
				$GLOBALS['cm_urlpage'] = "$SCRIPT_NAME/$k";
			}
			$GLOBALS['cm_intpage'] = "$v";
			$current = $myp[0];	
			if (($k == implode('/',$myp) || ($k == $myp[0]))) {
				$back .= c_xinc($item."on");
			} else {
				$back .= c_xinc($item);
			}
		}
	}
	return $back;
}

function user_nav($item) {
	global $SCRIPT_NAME, $myp;
	$menu = $GLOBALS["menu_$item"];
	$back = "";
	if (is_array($menu)) {
		foreach ($menu as $k => $v) {
            if (substr($k,-3)=='php') {
                $GLOBALS['cm_urlnav'] = "/$k"; 
            }
		    else {
                $GLOBALS['cm_urlnav'] = "$SCRIPT_NAME/$k";
            }
			$GLOBALS['cm_labelnav'] = "$v";
			if ($k == implode('/',$myp)) {
				$back.= c_xinc("navon");
			} elseif ($k == $myp[0]) {
				$back.= c_xinc("navon"); 
			} else {
				$back.= c_xinc("nav");
			}
		}
	}
	return $back;
}

function user_maknav($item) {
	global $SCRIPT_NAME,$myp,$domdir;
	$menu = $GLOBALS["menu_$item"];
	if (is_array($menu)) {
		foreach($menu as $k => $v) {
			if (substr($k,0,4) == 'http') {
				$GLOBALS['cm_urlpage'] = "$k";
			} else {
				$GLOBALS['cm_urlpage'] = "$SCRIPT_NAME/$k";
			}
			$GLOBALS['cm_intpage'] = "$v";
			$s = sizeof($e = explode('/', $k))-1;
			$GLOBALS['cm_menuimg'] = $e[$s]."_0.png";
			$back .= c_xinc($item);
		}
	}
	return $back;
}

function user_maktopnav($item) {
	global $SCRIPT_NAME,$myp,$domdir;
	$menu = $GLOBALS["menu_$item"];
	if (is_array($menu)) {
		foreach($menu as $k => $v) {
			if (substr($k,0,4) == 'http') {
				$GLOBALS['cm_urlpage'] = "$k";
			} else {
				$GLOBALS['cm_urlpage'] = "$SCRIPT_NAME/$k";
			}
			$GLOBALS['cm_intpage'] = "$v";
			$current = $myp[0];					
			$s = sizeof($e = explode('/', $k))-1;
			if (($k == implode('/',$myp) || ($k == $myp[0]))) {
				$GLOBALS['cm_menuimg'] = $e[$s]."_1.png";
				$back .= c_xinc($item."on");
			} else {
				$GLOBALS['cm_menuimg'] = $e[$s]."_0.png";
				$back .= c_xinc($item);
			}
		}
	}
	return $back;
}

function user_lang() {
	global $PHP_SELF,$myp,$langdir,$rhhost,$area;
	$lang = $GLOBALS["cf_langds"];
	$current = $myp[0];
	if (is_array($lang)) {
		foreach($lang as $k => $v) {
			$GLOBALS['cm_lg'] = "$v";
			if ($current != basename($PHP_SELF)) $mytmp = $myp[0].'/';
			if (!empty($area)) $tmparea = '.'.$area;
			if (basename($PHP_SELF) != 'index.php') $myp2 = basename($PHP_SELF);
			$GLOBALS['cm_urlpage'] = "http://$v$tmparea.$rhhost"."/index.php/".$mytmp.$myp2;
			if ($v == $langdir) {
				$back .= c_xinc("langon");
			} else {
				$back .= c_xinc("lang");
			}
			unset($mytmp);
		}
	}
	return $back;
}

function user_lnav() {
	global $SCRIPT_NAME,$myp,$langdir,$PHP_SELF;
	$GLOBALS['cm_loc'] = "";
	$current = $myp[0];
	if (!empty($GLOBALS["menu_$current"])) {
		$GLOBALS['cm_loc'] = $GLOBALS['cm_locurl'] = $myp[0];
		$page = basename($PHP_SELF);
		if (($page != $myp[0]) && ($GLOBALS['cm_loc'] != 'index')) {
			$GLOBALS['cm_currentfile'] = $page;
		} else {
			$GLOBALS['cm_currentfile'] = $page;
			$GLOBALS['cm_loc'] = 'home';
		}
	} else {
		$GLOBALS['cm_loc'] = 'home';
		$GLOBALS['cm_locurl'] = 'index';
		$GLOBALS['cm_currentfile'] = $myp[0];
	}
	$back .= c_xinc("lnav");
	return $back;
}
/*
echo "<pre>";
print_r(get_defined_vars())
echo "</pre>";
*/
?>
