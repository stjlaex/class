 <?php
/**									report_reports_email.php
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
	$pubdate=$reportdefs[0]['report']['date'];

	/*doing one student at a time*/
	for($c=0;$c<sizeof($sids);$c++){
		$sid=$sids[$c];
		$Student=fetchStudent_short($sid);
		$Contacts=fetchContacts($sid);

		setlocale(LC_CTYPE,'en_GB');
		$studentname=iconv('UTF-8', 'ASCII//TRANSLIT',$Student['DisplayFullName']['value']);

		$body='Please find attached the term report for '.$studentname.'.';
		$subject='King\'s Training Report for '.$studentname;
		$fromaddress='King\'s Training La Moraleja';
		$epfusername=get_epfusername($sid,$Student);
		$filename='Report'.$pubdate.'_'.$epfusername.'_'.$sid.'_'.$wrapper_rid.'.pdf';
		$attachments=array();
		$attachments[]=array('filepath'=>$CFG->installpath.'/reports/'.$filename,
							 'filename'=>$filename);
		/*		$attachments[]=array('filepath'=>$CFG->installpath.'/pdfreports/smc_newsletter.pdf',
							 'filename'=>'newsletter.pdf');
		*/

		while(list($index,$Contact)=each($Contacts)){
			$mailing=$Contact['ReceivesMailing']['value'];
			if(($mailing=='1' or $mailing=='2') and $Contact['EmailAddress']['value']!=''){
				$recipient=$Contact['EmailAddress']['value'];
				send_email_to($recipient,$fromaddress,$subject,$body,'',$attachments);
				$result[]=get_string('reportsemailed').': '.$recipient;
				}
			}
		}
	include('scripts/results.php');
	include('scripts/redirect.php');
?>
