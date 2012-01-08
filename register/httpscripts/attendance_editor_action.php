<?php
/**                    httpscripts/attendance_editor_action.php
 *
 */

require_once('../../scripts/http_head_options.php');

$sub=$_POST['sub'];
$sid=$_POST['sid'];

if($sub=='Cancel'){
	$openerId='-100';
	}
else{

	if(isset($_POST['openid'])){$openerId=$_POST['openid'];}
	if(isset($_POST['sid'])){$sid=$_POST['sid'];}
	if(isset($_POST['date'])){$date=$_POST['date'];}
	if(isset($_POST['attsession'])){$attsession=$_POST['attsession'];}
	if(isset($_POST['code'])){$code=$_POST['code'];}
	if(isset($_POST['bookid'])){$oldbookid=$_POST['bookid'];}
	if(isset($_POST['dayrepeat'])){$dayrepeat=$_POST['dayrepeat'];}
	if(isset($_POST['comment'])){$comment=$_POST['comment'];}else{$comment='';}
	if(isset($_POST['answer0'])){$answer=$_POST['answer0'];}else{$answer='';}

	trigger_error($attsession,E_USER_WARNING);

	if($sub=='Submit'){

		add_attendance_booking($sid,$date,$attsession,$code,$dayrepeat,$comment);

		if($answer=='yes'){
			/* Force both attsessions for the day to be the same. */
			$day=date('N',strtotime($date));
			if($attsession=='AM'){$attsession='PM';}
			else{$attsession='AM';}
			add_attendance_booking($sid,$date,$attsession,$code,$dayrepeat,$comment);
			}

		}
	elseif($sub=='Delete'){
		delete_attendance_booking($sid,$oldbookid);
		}
	}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>Attendance Editor</title>
<meta http-equiv="content-type" content="application/xhtml+xml; charset=utf-8" />
<meta http-equiv="Content-Script-Type" content="text/JavaScript" />
<meta name="copyright" content="Copyright 2002-2012 S T Johnson.  All trademarks acknowledged. All rights reserved" />
<meta name="version" content='<?php print "$CFG->version"; ?>' />
<meta name="licence" content="GNU Affero General Public License version 3" />
<script language="JavaScript" type="text/javascript" src="../../js/book.js?version=1013"></script>
</head>
<body onload="closeAttendanceHelper(<?php print '\''.$sid.'\',\''.$date.'\',\''.$openerId.'\'';?>);">
	<div id="bookbox">
	  <div id="viewcontent" class="content">
	  </div>
	</div>
</body>
</html>
