<?php
/**									eportfolio_message.php
 *
 */

$action='eportfolio_accounts.php';

include('scripts/sub_action.php');

$d_s=mysql_query("SELECT student.id FROM info JOIN student ON student.id=info.student_id WHERE info.enrolstatus='C' AND student.form_id!='' AND student.yeargroup_id!='';");
while($student=mysql_fetch_array($d_s,MYSQL_ASSOC)){
	$sids[]=$student['id'];
	}

$sentno=0;$failno=0;
/*doing one student at a time*/
for($c=0;$c<sizeof($sids);$c++){
	$Contacts=array();
	$sid=$sids[$c];
	$Student=fetchStudent_short($sid);
	/*TODO: fetching the password is not working for student's
	 *  with siblings and should be moved out of here to ClaSSic. 
	 */
	if(isset($CFG->eportfolio_access) and $CFG->eportfolio_access=='dob'){
		$dob=(array)explode('-',$Student['DOB']['value']);
		$password=$dob[2].$dob[1].$dob[0];
		}
	else{
		$enn=fetchStudent_singlefield($sid,'EnrolNumber');
		$password=good_strtolower($enn['EnrolNumber']['value']);
		}

	$len=strlen($password);
	while($len<5){
		$password='0'.$password;
		$len=strlen($password);
		}

	//setlocale(LC_CTYPE,'es_ES.utf8');
	$studentname=$Student['DisplayFullName']['value'];
	$studentname=iconv('UTF-8','ISO-8859-1',$Student['DisplayFullName']['value']);
	//$studentname=iconv('UTF-8','ASCII//TRANSLIT//IGNORE',$Student['DisplayFullName']['value']);
	//$studentname=utf8_to_ascii($studentname);
	$Contacts=(array)fetchContacts($sid);

	$subject='Access to Classic for '.$CFG->schoolname;

	while(list($index,$Contact)=each($Contacts)){
		$mailing=$Contact['ReceivesMailing']['value'];
		if(($mailing=='1' or $mailing=='2') and $Contact['EmailAddress']['value']!='' 
		   and $Contact['EPFUsername']['value']!=''){
			$recipient=$Contact['EmailAddress']['value'];
			//$recipient='stj@learningdata.ie';
			$message='<p>'.get_string('epfreportemail1').'</p>';
			$message.= '<p><a href="'.$CFG->eportfoliosite .'">'.$CFG->eportfoliosite .'</a></p>';
			$message.='<p>'.get_string('epfreportemail1bis').'</p>';
			$message.= '<p>Your username is: ' .$Contact['EPFUsername']['value']. '</p>';
			//$message.= '<p>Your password is: ' .$password. '</p>';
			if(isset($CFG->eportfolio_access) and $CFG->eportfolio_access=='dob'){$message.='<p>'.get_string('epfreportemail2dob').'</p>';}
			else{$message.='<p>'.get_string('epfreportemail2no').'</p>';}

			if(get_string('epfreportemail3')!='[[epfreportemail3]]'){
				$message.='<br /><p>'.get_string('epfreportemail3').'</p>';
				$message.= '<p>Su nombre de usuario es: '.$Contact['EPFUsername']['value']. '</p>';
				if(isset($CFG->eportfolio_access) and $CFG->eportfolio_access=='dob'){$message.='<p>'.get_string('epfreportemail4dob').'</p>';}
				else{$message.='<p>'.get_string('epfreportemail4no').'</p>';}
				}

			$templates=getTemplates('tmp','default');
			if(count($templates)>0){
				$content['{{content}}']=$message;
				$tags=getTags(true,'student',array('student_id'=>$recipient['sid'],'guardian_id'=>$recipient['gid']));
				$tags=array_merge($tags,$content);
				$messagebody=getMessage($tags,$message,'default');
				}
			else{$messagebody=$message;}

			$messagebodytxt=strip_tags(html_entity_decode($messagebody, ENT_QUOTES, 'UTF-8'));

			$messagehtml='<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd"><html><head><meta http-equiv="Content-Type" content="text/html; charset=utf-8" />';
			$messagehtml.=$messagebody;
			$messagehtml.='</body></html>';

			$messagetxt='';
			$messagetxt.=$messagebodytxt;

			$email_result=send_email_to($recipient,'',$subject,$messagetxt,$messagehtml);
			if($email_result){$sentno++;}
			else{$failno++;}
			}
		}
	}

	$result[]="Messages: ".$sentno." sent and ".$failno." fails";
	include('scripts/results.php');
	include('scripts/redirect.php');
?>
