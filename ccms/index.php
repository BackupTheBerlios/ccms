<? /* $Id: index.php,v 1.2 2003/10/01 11:32:48 terraces Exp $
Copyright (C) 2001, 2002, Makina Corpus, http://makinacorpus.org
This file is a componenet of CCMS <http://makinacorpus.org/index.php/ccms>
Created and maintained by mose (mose@makinacorpus.org)
Released under GPL version 2 or later, see LICENSE file
or http://www.gnu.org/copyleft/gpl.html
*/

if (!$l) $l='fr';
include("index.$l.php");

$bad = 0;

// If you want a real custom installation, edit those vars
$uid = "www-data";
$admindir = "admin";
$testdir = "test";
$cachedir = "cache";
$datadir = "data";
$navtype = "nav";
$deffile = "index";
$ccms = "CCMS";
$uploadlimit = 512000;
$nocache = 1;
$cips="127.0.0.1 = allow"; // allow/deny back office access

$ccmsver="0.2b3";

function buildlang($l) {
	global $PHP_SELF;
	foreach(array('fr','en') as $v) {
		$out.="&nbsp;<a href=$PHP_SELF?l=$v><img src='skel.admin/picto/$v.png' border='1' ";
		if ($l==$v) $out.= "width=20>";
		else $out.= "width=15>";
		$out.="</a>";
	}
	return $out;

}

function isIP($ip) {
    $ex = explode('.',$ip);
    if (count($ex)!=4) return false;
    foreach($ex as $i) {
        if (!is_numeric($i)) return false;
    }
    return true; 
}

function isSingle($word) {
    return $word!=='' && count(explode(' ', $word)==1);
}

function isCplx($lines) {
    $ok=1;
    foreach(explode("\n",$lines) as $ex) {
			if ($ex!="") {
            	$ex=explode("=",$ex);
	            if (count($ex)!=2) $ok=0;
			}
    }
    return $ok==1;
}

function wFile($content, $file) {
    $fp = fopen($file, 'w');
    fwrite($fp, $content);
    fclose($fp); 
}

if ($step==1) {
    if (@touch("./etc/foo")==FALSE) { 
   	    $out[] = "$no_rights (".getcwd()."/etc).";
    	$bad = 1;
  	} else {
        unlink("./etc/foo");
    }
    if (!file_exists($cpath) && !mkdir($cpath)) {
   	    $out[] = "$no_path $cpath";
  	} else {
        if (@touch("$cpath/foo")==FALSE) { 
       	    $out[] = "$no_rights $cpath";
        	$bad = 1;
      	} else {
            unlink("$cpath/foo");
        }
    }
    if (!isIP($cip)) {
   		$out[] = $err_ip;
   		$bad = 1;
    } 
    if (!isSingle($cname)) {
   		$out[] = $err_name;
   		$bad = 1;
    } 
    if (file_exists("$cpath/$cname")) { 
   	    $out[] = "$tpath $cpath/$cname $already";
    	$bad = 1;
  	}
    if (!isSingle($cmaildom)) {
   		$out[] = $err_domain;
   		$bad = 1;
    } 
    if (!$clangs || !isCplx($clangs)) {
   		$out[] = $err_lang;
   		$bad = 1;
    } 
    if (!$cdomains || !isCplx($cdomains)) {
   		$out[] = $err_doms;
   		$bad = 1;
    }
    if (!isSingle($tpasswd)) {
    	$out[] = $err_tpass;
        $bad = 1;
    }
    if ($tpasswd!=$tvpasswd) {
  		$out[] = $err_tpass;
        $bad = 1;
    }
} elseif ($step==2) {
    if ($auth=='file') {
        if (!isSingle($plogin)) {
      		$out[] = $err_admlog;
            $bad = 1;
        }
        if (!isSingle($ppasswd)) {
        	$out[] = $err_admpass;
            $bad = 1;
        }
        if ($ppasswd!=$pvpasswd) {
      		$out[] = $err_pass;
            $bad = 1;
        }
    } elseif ($auth=='mysql') {
        if (!isSingle($mserver)) {
       		$out[] = $err_sql;
            $bad = 1;
        }
        if (!isSingle($mdbname)) {
      		$out[] = $err_admlog;
            $bad = 1;
        }
        if (!isSingle($mlogin)) {
      		$out[] = $err_sqllog;
            $bad = 1;
        }
        if (!isSingle($mpasswd)) {
      		$out[] = $err_sqlpass;
            $bad = 1;
        }
        if (!isSingle($bmlogin)) {
      		$out[] = $err_admlog;
            $bad = 1;
        }
        if (!isSingle($bmpasswd)) {
        	$out[] = $err_admpass;
            $bad = 1;
        }
        if ($bmpasswd!=$bmvpasswd) {
      		$out[] = $err_pass;
            $bad = 1;
        }
    } elseif ($auth=='ldap') {
        if (!isSingle($lhost)) {
            $out[] = $err_ldap;
            $bad = 1;
        }
        if (!isSingle($lport)) {
     		$out[] = $err_ldport;
            $bad = 1;
        }
        if (!isSingle($lbasebind1) || !isSingle($lbasebind1)) {
      		$out[] = $err_ldbase;
            $bad = 1;
        }
        if (!isSingle($lattr1) || !isSingle($lattr2)) {
      		$out[] = $err_ldatt;
            $bad = 1;
        }
        if (!isSingle($lgroupbase)) {
      		$out[] = $err_ldgbase;
            $bad = 1;
        }
        if (!isSingle($lgroup)) {
      		$out[] = $err_ldgrp;
            $bad = 1;
        }
        if (!isSingle($lbasedn)) {
     		$out[] = $err_ldbdn;
            $bad = 1;
        }
    }
    if (!$bad) {
        // Writing apache configuration files
        $webroot = $cpath."/".$cname;
        $adminroot = $webroot.".".$admindir;
        $etcroot = $webroot.".etc";
        $logroot = $webroot.".logs";
        $testroot = $webroot.".".$testdir;
        $cgiroot = $cpath."/cgi-bin";

		$htpasswd = exec("which htpasswd");
		$virt = $virtssl = $cip;

        foreach(explode("\n",$clangs) as $lgs) {
            $lg=explode("=",$lgs);
            $langs[trim($lg[1])] = trim($lg[0]);
        }
        foreach(explode("\n",$cdomains) as $doms) {
            $dom=explode("=",$doms);
            $domains[trim($dom[1])] = trim($dom[0]);
        }
      	foreach(explode("\n",$cips) as $ip) {
            $i=explode("=",$ip);
            $ips[trim($i[0])] = trim($i[1]);
        }

        foreach($domains as $dom=>$v) {
            $doms = explode("/",$v);
            $apache .= <<<_END_
<Directory $webroot/$d>
Options FollowSymlinks
order allow,deny
allow from all
</Directory>\n\n
_END_;
            $apachet .=<<<_END_
<Directory $testroot/$dom>
Options FollowSymlinks
AuthType basic
AuthName $testdir.ccms
AuthUserFile $etcroot/back.auth
Satisfy Any
order deny,allow
deny from all\n
_END_;
			foreach($ips as $k => $v) {
				if ($v!="") $apachet.="$v from $k\n";
            }
            $apachet .="require valid-user\n</Directory>\n\n";
            $apachea .= "<VirtualHost $virtssl>\n";
            $apachea .= "ServerName $admindir.$doms[0]\n";
            foreach($langs as $k => $v) {
                $lgs = explode("/",$v);
                $apache .= "<VirtualHost $virt>\n"; 
                $apachet .= "<VirtualHost $virt>\n"; 
                if($lgs[0]=="-") {
                    array_shift($lgs);
                    $apache .= "ServerName $doms[0]\n";
                    $apachet .= "ServerName $testdir.$doms[0]\n";
					$hosts .= "\n$virt ";
					$hosts .= "$doms[0] ";
					$hosts .= "$testdir.$doms[0] $admindir.$doms[0]\n";
                } else {
                    $apache .= "ServerName $lgs[0].$doms[0]\n";
                    $apachet .= "ServerName $lgs[0].$testdir.$doms[0]\n";
 					$hosts .= "\n$virt ";
					$hosts .= "$lgs[0].$doms[0] ";
					$hosts .= "$lgs[0].$testdir.$doms[0] $lgs[0].$admindir.$doms[0]\n";
				}
                foreach($lgs as $l) {
                    foreach($doms as $d) {
						$apache .= "ServerAlias $l.$d\n";
						$apachet .= "ServerAlias $l.test.$d\n";
						$apachea .= "ServerAlias $l.admin.$d\n";
						$hosts .= "\n$virt ";
						$hosts .= "$l.$d ";
						$hosts .= "$l.$testdir.$d $l.$admindir.$d\n";
                    }
                }
                $apache .= "DocumentRoot $webroot/$dom\n";
                $apachet .= "DocumentRoot $testroot/$dom\n";
                $apache .= "ScriptAlias /cgi-bin/ $cgiroot/\n";
                $apachet .= "ScriptAlias /cgi-bin/ $cgiroot/\n";
                $apache .= "ServerAdmin webmaster@$doms[0]\n";
                $apachet .= "ServerAdmin webmaster@$doms[0]\n";
                $apache .= "ErrorLog $logroot/$doms[0]/$k/error_log\n";
                $apachet .= "ErrorLog $logroot/$testdir/error_log\n";
                $apache .= "CustomLog $logroot/$doms[0]/$k/access_log combined\n";
                $apachet .= "CustomLog $logroot/$testdir/access_log combined\n";
                $apache .= "php_value include_path .:$adminroot\n";
                $apachet .= "php_value include_path .:$adminroot\n";
                $apache .= "</VirtualHost>\n\n";
                $apachet .= "</VirtualHost>\n\n";
            }

            $apachea .=<<<_END_
DocumentRoot $adminroot
ScriptAlias /cgi-bin/ $cgiroot/
ServerAdmin webmaster@$doms[0]
ErrorLog $logroot/$admindir/error_log
CustomLog $logroot/$admindir/access_log combined
</VirtualHost>\n\n
_END_;
}
            $apachea .=<<<_END_
<Directory $adminroot>
Options FollowSymLinks
AuthType basic
AuthName $admindir.ccms
AuthUserFile $etcroot/back.auth
Satisfy Any
order deny,allow
deny from all\n
_END_;
            foreach($ips as $k => $v) {
                if ($v!="") $apachea.="$v from $k\n";
            }
            $apachea .=<<<_END_
require valid-user
</Directory>\n
<Directory $etcroot>
Options None
order deny,allow
deny from all
</Directory>\n
<Directory $logroot>
Options None
order deny,allow
deny from all
</Directory>\n
_END_;
        // Writing params file
        $params="<?\n";
        foreach($langs as $k => $v) {
            $lgs = explode("/",$v);
            $params.="\$cf_langds[] = '$k';\n";
            foreach($lgs as $lg) {
                $params.="\$cf_langs[] = '$lg';\n";
                $params.="\$cf_langdirs['$lg'] = '$k';\n";
            }
        }
        foreach($domains as $k => $v) {
            $dms = explode("/",$v);
            $params.="\$cf_domds[] = '$k';\n";
            foreach($dms as $dm) {
                $params.="\$cf_doms[] = '$dm';\n";
                $params.="\$cf_domdirs['$dm'] = '$k';\n";
            	$adminurl = $dm;
			}
        }
        $params.=<<<_END_
\$cf_etcroot = '$etcroot';
\$cf_adminroot = '$adminroot';
\$cf_adminprefix = '$admindir';
\$cf_testroot = '$testroot';
\$cf_webroot = '$webroot';
\$cf_cgibin = '$cgiroot';
\$cf_testprefix = '$testdir';
\$cf_uploadlimit = '$uploadlimit';
\$cm_cachedir = '$cachedir';
\$cf_datadir = '$datadir';
\$cf_deffile = '$deffile';
\$cm_logdir = '$logroot';
\$cm_etcdir = '$etcroot';
\$cm_navtype = '$navtype';
\$cm_ccmsver = '$ccmsver';
\$cm_ccms = '$ccms';
\$cm_maildomain = '$cmaildom';
\$cm_adminurl = 'admin.$adminurl';\n
_END_;
        if($auth=='ldap') {
            $params.=<<<_END_
\$ldap_host = '$lhost';
\$ldap_port = '$lport';
\$ldap_basebind1 = '$lbasebind1';
\$ldap_basebind2 = '$lbasebind2';
\$ldap_attr1 = '$lattr1';
\$ldap_attr2 = '$lattr2';
\$ldap_groupbase = '$lgroupebase';
\$ldap_group = '$lgroup';
\$ldap_basedn = '$lbasedn';\n
_END_;
        } elseif($auth=='mysql') {
            $params.=<<<_END_
\$mysql_host = '$mhost';
\$mysql_dbname = '$mdbname';
\$mysql_login = '$mlogin';
\$mysql_passd = '$mpasswd';\n
_END_;
        }
        $params.="define(NOCACHE, $nocache)\n?>";
        wFile($params, "./etc/params.php");
        wFile($apachea, "./etc/apache-admin.conf");
        wFile($apachet, "./etc/apache-test.conf");
        wFile($apache, "./etc/apache.conf");
       	wFile($hosts, "./etc/hosts");

        $res[] = "$ap_inc1 $etcroot $ap_inc2";
        $res[] = "$ap_inc3 $cip' $ap_inc2";
        $res[] = "$ht_inc1 $etcroot$ht_inc2";
        $res[] = "$ht_inc3 $tlogin/$tpasswd";

        // Install admin
        $dirs=array("","cache","html","inc","help","picto");
        foreach($dirs as $dir) {
        	mkdir("$adminroot/$dir", 0750);
        	if(is_dir("./skel.admin/$dir")) {
    			$dp = opendir("./skel.admin/$dir");
                while (($f = readdir($dp))!=false) {
                    if(!is_dir("./skel.admin/$dir/$f")) {
                        copy("./skel.admin/$dir/$f","$adminroot/$dir/$f");
	  	            }
                }
	        }
        }
        copy("./etc/params.php","$adminroot/inc/params.php");
        symlink("$adminroot/inc/auth.$auth.php", "$adminroot/inc/auth.php");
        if ($auth=='file') {
            exec("$htpasswd -bc ./etc/users $plogin $ppasswd");
            $res[] = "$mod_file htpasswd $etcroot/users"; 
        } elseif ($auth=='mysql') {
            $db_link = mysql_connect($mhost, $mlogin, $mpasswd);
            if (!($db_link && mysql_select_db($mdbname))) {
	           die("$no_db $mysql_error");
            }
            $create = "CREATE TABLE users (login  varchar(16) NOT NULL, passwd varchar(16) default NULL, flag tinyint(2) NOT NULL default '0', PRIMARY KEY  (login));";
            $pass = crypt($bmpasswd,substr($bmpasswd,0,2));
            $populate = "INSERT INTO users (`login`, `passwd`, `flag`) values ('$bmlogin','$pass', 1);";
            $result = mysql_query($create, $db_link);
            $result = mysql_query($populate, $db_link);
            mysql_close($db_link);  
        } elseif ($auth=='ldap') {
            $res[] = $set_ldap;
        }

        // Install admin-data
        $datadir = "$adminroot/data";
        $dataroot = "./skel.admin/data";
        $confroot='./skel.admin/data/conf';
        $imgroot='./skel.admin/data/images';
        $dirs=array('','_conf','_images','_docs');
        $files=array('skin','filter','global');
		if (file_exists($datadir)) {
            $date = time();
            rename($datadir,"${datadir}_$date");
        }
        mkdir($datadir, 0750);
        foreach($domains as $dom=>$v) {
            foreach($dirs as $dir) {
                mkdir("$datadir/$dom/$dir", 0750);
            }        
            foreach($files as $file) {
                copy("$confroot/$file","$datadir/$dom/_conf/$file");
            }
            $dp = opendir($imgroot);
            while (($f = readdir($dp))!=false) {
                if(!is_dir("$imgroot/$f")) {
                    copy("$imgroot/$f","$datadir/$dom/_images/$f");
                }
            }
            foreach($langs as $lang=>$v) {
                foreach($dirs as $dir) {
                    mkdir("$datadir/$dom/$lang/$dir", 0750);
                } 
                foreach(array('global','menu') as $file) {
                    copy("$confroot/LANG/$file", "$datadir/$dom/$lang/_conf/$file");
                }
                copy("$dataroot/TEMPLATE", "$datadir/$dom/$lang/$deffile");
                copy("$dataroot/contact", "$datadir/$dom/$lang/contact");
                copy("$dataroot/search", "$datadir/$dom/$lang/search");
   	        }  
        }
		
        // Install test web
        mkdir($testroot, 0750);
        $dirs=array('','cache','data');
        foreach($domains as $dom=>$v) {
			foreach($dirs as $dir) {
	            mkdir("$testroot/$dom/$dir", 0750);
			}
		    foreach(array('index.php','contact.php','search.php') as $f) {
                copy("./skel/$f","$testroot/$dom/$f");
			}
            symlink("$adminroot/data/$dom/_images", "$testroot/$dom/img");
			symlink("$adminroot/data/$dom/_images", "$testroot/$dom/images");
			symlink("$adminroot/data/$dom/_docs", "$testroot/$dom/docs");
		    foreach($langs as $lang=>$v) {
				symlink("$adminroot/data/$dom/$lang/_images", "$testroot/$dom/images$lang");
				symlink("$adminroot/data/$dom/$lang/_docs", "$testroot/$dom/docs$lang");
			}
			symlink("$adminroot/data/$dom", "$testroot/$dom/data/$dom");
		}

        // Intall web
		mkdir($webroot, 0750);
        foreach($domains as $dom=>$v) {
			$dirs=array('','cache','data');
	        foreach($dirs as $dir) {
	            mkdir("$webroot/$dom/$dir", 0750);
		    }
 		    foreach(array('index.php','contact.php','search.php') as $f) {
                copy("./skel/$f","$webroot/$dom/$f");
			}
			symlink("$adminroot/data/$dom/_images", "$webroot/$dom/img");
			symlink("$adminroot/data/$dom/_images", "$webroot/$dom/images");
			symlink("$adminroot/data/$dom/_docs", "$webroot/$dom/docs");
		    foreach($langs as $lang=>$v) {
				symlink("$adminroot/data/$dom/$lang/_images", "$webroot/$dom/images$lang");
				symlink("$adminroot/data/$dom/$lang/_docs", "$webroot/$dom/docs$lang");
			}

		}
		
		// Install web data
        $dirs=array('','_conf','_images','_docs');
        $files=array('skin','filter','global');
        foreach($domains as $dom=>$v) {
			$datadir="$webroot/$dom/data/";
			foreach($dirs as $dir) {
	            mkdir("$datadir/$dom/$dir", 0750);
	        }        
	        foreach($files as $file) {
	            copy("$confroot/$file","$datadir/$dom/_conf/$file");
	        }
			$dp = opendir($imgroot);
	        while (($f = readdir($dp))!=false) {
	            if(!is_dir("$imgroot/$f")) {
	                copy("$imgroot/$f","$datadir/$dom/_images/$f");
	            }
			}
			foreach($langs as $lang=>$v) {
	            foreach($dirs as $dir) {
	                mkdir("$datadir/$dom/$lang/$dir", 0750);
	            } 
	            foreach(array('global','menu') as $file) {
	                copy("$confroot/LANG/$file", "$datadir/$dom/$lang/_conf/$file");
	            }
                copy("$dataroot/TEMPLATE", "$datadir/$dom/$lang/$deffile");
                copy("$dataroot/contact", "$datadir/$dom/$lang/contact");
                copy("$dataroot/search", "$datadir/$dom/$lang/search");
            }   
		}

		// Install etc
		mkdir($etcroot, 0750);		
        exec("$htpasswd -bc ./etc/back.auth $tlogin $tpasswd");
		$files=array("apache.conf","apache-admin.conf","apache-test.conf","back.auth","hosts");
		foreach($files as $file) {
			copy("./etc/$file","$etcroot/$file");
		}
        if (file_exists("./etc/users")) {
            copy("./etc/users","$etcroot/users");
        }

		// Install logs
        $dirs=array("","$testdir","$admindir");
        foreach($dirs as $dir) {
       		mkdir("$logroot/$dir", 0750);
        }
        foreach($domains as $dom=>$v) {
            $doms = explode("/",$v);
  			mkdir("$logroot/$doms[0]", 0750);
	        foreach($langs as $lang=>$v) {
				mkdir("$logroot/$doms[0]/$lang", 0750);
			}
		}
        
        // Configure and install search engine
        @mkdir($cgiroot,0750);
        foreach($domains as $dom=>$v) {
            foreach($langs as $lang=>$v) {
                @mkdir($cgiroot."/dbs".$dom.$lang,0750);
            }
        }
        foreach(array("index.pl","parse.pl") as $file) {
            copy("./skel.admin/search/".$file,$cgiroot."/".$file);
        }  
        $res[] = $first_idx;
    }
}

if(!$bad) $step++;
if (!$step) $step=1; 

?>
<html>
<head>
<title>CCMS - web install (<? echo $step; ?>)</title>
</head>
<body bgcolor=#ffffff link=#CC6600 alink=#FFFF66 vlink=#CC3300>

<table width=100% height=100% border=0>
<tr><td align=center valign=middle>

<form method=post>
<input type=hidden name=step value=<? echo $step; ?>>
<table cellpadding=1 cellspacing=0 border=0 width=450>
<tr><td bgcolor=#445566>
<table width=100% cellpadding=1 cellspacing=1 border=0>
<tr><td bgcolor=#FFFFFF align=center>
<div style="color:#223344;font-size:200%;font-weight:bold;padding-top:2;">
<? echo "CCMS $ccmsver ::: $h_head ($step)"; if($step==1) echo buildlang($l); ?> 
</td></tr>
<tr><td align=center>
<div style="color:#ffffff;font-weight:bold;padding-bottom:1;"><? echo $msg[$step]; ?></div>

<? if (is_array($out)) { ?> 

</td></tr>
<tr><td bgcolor=#e6be67 valign=top align=left>
<div style="font-weight:bold;color:#223344;margin:2 10 2 10;"><li><? echo implode("\n<li>",$out); ?></div>
</td></tr>
<tr><td bgcolor=#445566 align=center>

<? } if ($step==1) { ?>

<table cellpadding=5 cellspacing=0 border=0 width=100%>
<tr><td bgcolor=#bdbdbd align=right valign=middle>
<table width=100% cellpadding=0 cellspacing=0 border=0>
<tr><td bgcolor=#445566>
<img src=skel.admin/picto/0.png width=1 height=2 hspace=0 vspace=0 border=0 alt=""><br>
</td></tr></table>
</td>
<td bgcolor=#bdbdbd valign=middle align=left>
<div style="font-weight:bold;"><? echo $h_fs; ?></div>
</td></tr>

<tr><td bgcolor=#dedede valign=top align=right>
<b><? echo $h_path; ?></b><br>
<font size=1><? echo $hd_path; ?><br></font>
</td>
<td bgcolor=#dedede valign=top align=left>
<input type=text name=cpath value='<? echo ($cpath)?$cpath:"/var/www/ccmstest"; ?>' size=20>
</td></tr>

<tr><td bgcolor=#dedede valign=top align=right>
<b><? echo $h_cname; ?></b><br>
<font size=1><? echo $hd_cname; ?><br></font>
</td>
<td bgcolor=#dedede valign=top align=left>
<input type=text name=cname value='<? echo ($cname)?$cname:"ccms"; ?>' size=20>
</td></tr>

<tr><td bgcolor=#dedede valign=top align=right>
<b><? echo $h_dname; ?></b><br>
<font size=1><? echo $hd_dname; ?><br></font>
</td>
<td bgcolor=#dedede valign=top align=left>
<input type=text name=cmaildom value='<? echo ($cmaildom)?$cmaildom:"domain.com"; ?>' size=20>
</td></tr>

<tr><td bgcolor=#bdbdbd align=right valign=middle>
<table width=100% cellpadding=0 cellspacing=0 border=0>
<tr><td bgcolor=#445566>
<img src=skel.admin/picto/0.png width=1 height=2 hspace=0 vspace=0 border=0 alt=""><br>
</td></tr></table>
</td>
<td bgcolor=#bdbdbd valign=middle align=left>
<div style="font-weight:bold;"><? echo $h_serv; ?></div>
</td></tr>

<tr><td bgcolor=#dedede valign=top align=right>
<b><? echo $h_ip; ?></b><br>
<font size=1><? echo $hd_ip; ?><br></font> 
</td>
<td bgcolor=#dedede valign=top align=left>
<input type=text name=cip rows=4 value='<? echo ($cip)?$cip:"127.0.0.1"; ?>'></textarea>
</td></tr>

<tr><td bgcolor=#bdbdbd align=right valign=middle>
<table width=100% cellpadding=0 cellspacing=0 border=0>
<tr><td bgcolor=#445566>
<img src=skel.admin/picto/0.png width=1 height=2 hspace=0 vspace=0 border=0 alt=""><br>
</td></tr></table>
</td>
<td bgcolor=#bdbdbd valign=middle align=left>
<div style="font-weight:bold;"><? echo $h_lg; ?></div>
</td></tr>

<tr><td bgcolor=#dedede valign=top align=right>
<b><? echo $h_langs; ?></b><br>
<font size=1><? echo $hd_langs; ?><br></font> 
</td>
<td bgcolor=#dedede valign=top align=left>
<textarea type=text name=clangs rows=4><? echo ($clangs)?$clangs:"-/fr/www = fr\nuk/en = en\nes = es"; ?></textarea>
</td></tr>

<tr><td bgcolor=#bdbdbd align=right valign=middle>
<table width=100% cellpadding=0 cellspacing=0 border=0>
<tr><td bgcolor=#445566>
<img src=skel.admin/picto/0.png width=1 height=2 hspace=0 vspace=0 border=0 alt=""><br>
</td></tr></table>
</td>
<td bgcolor=#bdbdbd valign=middle align=left>
<div style="font-weight:bold;"><? echo $h_dms; ?></div>
</td></tr>

<tr><td bgcolor=#dedede valign=top align=right>
<b><?echo $h_dms; ?></b><br>
<font size=1><? echo $hd_dms; ?><br></font>
</td>
<td bgcolor=#dedede valign=top align=left>
<textarea type=text name=cdomains rows=4><? echo ($cdomains)?$cdomains:"domain1.org/domain1.net = domain1\ndomain2.com = domain2"; ?></textarea>
</td></tr>

<tr><td bgcolor=#bdbdbd align=right valign=middle>
<table width=100% cellpadding=0 cellspacing=0 border=0>
<tr><td bgcolor=#445566>
<img src=skel.admin/picto/0.png width=1 height=2 hspace=0 vspace=0 border=0 alt=""><br>
</td></tr></table>
</td>
<td bgcolor=#bdbdbd valign=middle align=left>
<div style="font-weight:bold;"><? echo $h_test; ?></div>
</td></tr>

<tr><td bgcolor=#dedede valign=top align=right>
<b><? echo $h_tlog; ?></b><br>
<font size=1><? echo $hd_tlog ?></font>
</td>
<td bgcolor=#dedede valign=top align=left>
<input type=text name=tlogin value='<? echo ($tlogin)?$tlogin:"test"; ?>' size=20>
</td></tr>

<tr><td bgcolor=#dedede valign=top align=right>
<b><? echo $h_tpass; ?></b><br>
<font size=1><? echo "$hd_pass $hd_vpass"; ?><br></font>
</td>
<td bgcolor=#dedede valign=top align=left>
<input type=password name=tpasswd  size=20><br>
<input type=password name=tvpasswd  size=20><br>
</td></tr>

<?} elseif($step==2) { ?>

<input type=hidden name=cip value=<? echo $cip; ?>>
<input type=hidden name=cpath value=<? echo $cpath; ?>>
<input type=hidden name=cname value="<? echo $cname; ?>">
<input type=hidden name=cmaildom value="<? echo $cmaildom; ?>">
<input type=hidden name=clangs value="<? echo $clangs; ?>">
<input type=hidden name=cdomains value="<? echo $cdomains; ?>">
<input type=hidden name=tlogin value="<? echo $tlogin; ?>">
<input type=hidden name=tpasswd value="<? echo $tpasswd; ?>">

<table cellpadding=5 cellspacing=0 border=0 width=100%>
<tr><td bgcolor=#bdbdbd align=right valign=middle>
<table width=100% cellpadding=0 cellspacing=0 border=0>
<tr><td bgcolor=#445566>
<img src=skel.admin/picto/0.png width=1 height=2 hspace=0 vspace=0 border=0 alt=""><br>
</td></tr></table>
</td>
<td bgcolor=#bdbdbd valign=middle align=left>
<div style="font-weight:bold;"><? echo $h_auth; ?></div>
</td></tr>

<tr><td bgcolor=#dedede valign=top align=right>
<b><? echo $h_choice; ?></b><br>
<font size=1><? echo $hd_choice; ?></font>
</td>
<td bgcolor=#dedede valign=top align=left>
<input type=radio name=auth value="file" <? echo($auth=='file' || !$auth)?"checked":""; ?>>.htaccess<br>
<input type=radio name=auth value="mysql" <? echo($auth=='mysql')?"checked":""; ?>>MySQL<br>
<input type=radio name=auth value="ldap"<? echo($auth=='ldap')?"checked":""; ?>>LDAP<br>
</td></tr>

<tr><td bgcolor=#cdcdcd align=right valign=middle>
<table width=100% cellpadding=0 cellspacing=0 border=0>
<tr><td bgcolor=#445566>
<img src=skel.admin/picto/0.png width=1 height=2 hspace=0 vspace=0 border=0 alt=""><br>
</td></tr></table>
</td>
<td bgcolor=#cdcdcd valign=middle align=left>
<div style="font-weight:bold;">.htaccess</div>
</td></tr>

<tr><td bgcolor=#dedede valign=top align=right>
<b><? echo $h_log; ?></b><br>
<font size=1><? echo $hd_log ?></font>
</td>
<td bgcolor=#dedede valign=top align=left>
<input type=text name=plogin value='<? echo ($plogin)?$plogin:"admin"; ?>' size=20>
</td></tr>

<tr><td bgcolor=#dedede valign=top align=right>
<b><? echo $h_pass; ?></b><br>
<font size=1><? echo "$hd_pass $hd_vpass"; ?><br></font>
</td>
<td bgcolor=#dedede valign=top align=left>
<input type=password name=ppasswd  size=20><br>
<input type=password name=pvpasswd  size=20><br>
</td></tr>

<tr><td bgcolor=#cdcdcd align=right valign=middle>
<table width=100% cellpadding=0 cellspacing=0 border=0>
<tr><td bgcolor=#445566>
<img src=skel.admin/picto/0.png width=1 height=2 hspace=0 vspace=0 border=0 alt=""><br>
</td></tr></table>
</td>
<td bgcolor=#cdcdcd valign=middle align=left>
<div style="font-weight:bold;">MySQL</div>
</td></tr>

<tr><td bgcolor=#dedede valign=top align=right>
<b><? echo $h_serv; ?></b><br>
<font size=1><? echo $hd_serv; ?></font>
</td>
<td bgcolor=#dedede valign=top align=left>
<input type=text name=mserver value='<? echo ($mserver)?$mserver:"localhost"; ?>' size=20>
</td></tr>

<tr><td bgcolor=#dedede valign=top align=right>
<b><? echo $h_mbase; ?></b><br>
<font size=1><? echo $hd_mbase; ?></font>
</td>
<td bgcolor=#dedede valign=top align=left>
<input type=text name=mdbname value='<? echo ($mdbname)?$mdbname:"ccmsusers"; ?>' size=20>
</td></tr>

<tr><td bgcolor=#dedede valign=top align=right>
<b><? echo $h_mlog; ?></b><br>
<font size=1><? echo $hd_mlog; ?></font>
</td>
<td bgcolor=#dedede valign=top align=left>
<input type=text name=mlogin value='<? echo ($mlogin)?$mlogin:"ccms"; ?>' size=20>
</td></tr>

<tr><td bgcolor=#dedede valign=top align=right>
<b><? echo $h_mpass; ?></b><br>
<font size=1><? echo $hd_pass; ?></font>
</td>
<td bgcolor=#dedede valign=top align=left>
<input type=password name=mpasswd size=20>
</td></tr>

<tr><td bgcolor=#dedede valign=top align=right>
<b><? echo $h_log; ?></b><br>
<font size=1><? echo $hd_log ?></font>
</td>
<td bgcolor=#dedede valign=top align=left>
<input type=text name=bmlogin value='<? echo ($bmlogin)?$bmlogin:"admin"; ?>' size=20>
</td></tr>

<tr><td bgcolor=#dedede valign=top align=right>
<b><? echo $h_pass; ?></b><br>
<font size=1><? echo "$hd_pass $hd_vpass"; ?><br></font>
</td>
<td bgcolor=#dedede valign=top align=left>
<input type=password name=bmpasswd  size=20><br>
<input type=password name=bmvpasswd  size=20><br>
</td></tr>

<tr><td bgcolor=#cdcdcd align=right valign=middle>
<table width=100% cellpadding=0 cellspacing=0 border=0>
<tr><td bgcolor=#445566>
<img src=skel.admin/picto/0.png width=1 height=2 hspace=0 vspace=0 border=0 alt=""><br>
</td></tr></table>
</td>
<td bgcolor=#cdcdcd valign=middle align=left>
<div style="font-weight:bold;">LDAP</div>
</td></tr>

<tr><td bgcolor=#dedede valign=top align=right>
<b><? echo $h_serv; ?></b><br>
<font size=1><? echo $hd_lserv ?></font>
</td>
<td bgcolor=#dedede valign=top align=left>
<input type=text name=lhost value='<? echo ($lhost)?$lhost:"localhost"; ?>' size=20>
</td></tr>

<tr><td bgcolor=#dedede valign=top align=right>
<b><?echo $h_port; ?></b><br>
<font size=1><? echo $hd_port; ?></font>
</td>
<td bgcolor=#dedede valign=top align=left>
<input type=text name=lport value='<? echo ($lport)?$lport:"389"; ?>' size=20>
</td></tr>

<tr><td bgcolor=#dedede valign=top align=right>
<b>BaseBind1</b><br>
<font size=1>-----</font>
</td>
<td bgcolor=#dedede valign=top align=left>
<input type=text name=lbasebind1 value='<? echo ($lbasebind1)?$lbasebind1:"ou=People,dc=Test"; ?>' size=20>
</td></tr>

<tr><td bgcolor=#dedede valign=top align=right>
<b>Attribute 1</b><br>
<font size=1>------</font>
</td>
<td bgcolor=#dedede valign=top align=left>
<input type=text name=lattr1 value='<? echo ($lattr1)?$lattr1:"uid"; ?>' size=19>
</td></tr>

<tr><td bgcolor=#dedede valign=top align=right>
<b>BaseBind2</b><br>
<font size=1>-----</font>
</td>
<td bgcolor=#dedede valign=top align=left>
<input type=text name=lbasebind2 value='<? echo ($lbasebind2)?$lbasebind2:"ou=Users,dc=Test"; ?>' size=20>
</td></tr>

<tr><td bgcolor=#dedede valign=top align=right>
<b>Attribute 2</b><br>
<font size=1>------</font>
</td>
<td bgcolor=#dedede valign=top align=left>
<input type=text name=lattr2 value='<? echo ($lattr2)?$lattr2:"uid"; ?>' size=20>
</td></tr>

<tr><td bgcolor=#dedede valign=top align=right>
<b>Groupbase</b><br>
<font size=1>-----</font>
</td>
<td bgcolor=#dedede valign=top align=left>
<input type=text name=lgroupbase value='<? echo ($lgroupbase)?$lgroupbase:"ou=Groups,dc=Test"; ?>' size=20>
</td></tr>

<tr><td bgcolor=#dedede valign=top align=right>
<b>Group</b><br>
<font size=1>-----</font>
</td>
<td bgcolor=#dedede valign=top align=left>
<input type=text name=lgroup value='<? echo ($lgroup)?$lgroup:"ccms"; ?>' size=20>
</td></tr>

<tr><td bgcolor=#dedede valign=top align=right>
<b>BaseDN</b><br>
<font size=1>------</font>
</td>
<td bgcolor=#dedede valign=top align=left>
<input type=text name=lbasedn value='<? echo ($lbasedn)?$lbasedn:"uid"; ?>' size=20>
</td></tr>

<? } 
if($step==1 || $step==2) {
?>

<tr><td bgcolor=#bdbdbd colspan=2>
<table width=100% cellpadding=0 cellspacing=0 border=0>
<tr><td bgcolor=#445566>
<img src=skel.admin/picto/0.png width=1 height=2 hspace=0 vspace=0 border=0 alt=""><br>
</td></tr></table>
</td></tr>


<tr><td bgcolor=#dedede valign=top align=right>
<font size=1><? $n=($step==1)?$h_cont:$h_end; echo "$h_click $n $h_ins"; ?>
</td>
<td bgcolor=#dedede valign=bottom align=left>
<input type=submit value=">>>" style="padding:0 25 0 25;" width=100%>
</td></tr>

<? } elseif($step==3) { ?>

<table cellpadding=5 cellspacing=0 border=0 width=100%>
<tr><td bgcolor=#bdbdbd align=right valign=middle>
<table width=100% cellpadding=0 cellspacing=0 border=0>
<tr><td bgcolor=#445566>
<img src=skel.admin/picto/0.png width=1 height=2 hspace=0 vspace=0 border=0 alt=""><br>
</td></tr></table>
</td>
<td bgcolor=#bdbdbd valign=middle align=left>
<div style="font-weight:bold;"><? echo $ended; ?></div>
</td></tr>

<tr><td bgcolor=#dedede valign=top colspan=2>
<div style="font-weight:bold;color:#223344;margin:2 10 2 10;"><li><? echo implode("\n<hr>",$res); ?></div>
</td></tr>

<tr><td bgcolor=#bdbdbd colspan=2>
<table width=100% cellpadding=0 cellspacing=0 border=0>
<tr><td bgcolor=#445566>
<img src=skel.admin/picto/0.png width=1 height=2 hspace=0 vspace=0 border=0 alt=""><br>
</td></tr></table>
</td></tr>

<?  } ?>

</table>

</td></tr></table>
</td></tr></table>
</form>
</td></tr></table>
</body></html>


