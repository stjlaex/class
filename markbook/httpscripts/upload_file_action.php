<?php
/**                    httpscripts/upload_file_action.php
 */

require_once('../../scripts/http_head_options.php');
$sub=$_POST['sub'];
$sid=$_POST['sid'];
$tid=$_SESSION['username'];

if($sub=='Cancel'){
	$openerId='-100';
	$incom='';
	}
elseif($sub=='Submit'){
	$openerId=$_POST['openid'];
	$Student=fetchStudent_short($sid);
	if(isset($_POST['mid'])){$bid=$_POST['mid'];}
	if(isset($_POST['cid'])){$cid=$_POST['cid'];}
	if(isset($_POST['pid'])){$pid=$_POST['pid'];}
	if(isset($_POST['comment'])){$comment=clean_text($_POST['comment']);}else{$comment='';}
	if(isset($_POST['news'])){$news=$_POST['news'];}else{$news='no';}

	if($inmust=='yes' and $incom!=''){
		/*Create a new entry*/
		}
	elseif($inmust!='yes'){
		/* TODO: Update an existing file*/
		$entryn=$inmust;
		/*		mysql_query("UPDATE reportentry SET
						comment='$incom' WHERE report_id='$rid' AND
						student_id='$sid' AND subject_id='$bid' AND
						component_id='$pid' AND entryn='$entryn'");
		*/
		}
	}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>ClaSS Comment Writer</title>
<meta http-equiv="content-type" content="application/xhtml+xml; charset=utf-8" />
<meta http-equiv="Content-Script-Type" content="text/JavaScript" />
<meta name="copyright" content="Copyright 2002-2006 S T Johnson.  All trademarks acknowledged. All rights reserved" />
<meta name="version" content='<?php print "$CFG->version"; ?>' />
<meta name="licence" content="GNU General Public License version 2" />
<link id="viewstyle" rel="stylesheet" type="text/css" href="../../css/bookstyle.css" />
<script language="JavaScript" type="text/javascript" src="../../js/bookfunctions.js"></script>
</head>
<body onload="closeHelperWindow(<?php print '\''.$openerId.'\',\''.$entryn.'\',\'\'';?>);">
	<div id="bookbox">

	  <div id="heading">
			  <label><?php print_string('student'); ?></label>
			<?php print $Student['DisplayFullName']['value'];?>
	  </div>

	  <div id="viewcontent" class="content">
<?php
	  //	  include('../../scripts/results.php');
?>
	  </div>

	</div>
</body>
</html>
