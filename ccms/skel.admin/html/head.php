<? /* $Id: head.php,v 1.1 2003/09/17 12:40:50 terraces Exp $
Copyright (C) 2001, 2002, Makina Corpus, http://makinacorpus.org
This file is a componenet of CCMS <http://makinacorpus.org/index.php/ccms>
Created and maintained by mose (mose@makinacorpus.org)
Released under GPL version 2 or later, see LICENSE file
or http://www.gnu.org/copyleft/gpl.html
*/
?>

<HTML>
<HEAD>
<TITLE>
CCMS Admin
</TITLE>
<STYLE type="text/css">
<!--
body, td, div, p  { font-family: verdana, helvetica, arial, sans serif; }
tt, pre           { font-size :80%; font-family: fixed, monaco, lucida sans, monospace; }
input, textarea, select, .input { font-family: verdana, helvetica, arial, sans serif;
        border : 1; border-width : 1; border-color : #ffffff; 
        background : #ddeeff; padding : 0 3 0 3; color : #333333;  }
.submit { background : #99AABB; color : #000000; font-weight: bold; 
          padding : 2 2 2 2; }
.tsubmit { background : #99AABB; color : #000000; font-weight: bold; 
          padding : 3 2 3 2; margin: 0 0 0 0; font-family: verdana, helvetica, arial, sans serif;
					border : 1; border-width : 1; border-color : #ffffff;
					}
.checkbox { padding: 0 0 0 0; }
ul                { margin-top : 0px; margin-left: 10px; font-size :1em; }
a           { padding : 0 3 0 3; }
a:hover     { background : #333333; color :#FFFFFF; text-decoration :none; } 
.meat       { text-align : left;  }
.titre      { color : #003366; font-weight : bold; font-size :120%; }
.sstitre    { color : #336699; font-weight : bold }
.nav        { text-decoration :none; color:#113377 }
.bnav       { text-decoration :none; font-weight :bold; font-size :110%; color :#003366 }
.inav       { text-decoration :none; font-size :80%; color :#113377 }
.flag       { text-decoration :none; color :#000000 }
.dot        { color :#99CCFF; font-size :80% }
.dot1       { padding : 0 6 0 6; color :#336699; font-size :80% }
.dot2       { padding : 0 6 0 6; color :#003366; font-size :80% }
.menu       { padding : 2 6 2 6; text-decoration :none; font-size:80%; color :#000000; background :#bebebe; font-weight :bold }
.bmenu      { padding : 2 6 2 6; text-decoration :none; font-size:80%; color :#446688; font-weight :bold }
.bdir       { padding : 1 6 1 6; text-decoration :none; font-size:80%; color :#000000; background :#bebebe; font-weight :bold }
.dir        { padding : 1 6 1 6; text-decoration :none; font-size:80%; color :#446688; font-weight :bold }
.bfile      { padding : 0 6 0 6; text-decoration :none; font-size:80%; color :#000000; background :#becede; }
.file       { padding : 0 6 0 6; text-decoration :none; font-size:80%; color :#556677;  }
#nopad      { padding : 0 0 0 0; }
#nopad1     { padding : 1 0 1 0; }
-->
</STYLE>
</HEAD>

<BODY topmargin=0 leftmargin=0 marginwidth=0 marginheight=0 
bgcolor=#e4E4E4 text=#000000 link=#336699 vlink=#003366 alink=#FFCC00>

<table border="0" cellpadding="0" cellspacing="0" width="100%">
<tr>
<td bgcolor=#bebebe nowrap width=140><font size=2><?="$cm_ccms · ver $cm_ccmsver"?></font></td>
<td bgcolor=#bebebe>

<table border=0 cellpadding=0 cellspacing=0><tr>
<td bgcolor=#a2a2a2 width=1><img src=/picto/0.png width=1 height=1 hspace=0 vspace=0 border=0 alt=''></td>
<td bgcolor=#ffffff><a href=/index.php?p=<?=urlencode($p)?> class=bmenu>Files</a></td>
<td bgcolor=#a2a2a2 width=1><img src=/picto/0.png width=1 height=1 hspace=0 vspace=0 border=0 alt=''></td>
<td bgcolor=#ffffff><a href=/flow.php?p=<?=urlencode($p)?> class=bmenu>Flow</a></td>
<td bgcolor=#a2a2a2 width=1><img src=/picto/0.png width=1 height=1 hspace=0 vspace=0 border=0 alt=''></td>
<td bgcolor=#ffffff><a href=/help.php class=bmenu>Help</a></td>
<td bgcolor=#a2a2a2 width=1><img src=/picto/0.png width=1 height=1 hspace=0 vspace=0 border=0 alt=''></td>

</tr></table>

</td>
<td bgcolor=#bebebe align=right>
<table border=0 cellpadding=0 cellspacing=0><tr>
<td bgcolor=#bebebe align=right><font size=2><b><?=$me?></b></font></td>
<td bgcolor=#bebebe align=right><font size=2><a href=/index.php?logout=1 class=bmenu>Logout</a></font></td>
<td bgcolor=#bebebe align=right><font size=2>&nbsp;</font></td>
</tr></table>
</td></tr>
<tr><td colspan=4 bgcolor=#a2a2a2><img src=/picto/0.png width=1 height=1 hspace=0 vspace=0 border=0 alt=''></td></tr>
</table>


