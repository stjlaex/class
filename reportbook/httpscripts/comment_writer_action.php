<?php
/**                    httpscripts/comment_writer_action.php
 */

require_once('../../scripts/http_head_options.php');
$sub=$_POST['sub'];
$sid=$_POST['sid'];
$rid=$_POST['rid'];
$inno=$_POST['inno'];/*the number of textareas to expect*/
$incom='';
$tid=$_SESSION['username'];

/*Note: categories are not handled by the commentwriter*/


if($sub=='Cancel'){
	$openerId='-100';
	}
elseif($sub=='Submit'){
	$openerId=$_POST['openid'];
	$Student=fetchStudent_short($sid);
	if(isset($_POST['bid'])){$bid=$_POST['bid'];}
	if(isset($_POST['pid'])){$pid=$_POST['pid'];}
	if(isset($_POST['inmust'])){$inmust=$_POST['inmust'];}

	for($c=0;$c<$inno;$c++){
		if(isset($_POST['incom'.$c])){
			$incom.=clean_text($_POST['incom'.$c]);
			}
		/* Separate the subcomments with ::: for splitting 
		 * but last subcomment should not get a separator
		 */
		if($inno>1 and $c<($inno-1)){$incom.=':::';}
		}


	if($rid!=-1){
		if($inmust=='yes' and $incom!=''){
		if(mysql_query("INSERT INTO reportentry (comment, teacher_id, report_id, student_id, 
				   subject_id, component_id) VALUES ('$incom', '$tid', '$rid', '$sid','$bid', '$pid')")){
			$entryn=mysql_insert_id();
			}
		}
		elseif($inmust!='yes'){
			$entryn=$inmust;
			mysql_query("UPDATE reportentry SET
						comment='$incom' WHERE report_id='$rid' AND
						student_id='$sid' AND subject_id='$bid' AND
						component_id='$pid' AND entryn='$entryn'");
			}
		}
	}
$comment=js_addslashes($incom);
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
<link id="viewstyle" rel="stylesheet" type="text/css" href="../../css/commentwriter.css" />
<script language="JavaScript" type="text/javascript" src="../../js/bookfunctions.js"></script>
</head>
<body onload="closeHelperWindow(<?php print '\''.$openerId.'\',\''.$entryn.'\',\''.$comment.'\'';?>);">
	<div id="bookbox">

	  <div id="heading">
			  <label><?php print_string('student'); ?></label>
<?php 
if(isset($Student)){print $Student['DisplayFullName']['value'];}
?>
	  </div>

	  <div id="viewcontent" class="content">
<?php
	  //	  include('../../scripts/results.php');
?>
	  </div>

	</div>
</body>
</html>
