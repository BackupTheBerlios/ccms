<? /* $Id: index.php,v 1.1 2003/09/17 12:40:47 terraces Exp $
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

if (is_array($output)) {
	$cm_out = "<tr><td colspan=2 bgcolor=#ffffff class=bmenu>&nbsp;";
	$cm_out.= implode("</td></tr><tr><td colspan=2 class=bmenu bgcolor=#ffffff>&nbsp;",$output);
	$cm_out.= "</td></tr>";
} else {
	$cm_out = '';
}

foreach ($cf_domds as $do) {
	a_rectree($p,"/$do");
}

$cm_head = c_bread($p);
include "html/head.php";
include "html/head_browse.php";
if ((strstr($p,'_images')) or (strstr($p,'_docs'))) {
	include 'inc/image.php';
} else {
	include 'inc/file.php';
}
include "html/foot_browse.php";
include "html/foot.php";
?>

<? /* 
<pre><?print_r(get_defined_vars())?></pre> 
*/ ?>
