#! /usr/bin/php -q
<?php
/**
 *			   					httpscripts/medical_notice_cron.php
 *
 */

$book='medical';
$current='medical_notice_cron.php';

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
	$sentno=0;$failno=0;
	$footer=get_string('guardianemailfooterdisclaimer');
	$schoollogo='<img id="schoollogo" src="//'.$CFG->siteaddress.$CFG->sitepath.'/images/'.$CFG->schoollogo.'" style="display:block;margin:0 auto;max-width:180px;padding:2%;">';

	/*Tags*/
	$template_tags=array();
	$template_tags['{{schoollogo}}']='<a id="schoollink" href="">'.$schoollogo.'</a>';
	//$template_tags['{{title}}']='Medical Update notice';
	$template_tags['{{rightside}}']=$CFG->schoolname;
	$template_tags['{{content}}']='';
	$template_tags['{{footer}}']=$footer;

	/*Select the events for medical*/
	$d_e=mysql_query("SELECT * FROM (SELECT * FROM student_event
								 WHERE type='medical' AND status='0' AND catid!=''
								 ORDER BY student_id ASC,timestamp DESC)
						  AS b GROUP BY b.student_id;");
	while($events=mysql_fetch_array($d_e, MYSQL_ASSOC)){
		$sid=$events['student_id'];
		/*Section, medical users and students*/
		$sectionid=get_student_section($sid);
		$users=list_medical_users($sectionid);
		$Student=(array)fetchStudent_short($sid);
		//$med=fetchMedical($sid);
		$d_c=mysql_query("SELECT * FROM categorydef WHERE id='".$events['catid']."';");
		$m=mysql_fetch_row($d_c);
		$content=$m['6'];
		$d_t=mysql_query("SELECT * FROM categorydef WHERE type='tmp' AND name='default';");
		$t=mysql_fetch_row($d_t);
		$template=$t['6'];
		$template_tags['{{content}}']=$content;
		$template_tags['{{title}}']=$m['3'];
		$content_tags=array();
		$message=strtr($template,$template_tags);
		/*$cats=explode(';',$events['event']);
		$content_tags['{{medicalinfo}}']='';
		foreach($cats as $cat){
			if($cat!=''){
				$event_data=explode(':::',$cat);
				$categorytype=$event_data[0];
				$oldvalue=$event_data[1];
				$newvalue=$event_data[2];
				$lev=levenshtein(strtolower($oldvalue), strtolower($newvalue));
				$percent=(1-$lev/max(strlen($oldvalue), strlen($newvalue)))*100;
				foreach($med['Notes']['Note'] as $note){
					if($note['Detail']['value']!='' and $categorytype==$note['MedicalCategory']['value_db']){
						if($oldvalue=='' and $newvalue!=''){
							$medcat=$note['MedicalCategory']['value'];
							$medvalue=$note['Detail']['value'];
							$content_tags['{{medicalinfo}}'].='<strong>'.$medcat.'</strong>: "'.$medvalue.'" (new).';
							}
						elseif($oldvalue!='' and $percent<50){$content_tags['{{medicalinfo}}']='<strong>'.$note['MedicalCategory']['value'].'</strong>: from "'.$oldvalue.'" to "'.$newvalue.'".';}
						}
					}
				}
			}*/
		/*Tags for student fields*/
		foreach($Student as $key=>$value){
			if($key=='Forename' or $key=='Surname'){$value['value']='<strong>'.$value['value'].'</strong>';}
			elseif($key=='EnrolmentStatus'){$value['value']='<strong>'.get_string(displayEnum($enrolstatus[1],'enrolstatus')).'</strong>';}
			//$content_tags['{{Student'.$key.'}}']=$value['value'];
			$content_tags['{{'.$key.'}}']=$value['value'];
			}
		/*Tags for users and recipients*/
		foreach($users as $user){
			$User=(array)fetchUser($user['uid']);
			$recipient=array();
			$email=strtolower($User['EmailAddress']['value']);
			if(!empty($email)){
				$content_tags['{{UserTitle}}']=get_string(displayEnum($User['Title']['value'],'title'),'infobook');
				$content_tags['{{UserForename}}']=$User['Forename']['value'];
				$content_tags['{{UserSurname}}']=$User['Surname']['value'];
				$fullmessage=strtr($message,$content_tags);

				$recipient['name']=$User['DisplayFullName']['value'];
				$recipient['email']=$email;
				$recipient['sid']=$sid;
				$recipient['subject']=$template_tags['{{title}}'];
				$recipient['body']=$fullmessage;
				$recipient['Student']=$Student;
				$recipient['User']=$User;

				$recipients[]=$recipient;
				$sid_recipient_no++;
				}
			elseif($User['EmailAddress']['value']==''){
				$email_blank_uids[]=$User['id_db'];
				}
			}

		/*Create message events for recipients*/
		$replyto=$CFG->emailnoreply[0];
		$from=array('name'=>$CFG->schoolname,'email'=>$replyto);
		$attachments=array();
		foreach($recipients as $key => $recipient){
			$messagesubject=clean_text($recipient['subject']);

			$messagebodytxt=strip_tags(html_entity_decode($messagebody, ENT_QUOTES, 'UTF-8'));
			$messagetxt='';
			$messagetxt.=$messagebodytxt;
			$messagetxt.="\r\n". '--'. "\r\n" . $footer;

			$messagebody=$recipient['body'];
			$messagehtml=$messagebody;

			$email_result=send_email_to($recipient['email'],$from,$messagesubject,$messagetxt,$messagehtml,$attachments);
			if($email_result){
				$sentno++;
				mysql_query("UPDATE student_event SET status='1' WHERE type='medical' AND student_id='$sid';");
				}
			else{$failno++;}
			}

		$result[]=get_string('emailsentto',$book).' '. $sentno.' and '.$failno;
		}
	}

require_once($CFG->installpath.'/'.$CFG->applicationdirectory.'/scripts/cron_end_options.php');
?>
