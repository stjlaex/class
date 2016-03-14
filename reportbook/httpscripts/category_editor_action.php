<?php
/**                    httpscripts/category_editor_action.php
 */

require_once('../../scripts/http_head_options.php');
$sub=$_POST['sub'];
$rid=$_POST['rid'];
$type=$_POST['type'];
if(isset($_POST['bid'])){$bid=$_POST['bid'];}
if(isset($_POST['pid'])){$pid=$_POST['pid'];}
if(isset($_POST['gradingname'])){$gradingname=$_POST['gradingname'];}
if(isset($_POST['stage'])){$stage=$_POST['stage'];}
if(isset($_POST['subsubject'])){$subsubject=$_POST['subsubject'];}
if(isset($_POST['sublevel'])){$sublevel=$_POST['sublevel'];}

/*Note: categories are not handled by the commentwriter*/

if($sub=='Cancel'){
	$openerId='-100';
	$incom='';
	}
elseif($sub=='Submit'){
	$openerId=$_POST['openid'];

	if($gradingname==''){
		$d_gn=mysql_query("SELECT rating_name FROM report_skill WHERE subject_id='$pid' and profile_id='$rid' LIMIT 1;");
		$gn=mysql_fetch_row($d_gn);
		$gradingname=$gn[0];
		}
	
	if($type=='cat'){
		if($pid!='' and $pid!=' '){$bid=$pid;}
		if(!isset($stage)){$stage='%';}
		$RepDef=fetchReportDefinition($rid);
		$crid=$RepDef['Course']['value'];
		}
	else{
		$crid='%';
		}


	$maxcatn=50;
	/*Foreach statement*/
	for($matn=1;$matn<=$maxcatn;$matn++){
		if(isset($_POST['catid'.$matn])){
			$incatid=$_POST['catid'.$matn];
			$inname='name'. $matn;
			$inval=clean_text($_POST[$inname]);
			if(isset($_POST['stage'.$matn])){$instage=$_POST['stage'.$matn];}
			else{$instage=$stage;}
			if(isset($_POST['sublevel'.$matn])){$insublevel=$_POST['sublevel'.$matn];}
			else{$insublevel=$sublevel;}
			if(isset($_POST['subsubject'.$matn])){$insubsubject=$_POST['subsubject'.$matn];}
			else{$insubsubject=$subsubject;}
			/* it adds a new one - false*/
			if($incatid==-1 and $inval!=''){
				add_report_skill_statement($inval,$bid,$rid,$pid,$instage,$insubsubject,$insublevel,$gradingname,false);
				}
			/* it updates it - true*/
			elseif($incatid>0 and $inval!=''){
				add_report_skill_statement($inval,$bid,$rid,$pid,$instage,$insubsubject,$insublevel,$gradingname,true,$incatid);
				}
			/* it deletes it*/
			elseif($incatid>0 and $inval==''){
				delete_report_skill_statement($incatid,$rid);
				}
			}

	}

	}

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>ClaSS Comment Writer</title>
<meta http-equiv="content-type" content="application/xhtml+xml; charset=utf-8" />
<meta http-equiv="Content-Script-Type" content="text/JavaScript" />
<meta name="copyright" content="Copyright 2002-2016 S T Johnson.  All trademarks acknowledged. All rights reserved" />
<meta name="version" content='<?php print "$CFG->version"; ?>' />
<meta name="licence" content="Affero General Public License version 3" />
<link id="viewstyle" rel="stylesheet" type="text/css" href="../../css/bookstyle.css" />
<link id="viewstyle" rel="stylesheet" type="text/css" href="../../css/commentwriter.css" />
<script language="JavaScript" type="text/javascript" src="../../js/jquery-1.8.2.min.js"></script>
<script language="JavaScript" type="text/javascript" src="../../js/book.js?version=1014"></script>
</head>
<body onload="closeHelperWindow(<?php print '\''.$openerId.'\'';?>);">
	<div id="bookbox">

	  <div id="heading">
			  <label><?php print ''; ?></label>
	  </div>

	  <div id="viewcontent" class="content">
<?php
		  include('../../scripts/results.php');
?>
	  </div>

	</div>
</body>
</html>
