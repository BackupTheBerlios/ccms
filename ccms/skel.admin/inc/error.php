<?/* $Id: error.php,v 1.1 2003/09/17 12:40:52 terraces Exp $
Copyright (C) 2001,2002, Makina Corpus, http://makinacorpus.org
This file is a component of CCMS <http://makinacorpus.org/index.php/ccms>
Created and Maintained by mose <mose@makinacorpus.org>
Released under GPL version 2 or later, see LICENSE file
or http://www.gnu.org/copyleft/gpl.html
*/
?>
<table width=100% height=100% border=0>
<tr><td align=center valign=middle>
<h2>¤ <font color=#990000>error</font> ¤</h2>
<h3><? echo $cm_errmess ?></h1>
</td></tr></table>
<? 
echo "<pre>";
die(print_r(get_defined_vars())."</pre>");
exit; ?>
