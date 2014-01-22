#! /usr/bin/php -q
<?php
/**
 *			   					httpscripts/student_enrolment_notice_cron.php
 *
 */

$book='infobook';
$current='student_enrolment_notice_cron.php';

/* The path is passed as a command line argument. */
function arguments($argv) {
    $ARGS = array();
    foreach($argv as $arg){
		if(ereg('--([^=]+)=(.*)',$arg,$reg)){
			$ARGS[$reg[1]] = $reg[2];
			} 
		elseif(ereg('-([a-zA-Z0-9])',$arg,$reg)){
            $ARGS[$reg[1]] = 'true';
			}
		}
	return $ARGS;
	}
$ARGS=arguments($_SERVER['argv']);
require_once($ARGS['path'].'/school.php');
require_once($CFG->installpath.'/'.$CFG->applicationdirectory.'/scripts/cron_head_options.php');

if($CFG->emailoff!='yes'){
	$curryear=get_curriculumyear();
	$entryyear=$curryear-1;
	$sentno=0;$failno=0;
	$date=$entryyear.'-'.$CFG->enrol_cutoffmonth;
	$footer=get_string('guardianemailfooterdisclaimer');
	$schoollogo='<img id="schoollogo" src="http://'.$CFG->siteaddress.$CFG->sitepath.'/images/'.$CFG->schoollogo.'" style="display:block;margin:0 auto;max-width:180px;padding:2%;">';

	$template_tags=array();
	$template_tags['{{schoollogo}}']='<a id="schoollink" href="">'.$schoollogo.'</a>';
	//$template_tags['{{title}}']='Enrolment Update notice';
	$template_tags['{{rightside}}']=$CFG->schoolname;
	$template_tags['{{content}}']='';
	$template_tags['{{footer}}']=$footer;

	$d_e=mysql_query("SELECT * FROM (SELECT * FROM student_event WHERE timestamp>='$date-01 00:00:00' 
							AND type='enrolstatus' AND status='0' AND catid!='' 
							ORDER BY student_id ASC,timestamp DESC) AS b GROUP BY b.student_id;");
	while($events=mysql_fetch_array($d_e, MYSQL_ASSOC)){
		$sid=$events['student_id'];
		$d_s=mysql_query("SELECT * FROM student_event WHERE timestamp>='$date-01 00:00:00' 
							AND type='enrolstatus' AND status='1' AND catid!='' AND student_id='$sid';");
		$sent=array();
		while($s=mysql_fetch_array($d_s, MYSQL_ASSOC)){
			$sent[]=$s;
			}
		if(count($sent)==0){
			$Student=(array)fetchStudent_short($sid);
			$d_c=mysql_query("SELECT * FROM categorydef WHERE id='".$events['catid']."';");
			$m=mysql_fetch_row($d_c);
			$content=$m['6'];
			$d_t=mysql_query("SELECT * FROM categorydef WHERE type='tmp' AND name='default';");
			$t=mysql_fetch_row($d_t);
			$template=$t['6'];
			$template_tags['{{content}}']=$content;
			$template_tags['{{title}}']=$m['3'];
			$message=strtr($template,$template_tags);
			$enrolstatus=explode(':::',$events['event']); 
			$content_tags=array();
			foreach($Student as $key=>$value){
				if($key=='Forename' or $key=='Surname'){$value['value']='<strong>'.$value['value'].'</strong>';}
				elseif($key=='EnrolmentStatus'){$value['value']='<strong>'.get_string(displayEnum($enrolstatus[1],'enrolstatus')).'</strong>';}
				$content_tags['{{'.$key.'}}']=$value['value'];
				}

			$Contacts=(array)fetchContacts($sid);
			foreach($Contacts as $Contact){
				$recipient=array();
				if($Contact['ReceivesMailing']['value']=='1'){
					$email=strtolower($Contact['EmailAddress']['value']);
					if(!empty($email)){
						$content_tags['{{Relationship}}']=get_string(displayEnum($Contact['Relationship']['value'],'relationship'),'infobook');
						$content_tags['{{ContactTitle}}']=get_string(displayEnum($Contact['Title']['value'],'title'),'infobook');
						$content_tags['{{ContactForename}}']=$Contact['Forename']['value'];
						$content_tags['{{ContactSurname}}']=$Contact['Surname']['value'];
						$fullmessage=strtr($message,$content_tags);

						$recipient['name']=$Contact['DisplayFullName']['value'];
						$recipient['email']=$email;
						$recipient['sid']=$sid;
						$recipient['subject']=$template_tags['{{title}}'];
						$recipient['body']=$fullmessage;
						$recipient['Student']=$Student;
						$recipient['Contact']=$Contact;
						$recipient['Siblings']=(array)fetchDependents($Contact['id_db']);

						$recipients[]=$recipient;
						$sid_recipient_no++;
						}
					elseif($Contact['EmailAddress']['value']==''){
						$email_blank_gids[]=$Contact['id_db'];
						}
					}
				}

			foreach($recipients as $key => $recipient){
				$messagesubject=clean_text($recipient['subject']);

				$messagebodytxt=strip_tags(html_entity_decode($messagebody, ENT_QUOTES, 'UTF-8'));
				$messagetxt='';
				$messagetxt.=$messagebodytxt;
				$messagetxt.="\r\n". '--'. "\r\n" . $footer;

				$messagebody=$recipient['body'];
				$messagehtml=$messagebody;

				$email_result=send_email_to($recipient['email'],'',$messagesubject,$messagetxt,$messagehtml);
				if($email_result){
					$sentno++;
					mysql_query("UPDATE student_event SET status='1' WHERE type='enrolstatus' AND student_id='$sid';");
					}
				else{$failno++;}
				}
			}
		else{
			mysql_query("UPDATE student_event SET status='1' WHERE type='enrolstatus' AND student_id='$sid';");
			}

		$result[]=get_string('emailsentto',$book).' '. $sentno.' and '.$failno;
		}
	}

require_once($CFG->installpath.'/'.$CFG->applicationdirectory.'/scripts/cron_end_options.php');
?>
