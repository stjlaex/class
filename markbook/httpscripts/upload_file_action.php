<?php
/**                    httpscripts/upload_file_action.php
 *
 */

require_once('../../scripts/http_head_options.php');
require_once('../../lib/eportfolio_functions.php');

$sub=$_POST['sub'];
$sid=$_POST['sid'];
$tid=$_SESSION['username'];

if($sub=='Cancel'){
	$openerId='-100';
	$incom='';
	}
elseif($sub=='Submit'){
	$openerId=$_POST['openid'];
	if(isset($_POST['inmust'])){$inmust=$_POST['inmust'];}
	if(isset($_POST['eid'])){$eid=$_POST['eid'];}
	if(isset($_POST['bid'])){$bid=$_POST['bid'];}
	if(isset($_POST['pid'])){$pid=$_POST['pid'];}
	if(isset($_POST['comment'])){$comment=clean_text($_POST['comment']);}else{$comment='';}
	$todate=date('Y-m-d');
	$Student=fetchStudent_singlefield($sid,'EPFUsername');


	if($inmust=='yes'){
		/*Create a new entry*/
		trigger_error($sid.' : '.$comment. ' : '.$bid.' : '.$pid,E_USER_WARNING);

		$score=(array)get_assessment_score($eid,$sid,$bid,$pid);
		if($score['id']>0){
			$eidsid_id=$score['id'];
			}
		else{
			mysql_query("INSERT INTO eidsid (assessment_id, student_id, subject_id, component_id, result, value, date) 
							VALUES ('$eid','$sid','$bid','$pid','','','$todate');");
			$eidsid_id=mysql_insert_id();
			}

		$d_c=mysql_query("INSERT INTO comments SET student_id='$sid', detail='$comment', entrydate='$todate', 
							subject_id='$bid', category='$pid', eidsid_id='$eidsid_id';");
		$entid=mysql_insert_id();
		require_once('../../lib/eportfolio_functions.php');
		link_files($Student['EPFUsername']['value'],'assessment',$entid);

		//$Student=fetchStudent_short($sid);
		//elgg_upload_files($publishdata);
		//$EPFUsername=$Student['EPFUsername'];
		//$publishdata['foldertype']='work';
		//$publishdata['batchfiles'][]=array('epfusername'=>$EPFUsername['value'],
		//							   'filename'=>$filename,
		//							   'tmpname'=>$tmpname);


		}
	elseif($inmust!='yes'){
		/* TODO: Update an existing file*/
		$entryn=$inmust;
		}
	}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>ClaSS File Uploader</title>
<meta http-equiv="content-type" content="application/xhtml+xml; charset=utf-8" />
<meta http-equiv="Content-Script-Type" content="text/JavaScript" />
<meta name="copyright" content="Copyright 2002-2012 S T Johnson.  All trademarks acknowledged. All rights reserved" />
<meta name="version" content='<?php print "$CFG->version"; ?>' />
<meta name="licence" content="GNU Affero General Public License version 3" />
<link id="viewstyle" rel="stylesheet" type="text/css" href="../../css/bookstyle.css" />
<script language="JavaScript" type="text/javascript" src="../../js/book.js?version=1013"></script>
</head>
<body onload="closeHelperWindow(<?php print '\''.$openerId.'\',\''.$entryn.'\',\'\'';?>);">
	<div id="bookbox">
	  <div id="heading">
	  </div>
	  <div id="viewcontent" class="content">
	  </div>
	</div>
</body>
</html>
