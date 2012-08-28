<?php
/**									comments_new_action.php
 *
 */

$action='comments_list.php';

if(isset($_POST['commentid'])){$commentid=$_POST['commentid'];}else{$commentid=-1;}
$detail=clean_text($_POST['detail']);
$entrydate=$_POST['entrydate'];
if(isset($_POST['bid']) and $_POST['bid']!=''){$bid=$_POST['bid'];}else{$bid='G';}
if(isset($_POST['catid'])){$catid=$_POST['catid'];}else{$catid='';}
if(isset($_POST['ratvalue'])){$ratvalue=$_POST['ratvalue'];}else{$ratvalue='N';}
if(isset($_POST['newyid'])){$newyid=$_POST['newyid'];}else{$newyid=$Student['YearGroup']['value'];}
if(isset($_POST['guardianemail0'])){$guardianemail=$_POST['guardianemail0'];}else{$guardianemail='no';}
if(isset($_POST['teacheremail0'])){$teacheremail=$_POST['teacheremail0'];}else{$teacheremail='no';}
if(isset($_POST['senemail0'])){$senemail=$_POST['senemail0'];}else{$senemail='no';}


include('scripts/sub_action.php');

	if($bid=='%'){$bid='G';}
	$category=$catid.':'.$ratvalue.';';
	$yid=$Student['YearGroup']['value'];
	$teachername=get_teachername($tid);
	$Student=array_merge($Student,fetchStudent_singlefield($sid,'Boarder'));
	$Student=array_merge($Student,fetchStudent_singlefield($sid,'EPFUsername'));

	if($commentid!='-1'){
		mysql_query("UPDATE comments SET student_id='$sid',
		detail='$detail', entrydate='$entrydate', yeargroup_id='$newyid',
		subject_id='$bid', category='$category', teacher_id='$tid' WHERE id='$commentid';");
		}
	else{
		mysql_query("INSERT INTO comments SET student_id='$sid',
		detail='$detail', entrydate='$entrydate', yeargroup_id='$newyid',
		subject_id='$bid', category='$category', teacher_id='$tid';");
		$commentid=mysql_insert_id();


		if(isset($_POST['sanction']) and $_POST['sanction']!=''){
			$sanction_catid=$_POST['sanction'];
			$closed=$_POST['closed'];
			$sanctiondetail=clean_text($_POST['sanctiondetail']);

			list($sratingnames,$sanction_catdefs)=fetch_categorydefs('inc');
			if(array_key_exists($sanction_catid,$sanction_catdefs)){$sanctionname=$sanction_catdefs[$catid]['name'];}
			else{$sanctionname='';}
			$sanction=$sanction_catid . ':;';

			mysql_query("INSERT INTO incidents SET student_id='$sid',
							detail='$detail - $sanctiondetail', entrydate='$entrydate', yeargroup_id='$newyid',
							subject_id='$bid', category='$sanction', teacher_id='$tid', closed='$closed'");
			$incid=mysql_insert_id();
			mysql_query("UPDATE comments SET incident_id='$incid' WHERE id='$commentid';");
			}
		elseif(isset($_POST['points']) and $_POST['points']!=''){
			$pointsvalue=$_POST['points'];
			$activity=$_POST['activity'];
			$curryear=get_curriculumyear();
			$d_rating=mysql_query("SELECT descriptor FROM rating WHERE name='meritpoints' AND value='$pointsvalue';");
			if(mysql_num_rows($d_rating)>0){
				$pointsresult=mysql_result($d_rating,0);
				}
			mysql_query("INSERT INTO merits (teacher_id,student_id,date,year,activity,value,result,detail,subject_id) 
							VALUES ('$tid','$sid','$entrydate','$curryear','$activity','$pointsvalue',
									'$pointsresult','$detail','$bid');");
			$merid=mysql_insert_id();
			mysql_query("UPDATE comments SET merit_id='$merid' WHERE id='$commentid';");
			}


		/* Message to all relevant teaching staff. */
		$teachergroup='p';
		if($teacheremail=='yes'){$teachergroup.='a';}
		if($senemail=='yes'){$teachergroup.='s';}

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
			if($catid!=''){
				$d_c=mysql_query("SELECT comment FROM categorydef WHERE id='$catid' AND type='con';");
				$userlist=mysql_result($d_c,0);
				$usernames=(array)explode(':::',$userlist);
				$teachers=array();
				foreach($usernames as $username){
					if($username!=''){
						$teachers[]=get_user($username,'username');
						}
					}
				if(sizeof($teachers)>0){
					$result1=(array)message_student_teachers($sid,$tid,$bid,$messagesubject,$messagetxt,$message,$teachers);
					$result=array_merge($result,$result1);
					}
				}
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
					mysql_query("UPDATE comments SET guardians='1' WHERE id='$commentid';");
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
	if($commentid!='' and $guardianemail=='yes' and $CFG->emailguardiancomments=='epf' ){
		require_once($CFG->dirroot.'/lib/eportfolio_functions.php');
		$epfu=$Student['EPFUsername']['value'];
		$title='Subject: ' .display_subjectname($bid);
		$message='<p>'.$detail.'</p>';
		if($CFG->eportfolio_db!='' and $epfu!=''){
			/* Set guardians field in comments table to 1 to indicate shared. */
			mysql_query("UPDATE comments SET guardians='1' WHERE id='$commentid';");
	   		elgg_new_comment($epfu,$entrydate,$message,$title,$tid);
   			$result[]='Shared with parents.';
			}
		}

include('scripts/results.php');	
include('scripts/redirect.php');	
?>
