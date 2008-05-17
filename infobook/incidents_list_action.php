<?php
/**									incidents_list_action.php
 */

$action='incidents_list.php';

$yid=$Student['NCyearActual']['id_db'];
$incid=$_POST['id_db'];
$actionno=$_POST['no_db'];
$detail=clean_text($_POST['detail']);
$entrydate=$_POST['entrydate'];
$closed=$_POST['closed'];
if(isset($_POST['bid'])){$bid=$_POST['bid'];}else{$bid='G';}
if(isset($_POST['catid'])){$catid=$_POST['catid'];}else{$catid='';}
if(isset($_POST['ratvalue'])){$ratvalue=$_POST['ratvalue'];}else{$ratvalue='';}
if(isset($_POST['newyid'])){$newyid=$_POST['newyid'];}else{$newyid=$yid;}

include('scripts/sub_action.php');


	if($bid=='%'){$bid='G';}
	$category=$catid . ':' . $ratvalue . ';';
	if($incid!='' and $actionno==''){
		mysql_query("UPDATE incidents SET student_id='$sid',
		detail='$detail', entrydate='$entrydate', yeargroup_id='$newyid',
		subject_id='$bid', category='$category',
		teacher_id='$tid' WHERE id='$incid'");
		}
	elseif($incid=='' and $actionno==''){
		mysql_query("INSERT INTO incidents SET student_id='$sid',
			detail='$detail', entrydate='$entrydate', yeargroup_id='$newyid',
			subject_id='$bid', category='$category',
							teacher_id='$tid'");
		$incid=mysql_insert_id();
		$result[]=get_string('incidentrecorded',$book);
		$teachername=get_teachername($tid);

		$footer='--'. "\r\n" . get_string('pastoralemailfooterdisclaimer');
		$subject='Incident Report for '.$Student['Forename']['value']
				.' '.$Student['Surname']['value'].' ('. 
					$Student['RegistrationGroup']['value'].')';
		if($closed=='Y'){$status='Closed';}else{$status='Open';} 
		$message=$subject."\r\n". 'Status: '.$status."\r\n". 
				'Subject: '.display_subjectname($bid)."\r\n". 
				'Posted by '.$teachername. "\r\n";
		$message.="\r\n". $detail. "\r\n";
		$message.="\r\n". $footer;
		$fromaddress='ClaSS';

		$recipients=list_sid_responsible_users($sid,$bid);
		if($recipients and $CFG->emailoff!='yes' and $CFG->emailincidents=='yes'){
			if(sizeof($recipients)>0){
				foreach($recipients as $index => $recipient){
					$recipient['email']=strtolower($recipient['email']);
					send_email_to($recipient['email'],$fromaddress,$subject,$message);
					$result[]=get_string('emailsentto').' '.$recipient['username'];
					}
				}
			}

		$Contacts=(array)fetchContacts_emails($sid);
		$footer='--'. "\r\n" .get_string('guardianemailfooterdisclaimer');
		$message=$subject."\r\n". 'Subject: ' .display_subjectname($bid)."\r\n". 
				'Posted by '.$teachername. "\r\n";
		$message.="\r\n". $detail. "\r\n";
		$message.="\r\n". $footer;
		$fromaddress=$CFG->schoolname;

		if($Contacts and $CFG->emailoff!='yes' and $yid>6 
						and $CFG->emailguardianincidents=='yes'){
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

	elseif($actionno==-1){
		mysql_query("INSERT INTO incidenthistory SET incident_id='$incid',
		comment='$detail', entrydate='$entrydate', 
		category='$category', teacher_id='$tid'");
		}
	elseif($actionno!=''){
		mysql_query("UPDATE incidenthistory SET
		comment='$detail', entrydate='$entrydate', 
		category='$category', teacher_id='$tid' WHERE
		incident_id='$incid' AND entryn='$actionno'");
		}

	if($closed!=''){
		mysql_query("UPDATE incidents SET closed='$closed' WHERE id='$incid'");
		}

include('scripts/results.php');
include('scripts/redirect.php');	
?>
