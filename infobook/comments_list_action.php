<?php
/**									comments_list_action.php
 */

$action='comments_list.php';

$id=$_POST['id_db'];
$detail=clean_text($_POST['detail']);
$entrydate=$_POST['entrydate'];
if(isset($_POST['bid'])){$bid=$_POST['bid'];}else{$bid='G';}
if(isset($_POST['catid'])){$catid=$_POST['catid'];}else{$catid='';}
if(isset($_POST['ratvalue'])){$ratvalue=$_POST['ratvalue'];}else{$ratvalue='N';}
if(isset($_POST['newyid'])){$newyid=$_POST['newyid'];}else{$newyid=$Student['YearGroup']['value'];;}


include('scripts/sub_action.php');

	if($bid=='%'){$bid='G';}
	$category=$catid.':'.$ratvalue.';';

	if($id!=''){
		mysql_query("UPDATE comments SET student_id='$sid',
		detail='$detail', entrydate='$entrydate', yeargroup_id='$newyid',
		subject_id='$bid', category='$category', teacher_id='$tid'
		WHERE id='$id'");
		}
	else{
		mysql_query("INSERT INTO comments SET student_id='$sid',
		detail='$detail', entrydate='$entrydate', yeargroup_id='$newyid',
		subject_id='$bid', category='$category',
		teacher_id='$tid'");
		$result[]=get_string('commentrecorded',$book);

		$teachername=display_teachername($tid);
		$footer='--'. "\r\n" . get_string('pastoralemailfooterdisclaimer');
		$subject='Comment for '.$Student['Forename']['value']
				.' '.$Student['Surname']['value'].' ('. 
					$Student['RegistrationGroup']['value'].')'; 
		$message=$subject."\r\n".'Subject: '. display_subjectname($bid)."\r\n". 
				'Posted by '.$teachername. "\r\n";
		$message.="\r\n". $detail. "\r\n";
		$message.="\r\n". $footer;
		$recipients=list_sid_responsible_users($sid,$bid);
		if($recipients and $CFG->emailoff!='yes' and $CFG->emailcomments=='yes'){
			if(sizeof($recipients)>0){
				foreach($recipients as $key => $recipient){
					$recipient['email']=strtolower($recipient['email']);
					send_email_to($recipient['email'],$fromaddress,$subject,$message);
					$result[]=get_string('emailsentto').' '.$recipient['username'];
					}
				}
			}
		}

include('scripts/results.php');	
include('scripts/redirect.php');	
?>
