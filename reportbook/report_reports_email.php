<?php
/**									report_reports_publish.php
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

	/*find the definition specific to each report */
	$reportdefs=array();
	$wrapper_rid=$rids[0];/*should be first in rids*/
	for($c=0;$c<sizeof($rids);$c++){ 
		//trigger_error('rid'.$rids[$c],E_USER_WARNING);
		$reportdefs[]=fetchReportDefinition($rids[$c]);
		}

	/*doing one student at a time*/
	for($c=0;$c<sizeof($sids);$c++){
		$sid=$sids[$c];
		$Student=fetchStudent_short($sid);
		$Contacts=fetchContacts($sid);

		$body='Please find attached the term report for '.$Student['DisplayFullName']['value'].' and the latest edition of the school newsletter.';
		$subject='Saint Michael\'s College report for '.$Student['DisplayFullName']['value'];
		$fromaddress='smc@classforschools.com';
		$filename='Report_'.$Student['Surname']['value'].'_'.$sid.'_'.$wrapper_rid.'.pdf';
		$attachments=array();
		$attachments[]=array('filepath'=>$CFG->installpath.'/pdfreports/'.$filename,
							 'filename'=>$filename);
		//$attachments[]=array('filepath'=>$CFG->installpath.'/pdfreports/smc_newsletter.pdf',
		//					 'filename'=>'newsletter.pdf');

		while(list($index,$Contact)=each($Contacts)){
			$mailing=$Contact['ReceivesMailing']['value'];
			if(($mailing=='1' or $mailing=='2') and $Contact['EmailAddress']['value']!=''){
				$recipient=$Contact['EmailAddress']['value'];
				//$recipient='stj@laex.org';
				send_email_to($recipient,$fromaddress,$subject,$body,'',$attachments);
				$result[]=get_string('reportsemailed').': '.$recipient;
				}
			}
		}
	include('scripts/results.php');
	include('scripts/redirect.php');
?>
