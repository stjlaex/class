<?php
/**									comments_list_action.php
 *
 */

$action='comments_list.php';

$id=$_POST['id_db'];
$detail=clean_text($_POST['detail']);
$entrydate=$_POST['entrydate'];
if(isset($_POST['bid']) and $_POST['bid']!=''){$bid=$_POST['bid'];}else{$bid='G';}
if(isset($_POST['catid'])){$catid=$_POST['catid'];}else{$catid='';}
if(isset($_POST['ratvalue'])){$ratvalue=$_POST['ratvalue'];}else{$ratvalue='N';}
if(isset($_POST['newyid'])){$newyid=$_POST['newyid'];}else{$newyid=$Student['YearGroup']['value'];}
if(isset($_POST['guardianemail0'])){$guardianemail=$_POST['guardianemail0'];}else{$guardianemail='no';}
if(isset($_POST['teacheremail0'])){$teacheremail=$_POST['teacheremail0'];}else{$teacheremail='no';}


include('scripts/sub_action.php');

	if($bid=='%'){$bid='G';}
	$category=$catid.':'.$ratvalue.';';
	$yid=$Student['YearGroup']['value'];
	$teachername=get_teachername($tid);
	$Student=array_merge($Student,fetchStudent_singlefield($sid,'Boarder'));
	$Student=array_merge($Student,fetchStudent_singlefield($sid,'EPFUsername'));

	if($id!=''){
		mysql_query("UPDATE comments SET student_id='$sid',
		detail='$detail', entrydate='$entrydate', yeargroup_id='$newyid',
		subject_id='$bid', category='$category', teacher_id='$tid' WHERE id='$id';");
		}
	else{
		mysql_query("INSERT INTO comments SET student_id='$sid',
		detail='$detail', entrydate='$entrydate', yeargroup_id='$newyid',
		subject_id='$bid', category='$category', teacher_id='$tid';");
		$id=mysql_insert_id();

		/* Message to relevant teaching staff. */
		if($teacheremail=='yes'){$teachergroup='%';}else{$teachergroup='p';}

		$messagesubject='Comment for '.$Student['Forename']['value'] .' '.$Student['Surname']['value'].' ('. 
					$Student['RegistrationGroup']['value'].')';
		$footer=get_string('pastoralemailfooterdisclaimer');

		/* Construct a html version of message */
		$message='<p>'.$messagesubject.'</p><p>Subject: '. display_subjectname($bid).'</p>'. 
				'<p>Posted by '.$teachername. '</p>';
		$message.='<p>'. $detail. '</p>';
		if($guardianemail=='yes' and ($Student['Boarder']['value']=='N' or $CFG->emailboarders=='yes')){
			$message.='<p>Note: this message has been shared with parents.</p>';
			}
		$message.='<br /><hr><p>'. $footer.'</p>';

		/* Plain text version of the message */
		$messagetxt=$messagesubject."\r\n".'Subject: '. display_subjectname($bid)."\r\n". 
				'Posted by '.$teachername. "\r\n";
		$messagetxt.=$detail. "\r\n";
		if($guardianemail=='yes' and ($Student['Boarder']['value']=='N' or $CFG->emailboarders=='yes')){
			$messagetxt.='Note: this message has been shared with parents.'."\r\n";
			}
		$messagetxt.="\r\n". '--'. "\r\n" . $footer;

		/* Option to message teachers */
		if($CFG->emailcomments=='yes'){
			$result=(array)message_student_teachers($sid,$tid,$bid,$messagesubject,$messagetxt,$message,$teachergroup);
			}

		/* Option to message parents. */
		if($guardianemail=='yes' and ($Student['Boarder']['value']=='N' or $CFG->emailboarders=='yes')){
			$Contacts=(array)fetchContacts_emails($sid);
			$footer=get_string('guardianemailfooterdisclaimer');
			$message='<p>'.$messagesubject.'</p><p>'. 'Subject: ' .display_subjectname($bid).'</p>'. 
				'<p>Posted by '.$teachername. '</p>';
			$message.='<p>'. $detail. '</p>';
			$message.='<br /><hr><p>'. $footer.'</p>';

			/* Plain text version of the message */
			$messagetxt=$messagesubject."\r\n".'Subject: '. display_subjectname($bid)."\r\n". 
				'Posted by '.$teachername. "\r\n";
			$messagetxt.=$detail. "\r\n";
			$messagetxt.="\r\n". '--'. "\r\n" . $footer;


			if($Contacts and $CFG->emailoff!='yes' and $CFG->emailguardiancomments=='yes'){
				if(sizeof($Contacts)>0){
					mysql_query("UPDATE comments SET guardians='1' WHERE id='$id';");
					foreach($Contacts as $index => $Contact){
						$emailaddress=strtolower($Contact['EmailAddress']['value']);
						send_email_to($emailaddress,'',$messagesubject,$messagetxt,$message);
						$result[]=get_string('emailsentto','infobook').' '. 
							get_string(displayEnum($Contact['Relationship']['value'],'relationship'),'infobook'). 
							' '.$Contact['Surname']['value'];
						}
					}
				}
			}
		}

	/**
	 * This could be needed after an edit or for a new comment which
	 * is why its outside the above condition.
	 */

	if($id!='' and $guardianemail=='yes' and $CFG->emailguardiancomments=='epf' ){
		require_once($CFG->dirroot.'/lib/eportfolio_functions.php');

		$epfu=$Student['EPFUsername']['value'];
		$title='Subject: ' .display_subjectname($bid);
		$message='<p>'.$detail.'</p>';
		if($CFG->eportfolio_db!='' and $epfu!=''){
			/* Set guardians field in comments table to 1 to indicate shared. */
			mysql_query("UPDATE comments SET guardians='1' WHERE id='$id';");
	   		elgg_new_comment($epfu,$entrydate,$message,$title,$tid);
   			$result[]='Shared with parents.';
			}

		}

include('scripts/results.php');	
include('scripts/redirect.php');	
?>
