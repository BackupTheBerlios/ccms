<?/* $Id: conf.php,v 1.1 2003/09/17 12:40:52 terraces Exp $
Copyright (C) 2001,2002, Makina Corpus, http://makinacorpus.org
This file is a component of CCMS <http://makinacorpus.org/index.php/ccms>
Created and maintained by mose <mose@makinacorpus.org>
Released under GPL version 2 or later, see LICENSE file
or http://www.gnu.org/copyleft/gpl.html
*/
include 'inc/params.php';
define(NOCACHE,TRUE);
$myp = array();
$hhost = split('\.',$HTTP_HOST);

if (in_array($hhost[0],$cf_langs)) {
	$langdir = $cf_langdirs["$hhost[0]"];
	array_shift($hhost);
}

if ($hhost[0] == $cf_adminprefix) {
	$cm_datadir = 'datadmin';
	array_shift($hhost);
	$area = 'admin';
} else {
	if  ($hhost[0] == $cf_testprefix) {
		array_shift($hhost);		
		$area = 'test';
	}
	$cm_datadir = $cf_datadir;
}

$rhhost = implode('.',$hhost);

if (in_array($rhhost,$cf_doms)) {
	$domdir = $cf_domdirs["$rhhost"];
}

if ($PATH_INFO) {
	$myp = split('/',$PATH_INFO);
	array_shift($myp);
	if (!$langdir) {
		if (in_array($myp[0],$cf_langs)) {
			$langdir = array_shift($myp);
		} else {
			$langdir = $cf_langdirs['-'];
		}
	}
} else {
	if (!$langdir) {
		$langdir = $cf_langdirs['-'];
	}
}

if (!$domdir) {
	$domdir = current($cf_domdirs);
}

$cm_parent = strtoupper($myp[0]);

$zp = array_merge(array("$domdir","$langdir"),$myp);
$it = '/'.implode('/',$zp);

// misc var settings
$cm_images = "/images$cm_lang";  // directory where are images
if ($area != 'admin') {
	$p = $it;
}


include 'inc/format.php';

// get main lib engine
include 'inc/lib.php';

// build local conf files
if ($area == 'admin') {
	//echo c_parseskin($cm_datadir);
	if (!$p) {
		$p = "/";
	}
	if ($action) {
	}
} else {
    global $PHP_SELF;
	$e = explode('/', $PHP_SELF);
	$f = explode('.', $e[1]);
	include c_buildconf($it,"skin","skin",$f[0]);
	include c_buildconf($it,"global","cm",$f[0]);
	include c_buildconf($it,"menu","menu",$f[0]);
	include c_buildconf($it,"filter","filter",$f[0]);
	include c_buildconf($it,"contact","skin",$f[0]);
	include c_buildconf($it,"search","skin",$f[0]);

}
$np = split('~',$p);
if (count($np) > 2) {
	$lm_author = array_pop($np);
	$lm_date = array_pop($np);
}
$p = implode('',$np);

//echo "<pre>";die(print_r(get_defined_vars()));
//die("");
// conditionnal inclusion of javascript in skin
/*
if (@is_file("$cm_datadir/.js.$p")) {
  $cm_plugjs = implode('',file("$cm_datadir/.js.$p"));
}
if (@is_file("$cm_datadir/conf/css")) {
  $cm_plugcss = implode('',file("$cm_datadir/conf/css"));
}
*/
?>
