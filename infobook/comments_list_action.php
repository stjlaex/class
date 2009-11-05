<?php
/**									comments_list_action.php
 *
 */

$action='comments_list.php';

$id=$_POST['id_db'];
$detail=clean_text($_POST['detail']);
$entrydate=$_POST['entrydate'];
if(isset($_POST['bid'])){$bid=$_POST['bid'];}else{$bid='G';}
if(isset($_POST['catid'])){$catid=$_POST['catid'];}else{$catid='';}
if(isset($_POST['ratvalue'])){$ratvalue=$_POST['ratvalue'];}else{$ratvalue='N';}
if(isset($_POST['newyid'])){$newyid=$_POST['newyid'];}else{$newyid=$Student['YearGroup']['value'];;}
if(isset($_POST['guardianemail0'])){$guardianemail=$_POST['guardianemail0'];}else{$guardianemail='no';}
if(isset($_POST['teacheremail0'])){$teacheremail=$_POST['teacheremail0'];}else{$teacheremail='no';}


include('scripts/sub_action.php');

	if($bid=='%'){$bid='G';}
	$category=$catid.':'.$ratvalue.';';

	if($id!=''){
		mysql_query("UPDATE comments SET student_id='$sid',
		detail='$detail', entrydate='$entrydate', yeargroup_id='$newyid',
		subject_id='$bid', category='$category', teacher_id='$tid'
		WHERE id='$id';");
		}
	else{
		mysql_query("INSERT INTO comments SET student_id='$sid',
		detail='$detail', entrydate='$entrydate', yeargroup_id='$newyid',
		subject_id='$bid', category='$category',
		teacher_id='$tid';");
		$result[]=get_string('commentrecorded',$book);

		$teachername=get_teachername($tid);
		$footer='--'. "\r\n" . get_string('pastoralemailfooterdisclaimer');
		$subject='Comment for '.$Student['Forename']['value']
				.' '.$Student['Surname']['value'].' ('. 
					$Student['RegistrationGroup']['value'].')'; 
		$message=$subject."\r\n".'Subject: '. display_subjectname($bid)."\r\n". 
				'Posted by '.$teachername. "\r\n";
		$message.="\r\n". $detail. "\r\n";
		$message.="\r\n". $footer;
		$fromaddress=$CFG->schoolname;

		$precips=(array)list_sid_responsible_users($sid,$bid);
		$arecips=array();
		if($teacheremail=='yes'){
			$arecips=(array)list_student_teachers($sid);
			}
		$recipients=array_merge($precips, $arecips);

		if($recipients and $CFG->emailoff!='yes' and $CFG->emailcomments=='yes'){
			if(sizeof($recipients)>0){
				$dones=array();
				foreach($recipients as $key => $recipient){
					if(!array_key_exists($recipient['username'],$dones)){
						$recipient['email']=strtolower($recipient['email']);
						send_email_to($recipient['email'],$fromaddress,$subject,$message);
						$result[]=get_string('emailsentto').' '.$recipient['username'];
						trigger_error('Email sent to: '.$recipient['username'],E_USER_WARNING);
						$dones[$recipient['username']]=$recipient['username'];
						}
					}
				}
			}

		$Student=fetchStudent_singlefield($sid,'Boarder');
		if($guardianemail=='yes' and ($Student['Boarder']['value']=='N' or $CFG->emailboarders=='yes')){
			$Contacts=(array)fetchContacts_emails($sid);
			$footer='--'. "\r\n" .get_string('guardianemailfooterdisclaimer');
			$message=$subject."\r\n". 'Subject: ' .display_subjectname($bid)."\r\n". 
				'Posted by '.$teachername. "\r\n";
			$message.="\r\n". $detail. "\r\n";
			$message.="\r\n". $footer;
			$fromaddress=$CFG->schoolname;
			if($Contacts and $CFG->emailoff!='yes' and $CFG->emailguardiancomments=='yes'){
				if(sizeof($Contacts)>0){
					foreach($Contacts as $index => $Contact){
						$emailaddress=strtolower($Contact['EmailAddress']['value']);
						send_email_to($emailaddress,$fromaddress,$subject,$message);
						$result[]=get_string('emailsentto').' '. 
							get_string(displayEnum($Contact['Relationship']['value'],'relationship'),'infobook'). 
							' '.$Contact['Surname']['value'];
						}
					}
				}
			}
		}

include('scripts/results.php');	
include('scripts/redirect.php');	
?>
