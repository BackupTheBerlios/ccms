<? /* $Id: format.php,v 1.1 2003/09/17 12:40:53 terraces Exp $
Copyright (C) 2001,2002, Makina Corpus, http://makinacorpus.org
This file is a component of CCMS <http://makinacorpus.org/index.php/ccms>
Created and maintained by mose <mose@makinacorpus.org>
Released under GPL version 2 or later, see LICENSE file
or http://www.gnu.org/copyleft/gpl.html
*/

// $fw = size pour les input
$fw = 62; $fwa = $fw - 3;

// t = input type=text (x.n : size=n)
// a = textarea (x.n.d : cols=n rows=d)
// - = locked
$cf_specialformat = array( 'css','filter','global','menu','skin');
$cf_specialidx = array( '_conf','_docs','_images' );
$cf_specialdir = array();

$cf_format[0] = array(
		'DATE'       =>'-',
		'AUTEUR'     =>'-',
		'TITRE'      =>"t.$fw",
		'CHAPEAU'    =>"a.$fwa.5",
		'CONTENU'    =>"A.$fwa.20",
		'HTMLTITLE'  =>"t.$fw",
		'DESCRIPTION'=>"a.$fwa.5",
		'MOTSCLE'    =>"t.$fw"
);
$cf_format['css'] = array(
		'DATE'       =>'-',
		'AUTEUR'     =>'-',
		'CONTENU'    =>"m.2.t,t"
);
$cf_format['filter'] = array(
		'DATE'       =>'-',
		'AUTEUR'     =>'-',
		'CONTENU'    =>"m.4.-,t,a,a"
);
$cf_format['global'] = array(
		'DATE'       =>'-',
		'AUTEUR'     =>'-',
		'CONTENU'    =>"m.2.t,t"
);
$cf_format['menu'] = array(
		'DATE'       =>'-',
		'AUTEUR'     =>'-',
		'CONTENU'    =>"m.3.t,t,t"
);
$cf_format['skin'] = array(
		'DATE'       =>'-',
		'AUTEUR'     =>'-',
		'SKIN_head'    =>"a.$fwa.20",
		'SKIN_headerror'    =>"a.$fwa.5",
		'SKIN_dnav'    =>"a.$fwa.5",
		'SKIN_dnavon'    =>"a.$fwa.5",
		'SKIN_nav'    =>"a.$fwa.5",
		'SKIN_navon'    =>"a.$fwa.5",
		'SKIN_tnav'    =>"a.$fwa.5",
		'SKIN_tnavon'    =>"a.$fwa.5",
		'SKIN_lang'    =>"a.$fwa.5",
		'SKIN_langon'    =>"a.$fwa.5",
		'SKIN_show'    =>"a.$fwa.5",
		'SKIN_foot'    =>"a.$fwa.10"
);

/* 
$cf_format[''] = array(
        'DATE'  =>'-',
        'AUTEUR'  =>'-',
        'TITRE'  =>"t.$fw",
        'CONTENU'  =>"A.$fwa.3",
);
*/

$cf_format['_images'] = array();
$cf_formatidx[0] = array( 'DATE', 'TITRE', 'AUTEUR' );
$cf_formatidx['_conf'] = array( 'DATE', 'AUTEUR' );
$cf_formatidx['_images'] = array( 'DATE', 'TITRE', 'SIZE' );
$cf_formatidx['_docs'] = array( 'DATE', 'TITRE', 'AUTEUR' );

$p_file = basename($p);
$type = explode("_",$p_file);

if (in_array($p_file,$cf_specialformat)) {
	$mformat = $cf_format[$p_file];
} elseif (in_array($type[0],$cf_specialformat)) {
	$mformat = $cf_format[$type[0]];
} else {
    $ex = explode("/",$p);
    foreach($ex as $k=>$v) {
        if (in_array($v, $cf_specialdir)) {
            $mformat = $cf_format[$v];
        }
    }
	if (!$mformat) $mformat = $cf_format[0];
}

if (in_array($p_file,$cf_specialidx)) {
	$mformatidx = $cf_formatidx[$p_file];
} else {
	$mformatidx = $cf_formatidx[0];
}

$format = array_keys($mformat);
?>
