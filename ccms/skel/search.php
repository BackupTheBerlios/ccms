<?/* $Id: search.php,v 1.1 2003/09/17 12:40:47 terraces Exp $
Copyright (C) 2001, Makina Corpus, http://makinacorpus.org
Created and maintained by mose <mose@makinacorpus.org>
Released under GPL version 2 or later, see LICENSE file
or http://www.gnu.org/copyleft/gpl.html
*/

include 'inc/conf.php';

function search($terms,$start=0,$numres=10) {
    global $p,$cf_datadir,$domdir,$langdir,$cm_maildomain,$cf_testroot,$HTTP_HOST, $cm_nosearch, $cm_numsearch;
    $ex = explode('/',$p);
    $cgi = "$HTTP_HOST/cgi-bin/parse.pl?terms=$terms&&start=$start&&lng=$ex[2]&&domain=$ex[1]";
    if (substr($cgi,0,4)!='http') {
		$cgi="http://$cgi";
	}
    $fp = fopen($cgi,"r");
    while(!feof($fp)) {
		$data.= fgets($fp,4096);
	}
    trim($data);
    if (empty($data)) {
		$feed = $cm_nosearch;
	} else {
        $res = explode("\n",$data);
        $total = array_shift($res);
        array_pop($res);
        $feed.="<b>$total $cm_numsearch:</b>";
        $res = array_slice($res,0,$numres);
        foreach($res as $r) {
			$tmp = explode("+++",$r);
            $infos = explode("---",$tmp[0]);
            list($domain,$lang,$fname) = $infos;
            $file = "$cf_testroot/$domain/$cf_datadir/$domain/$lang/$fname";
            $fp = @fopen($file,"r");
            $contenu = @fread($fp,filesize($file));
            ereg("<TITRE>(.*)</TITRE>",$contenu,$title);
            ereg("<CHAPEAU>(.*)</CHAPEAU>",$contenu,$header);
            $feed.="<br><br><b>$title[0] [<a href='index.php$fname'>$fname</a>]</b><br>$header[0]";
			$compteur++;
        }
    }
	$compteur++;
    $feed.= "<div align=right>";
    for($i=0;$i<$total;$i+=$numres) {
		$n++;
		$u=i+1;
	    $feed.= "<a href=\"/search.php?terms=$terms&&numres=$numres&&start=$u\"><b>$n</b></a>&nbsp;&nbsp;";
    }
    $feed.="&nbsp&nbsp;</div align=right>";
	return $feed;
}

$SCRIPT_NAME='/index.php';
echo c_inc('head');
if($_POST) {
    $terms = urlencode($out["'terms'"]);
    $start = $out["'start'"];
    $numres = $out["'num'"];
}
echo search($terms, $start, $numres);
echo c_inc('foot');

?>
<?/* 
<pre><?print_r(get_defined_vars())?></pre> 
*/?>

