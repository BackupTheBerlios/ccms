<?/* $Id: index.php,v 1.1 2003/09/17 12:40:47 terraces Exp $
Copyright (C) 2001, Makina Corpus, http://makinacorpus.org
Created and maintained by mose <mose@makinacorpus.org>
Released under GPL version 2 or later, see LICENSE file
or http://www.gnu.org/copyleft/gpl.html
*/

include 'inc/conf.php';

if (!c_iscached($p,$cm_q)) {
	$page = cm_getarray($p,$format);
	$page[CONTENU] = cm_richtext($page[CONTENU]);
	$feed = c_inc('head');
    $feed.= c_inc('show');
	$feed.= c_inc('foot');
	c_writecache($feed,$p,$cm_q);
}
c_readcache($p,$cm_q);
?>
<?/* 
<pre><?print_r(get_defined_vars())?></pre> 
*/?>

