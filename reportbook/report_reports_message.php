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
				$message=get_string('epfreportemail1');
				$message.= "\r\n".$CFG->eportfoliosite ."\r\n";
				$message.=get_string('epfreportemail1bis');
				$message.= "\r\n". 'Your username is: ' .$Contact['EPFUsername']['value']. "\r\n";
				//$message.= "\r\n". 'Your password is: ' .$password. "\r\n";
				if(isset($CFG->eportfolio_access) and $CFG->eportfolio_access=='dob'){$message.=get_string('epfreportemail2dob');}
				else{$message.=get_string('epfreportemail2no');}

				if(get_string('epfreportemail3')!='[[epfreportemail3]]'){
					$message.="\r\n"."\r\n".get_string('epfreportemail3');
					$message.= "\r\n".'Su nombre de usuario es: '.$Contact['EPFUsername']['value']. "\r\n";
					if(isset($CFG->eportfolio_access) and $CFG->eportfolio_access=='dob'){$message.=get_string('epfreportemail4dob');}
					else{$message.=get_string('epfreportemail4no');}
					}

				$footer='--'. "\r\n" .get_string('guardianemailfooterdisclaimer');
				$message.="\r\n". $footer;

				send_email_to($recipient,'',$subject,$message,'');
				}
			}
		}

	include('scripts/results.php');
	include('scripts/redirect.php');
?>
