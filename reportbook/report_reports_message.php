<?php
/**									report_reports_message.php
 *
 */

$action='report_reports.php';
$action_post_vars=array('rids');


if(isset($_POST['sids'])){$sids=(array) $_POST['sids'];}else{$sids=array();}
if(isset($_POST['rids'])){$rids=(array) $_POST['rids'];}else{$rids=array();}

include('scripts/sub_action.php');

if(sizeof($sids)==0){
		$result[]=get_string('youneedtoselectstudents');
   		include('scripts/results.php');
   		include('scripts/redirect.php');
		exit;
		}

/*
 * TODO: 
 */
$report=false;

if(sizeof($rids)!=0){
	/*find the definition specific to each report */
	$reportdefs=array();
	$wrapper_rid=$rids[0];/*should be first in rids*/
	for($c=0;$c<sizeof($rids);$c++){ 
		$reportdefs[]=fetch_reportdefinition($rids[$c]);
		}
	$pubdate=$reportdefs[0]['report']['date'];
	//$report=true;
	}

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
		
		if($report){
			$subject='Report for '.$studentname;
			}
		else{
			$subject='Access to ClaSSic for '.$CFG->schoolname;
			}

		while(list($index,$Contact)=each($Contacts)){
			$mailing=$Contact['ReceivesMailing']['value'];
			if(($mailing=='1' or $mailing=='2') and $Contact['EmailAddress']['value']!='' 
			   and $Contact['EPFUsername']['value']!=''){
				$recipient=$Contact['EmailAddress']['value'];
				//$recipient='stj@laex.org';
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

				$footer=get_string('guardianemailfooterdisclaimer');
				$messagetxt=strip_tags(html_entity_decode($message, ENT_QUOTES, 'UTF-8'))."\r\n".'--'. "\r\n" . $footer;
				$message.='<br /><hr><p>'. $footer.'<p>';

				send_email_to($recipient,'',$subject,$messagetxt,$message);
				}
			}
		}

	include('scripts/results.php');
	include('scripts/redirect.php');
?>
