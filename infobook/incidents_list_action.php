<?php
/**									incidents_list_action.php
 */

$action='incidents_list.php';

$incid=$_POST['id_db'];
$actionno=$_POST['no_db'];
$detail=clean_text($_POST['detail']);
$entrydate=$_POST['entrydate'];
$closed=$_POST['closed'];
if(isset($_POST['bid'])){$bid=$_POST['bid'];}else{$bid='G';}
if(isset($_POST['catid'])){$catid=$_POST['catid'];}else{$catid='';}
if(isset($_POST['ratvalue'])){$ratvalue=$_POST['ratvalue'];}else{$ratvalue='';}
if(isset($_POST['newyid'])){$newyid=$_POST['newyid'];}else{$newyid=$Student['YearGroup']['value'];}

include('scripts/sub_action.php');


	if($bid=='%'){$bid='G';}
	list($ratingnames,$catdefs)=fetch_categorydefs('inc');
	if(array_key_exists($catid,$catdefs)){$sanction=$catdefs[$catid]['name'];}
	else{$sanction='';}
	$category=$catid . ':' . $ratvalue . ';';
	$teachername=get_teachername($tid);

	if($incid!='' and $actionno==''){
		mysql_query("UPDATE incidents SET student_id='$sid',
		detail='$detail', entrydate='$entrydate', yeargroup_id='$newyid',
		subject_id='$bid', category='$category',
		teacher_id='$tid' WHERE id='$incid'");
		}
	elseif($incid=='' and $actionno==''){
		mysql_query("INSERT INTO incidents SET student_id='$sid',
			detail='$detail', entrydate='$entrydate', yeargroup_id='$newyid',
			subject_id='$bid', category='$category', teacher_id='$tid'");
		$incid=mysql_insert_id();


		$footer=get_string('pastoralemailfooterdisclaimer');
		$subject='Incident Report for '.$Student['Forename']['value']
				.' '.$Student['Surname']['value'].' ('. 
					$Student['RegistrationGroup']['value'].')';
		if($closed=='Y'){$status='Closed';}else{$status='Open';}
		$message='<p>'.$subject.'</p><p>'. 'Status: '.$status.'</p>'. 
				'<p>Subject: '.display_subjectname($bid).'</p>'. 
				'<p>Posted by '.$teachername. '</p>';
		$message.='<p>'.get_string('sanction','infobook').': '.$sanction.'</p>';
		$message.='<p>'. $detail. '</p>';
		$messagetxt=strip_tags(html_entity_decode($message, ENT_QUOTES, 'UTF-8'))."\r\n".'--'. "\r\n" . $footer;
		$message.='<br /><hr><p>'. $footer.'<p>';

		/* Message to relevant pastoral teaching staff. */
		if($CFG->emailincidents=='yes'){
			$result=(array)message_student_teachers($sid,$tid,$bid,$subject,$messagetxt,$message,'p');
			}

		/* Optionaly send message to parents. */
		$Student=array_merge($Student,fetchStudent_singlefield($sid,'Boarder'));
		if($CFG->emailguardianincidents=='yes' and ($Student['Boarder']['value']=='N' 
													or $CFG->emailboarders=='yes')){
			$Contacts=(array)fetchContacts_emails($sid);
			$footer=get_string('guardianemailfooterdisclaimer');
			$message='<p>'.$subject. '<p>Subject: ' .display_subjectname($bid).'</p>'. 
				'<p>Posted by '.$teachername. '</p>';
			$message.='<p>'. $detail. '</p>';
			$messagetxt=strip_tags(html_entity_decode($message, ENT_QUOTES, 'UTF-8'))."\r\n".'--'. "\r\n" . $footer;
			$message.='<br /><hr><p>'. $footer.'<p>';

			$fromaddress=$CFG->schoolname;

			/*TODO: this is a hack to stop incidents to parents of primary children*/
			if($Contacts and $CFG->emailoff!='yes' and $CFG->emailguardianincidents=='yes' and $yid>6){
				if(sizeof($Contacts)>0){
					foreach($Contacts as $index => $Contact){
						$emailaddress=strtolower($Contact['EmailAddress']['value']);
						send_email_to($emailaddress,$fromaddress,$subject,$messagetxt,$message);
						$result[]=get_string('emailsentto').' '. 
							get_string(displayEnum($Contact['Relationship']['value'],'relationship'),'infobook').
							' '.$Contact['Surname']['value'];
						}
					}
				}
			}

		}

	elseif($actionno==-1){
		mysql_query("INSERT INTO incidenthistory SET incident_id='$incid',
						comment='$detail', entrydate='$entrydate', category='$category', teacher_id='$tid'");
		}
	elseif($actionno!=''){
		mysql_query("UPDATE incidenthistory SET comment='$detail', entrydate='$entrydate', 
						category='$category', teacher_id='$tid' WHERE incident_id='$incid' AND entryn='$actionno'");
		}

	if($closed!=''){
		mysql_query("UPDATE incidents SET closed='$closed' WHERE id='$incid'");

		if($closed=='Y'){
			/* Message original poster of incident of closure. */
			$d_i=mysql_query("SELECT teacher_id FROM incidents WHERE id='$incid';"); 
			$othertid=mysql_result($d_i,0);
			$teachers=array();
			$teachers[]=get_user($othertid);

			$footer=get_string('pastoralemailfooterdisclaimer');
			$subject='CLOSED: Incident for '.$Student['Forename']['value']
				.' '.$Student['Surname']['value'].' ('. $Student['RegistrationGroup']['value'].')';
			$message=$subject.'<p>Status: CLOSED </p>'. 
				'<p>Subject: '.display_subjectname($bid).'</p>'. 
				'<p>Posted by '.$teachername. '</p>';
			$message.='<p>'.get_string('sanction','infobook').': '.$sanction.'</p>';
			$message.='<p>'. $detail. '</p>';
			$messagetxt=strip_tags(html_entity_decode($message, ENT_QUOTES, 'UTF-8'))."\r\n".'--'. "\r\n" . $footer;
			$message.='<br /><hr><p>'. $footer.'<p>';

			$result=(array)message_student_teachers($sid,$tid,$bid,$subject,$messagetxt,$message,$teachers);
			}

		}

include('scripts/results.php');
include('scripts/redirect.php');	
?>
