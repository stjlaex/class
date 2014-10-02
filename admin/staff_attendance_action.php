<?php
/**									staff_attendance_action.php
 */

$action='staff_attendance.php';

include('scripts/sub_action.php');

if(isset($_POST['uids'])){$uids=(array)$_POST['uids'];}else{$uids=array();}

if($sub=='Submit' or count($uids)>0){
	if(isset($_POST['usernames']) and $_POST['usernames']!=''){$usernames=$_POST['usernames'];}else{$usernames=array();}
	if(isset($_POST['date0']) and $_POST['date0']!=''){$eventdate=$_POST['date0'];}else{$eventdate=date("Y-m-d");}

	if(count($usernames)==0){
		$action='staff_list.php';
		foreach($uids as $uid){
			$d_user=mysql_query("SELECT username FROM users WHERE uid='$uid';");
			$username=mysql_result($d_user,0);
			$usernames[]=$username;
			}
		}

	foreach($usernames as $username){
		if(isset($_POST['attendancestatus-'.$username]) and $_POST['attendancestatus-'.$username]!=''){$attendancestatus=$_POST['attendancestatus-'.$username];}else{$attendancestatus='';}
		if(isset($_POST['attendancecomment-'.$username]) and $_POST['attendancecomment-'.$username]!=''){$attendancecomment=$_POST['attendancecomment-'.$username];}else{$attendancecomment='';}
		if(count($uids)>0){$attendancestatus='a';}
		$uid=get_uid($username);

		if($attendancestatus!=''){
			$d_attendance=mysql_query("SELECT status, comment FROM user_attendance
									WHERE username='$username' AND date='$eventdate';");
			if(mysql_num_rows($d_attendance)==0 and $attendancestatus=='a'){
				mysql_query("INSERT INTO user_attendance (status, username, date, comment, session) 
							VALUES ('$attendancestatus','$username','$eventdate','$attendancecomment','');");
				}
			else{
				$att=mysql_fetch_array($d_attendance,MYSQL_ASSOC);
				if($att['status']!=$attendancestatus or $att['comment']!=$attendancecomment){
					mysql_query("UPDATE user_attendance SET status='$attendancestatus', comment='$attendancecomment', session=''
								WHERE username='$username' AND date='$eventdate';");
					}
				}
			}
		}
	}
elseif($sub=='export'){
	require_once('Spreadsheet/Excel/Writer.php');

	if(isset($_POST['usernames']) and $_POST['usernames']!=''){$usernames=$_POST['usernames'];}else{$usernames=array();}
	if(isset($_POST['date0']) and $_POST['date0']!=''){$eventdate=$_POST['date0'];}else{$eventdate=date("Y-m-d");}
	
	$file=$CFG->eportfolio_dataroot. '/cache/files/';
	$file.='class_export.xls';
	$workbook=new Spreadsheet_Excel_Writer($file);
	$workbook->setVersion(8);
	$format_hdr_bold=&$workbook->addFormat(
		array(
			'Size' => 11,
			'Color' => 'white',
			'Pattern' => 1,
			'Bold' => 1,
			'FgColor' => 'gray'
			));
	$format_line_bold=&$workbook->addFormat(
		array(
			'Size' => 10,
			'Bold' => 1
			));
	$format_line_normal=&$workbook->addFormat(
		array(
			'Size' => 10,
			'Bold' => 0
			));
	$worksheet=&$workbook->addWorksheet('Export_Staff_Attendance');

	if(!$file){
		$error[]='unabletoopenfileforwriting';
		}
	else{
		$worksheet->setInputEncoding('UTF-8');
		$worksheet->write(0, 0, 'Date:', $format_hdr_bold);
		$worksheet->write(0, 1, $eventdate, $format_line_bold);
		$worksheet->write(1, 0, 'Classis Id.', $format_hdr_bold);
		$worksheet->write(1, 1, 'Username', $format_hdr_bold);
		$worksheet->write(1, 2, 'Surname', $format_hdr_bold);
		$worksheet->write(1, 3, 'Forename', $format_hdr_bold);
		$worksheet->write(1, 4, 'Attendance', $format_hdr_bold);
		$worksheet->write(1, 5, 'Comment', $format_hdr_bold);

		$rown=2;
		foreach($usernames as $username){
			$d_user=mysql_query("SELECT uid, surname, forename 
							FROM users WHERE username='$username'");
			$uid=mysql_result($d_user,0,'uid');
			$surname=mysql_result($d_user,0,'surname');
			$forename=mysql_result($d_user,0,'forename');

			$d_ua=mysql_query("SELECT * FROM user_attendance WHERE username='$username' AND date='$eventdate';");
			$comment=mysql_result($d_ua,0,'comment');
			$status=mysql_result($d_ua,0,'status');

			if($status=='a'){
				$status='absent';
				$worksheet->write($rown, 0, $uid, $format_line_bold);
				$worksheet->write($rown, 1, $username, $format_line_bold);
				$worksheet->write($rown, 2, $surname, $format_line_bold);
				$worksheet->write($rown, 3, $forename, $format_line_bold);
				$worksheet->write($rown, 4, get_string($status,$book), $format_line_normal);
				$worksheet->write($rown, 5, $comment, $format_line_normal);

				$rown++;
				}
			}

		$workbook->close();
	?>
		<input type="hidden" name="openexport" id="openexport" value="xls">
	<?php
		}
	}

include('scripts/redirect.php');
?>
