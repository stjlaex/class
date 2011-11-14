 <?php
/**									report_reports_email.php
 * 
 * Can email a published report as an attachment to parents. Needs the
 * reports to be in files directory of dataroot and for students to
 * have an epfusername. Does not need epfdb.
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

	/*find the definition specific to each report */
	$reportdefs=array();
	$wrapper_rid=$rids[0];/*should be first in rids*/
	for($c=0;$c<sizeof($rids);$c++){ 
		$reportdefs[]=fetch_reportdefinition($rids[$c]);
		}
	$pubdate=$reportdefs[0]['report']['date'];

	for($c=0;$c<sizeof($sids);$c++){
		$sid=$sids[$c];
		$Student=fetchStudent_short($sid);
		$studentname=$Student['DisplayFullName']['value'];
		$Contacts=fetchContacts($sid);

		$bodytxt="\r\n";
		$bodytxt.='This is an automatic email sent on behalf of '. $CFG->schoolname.'.'."\r\n";
		$bodytxt.='Please find the academic report for '.$studentname.' attached.'."\r\n";
		$bodytxt.="\r\n";
		
		$body='<p>This is an automatic email sent on behalf of '. $CFG->schoolname.'</p>';
		$bodytxt.='<p>Please find the academic report for '.$studentname.' attached.</p>';
			
		$subject='Report for '.$studentname;
		$fromaddress='';
		$filename='Report'.$pubdate.'_'.$sid.'_'.$wrapper_rid.'.pdf';
		$attachments=array();

		$S=fetchStudent_singlefield($sid,'EPFUsername');
		$epfusername=$S['EPFUsername']['value'];
		if(!empty($epfusername)){
			$reportpath=$CFG->eportfolio_dataroot.'/files/' . substr($epfusername,0,1) . '/' . $epfusername;
			if(file_exists($reportpath.'/'.$filename)){
				$attachments[]=array('filepath'=>$reportpath.'/'.$filename,
									 'filename'=>$filename);
				foreach($Contacts as $Contact){
					$mailing=$Contact['ReceivesMailing']['value'];
					if(($mailing=='1' or $mailing=='2') and $Contact['EmailAddress']['value']!=''){
						$recipient=$Contact['EmailAddress']['value'];
						send_email_to($recipient,$fromaddress,$subject,$bodytxt,$body,$attachments);
						$result[]=get_string('reportsemailed').': '.$recipient;
						}
					}
				}
			else{
				trigger_error('Report file missing for: '.$reportpath.'/'.$filename,E_USER_WARNING);
				}
			}
		}
		
	include('scripts/results.php');
	include('scripts/redirect.php');
?>
