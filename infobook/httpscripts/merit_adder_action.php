<?php
/**                    httpscripts/merit_adder_action.php
 *
 */

require_once('../../scripts/http_head_options.php');
$sub=$_POST['sub'];
$sid=$_POST['sid'];
$tid=$_SESSION['username'];

$Student=fetchStudent_short($sid);

if($sub=='Cancel'){
	$openerId='-100';
	}
elseif($sub=='Submit'){

	if(isset($_GET['openid'])){$openerId=$_GET['openid'];}
	if(isset($_POST['openid'])){$openerId=$_POST['openid'];}
	if(isset($_GET['points'])){$pointsvalue=$_GET['points'];}
	if(isset($_POST['points'])){$pointsvalue=$_POST['points'];}
	if(isset($_GET['activity'])){$activity=$_GET['activity'];}
	if(isset($_POST['activity'])){$activity=$_POST['activity'];}
	if(isset($_GET['corevalue'])){$corevalue=$_GET['corevalue'];}
	if(isset($_POST['corevalue'])){$corevalue=$_POST['corevalue'];}

	if(isset($_POST['inmust'])){$inmust=$_POST['inmust'];}else{$inmust='yes';}
	if(isset($_POST['bid'])){$bid=$_POST['bid'];}else{$bid='';}
	if(isset($_POST['pid'])){$pid=$_POST['pid'];}else{$pid='';}
	if(isset($_POST['detail'])){$detail=$_POST['detail'];}else{$detail='';}

	$todate=date('Y-m-d');
	$curryear=get_curriculumyear();
	$rating_name='meritpoints';

	$d_rating=mysql_query("SELECT descriptor FROM rating WHERE name='$rating_name' AND value='$pointsvalue';");
	if(mysql_num_rows($d_rating)>0){
		$pointsresult=mysql_result($d_rating,0);
		}
	else{
		$pointsresult=$pointsvalue;
		$error[]='The category value '.$pointsvalue.' does not exist for rating name '.$rating_name.'!';
		}


	if($inmust=='yes' and $pointsvalue!=''){
		mysql_query("INSERT INTO merits (teacher_id, student_id, date, year, activity, value, result, detail,
						subject_id, component_id, core_value) 
						VALUES ('$tid', '$sid', '$todate', '$curryear', '$activity','$pointsvalue',
									'$pointsresult','$detail','$bid', '$pid', '$corevalue');");


		if(isset($CFG->emailmerits) and $CFG->emailmerits=='yes'){
			/* Message to relevant teaching staff. */
			list($ratingnames,$catdefs)=fetch_categorydefs('mer');

			$footer=get_string('pastoralemailfooterdisclaimer');
			$messagesubject='Merit for '.$Student['Forename']['value']
				.' '.$Student['Surname']['value'].' ('. 
					$Student['RegistrationGroup']['value'].')'; 
			$message='<p>'.$messagesubject.'</p><p>For: '. $catdefs[$activity]['name'] .'</p>';
			$message.='<p>'. $detail.'</p>';
			$messagetxt=strip_tags(html_entity_decode($message, ENT_QUOTES, 'UTF-8'))."\r\n".'--'. "\r\n" . $footer;
			$message.='<br /><hr><p>'. $footer.'<p>';

			$result=(array)message_student_teachers($sid,'',$bid,$messagesubject,$messagetxt,$message,'p');
			}


		}
	elseif($inmust!='yes'){

		/*TODO: allow editing of existing merits. */
		$merid=$inmust;
		mysql_query("UPDATE merits SET comment='$incom' WHERE report_id='$rid' AND
						student_id='$sid' AND subject_id='$bid' AND
						component_id='$pid' AND entryn='$entryn';");

		}
	}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>ClaSS Merits</title>
<meta http-equiv="content-type" content="application/xhtml+xml; charset=utf-8" />
<meta http-equiv="Content-Script-Type" content="text/JavaScript" />
<meta name="copyright" content="Copyright 2002-2012 S T Johnson.  All trademarks acknowledged. All rights reserved" />
<meta name="version" content='<?php print "$CFG->version"; ?>' />
<meta name="licence" content="GNU Affero General Public License version 3" />
<link id="viewstyle" rel="stylesheet" type="text/css" href="../../css/bookstyle.css" />
<link id="viewstyle" rel="stylesheet" type="text/css" href="../../css/infobook.css" />
<script language="JavaScript" type="text/javascript" src="../../js/jquery-1.8.2.min.js"></script>
<script language="JavaScript" type="text/javascript" src="../../js/book.js?version=1013"></script>
</head>
<body onload="closeHelperWindow(<?php print '\''.$openerId.'\'';?>);">
	<div id="bookbox">
	  <div id="viewcontent" class="content">
	  </div>
	</div>
</body>
</html>
