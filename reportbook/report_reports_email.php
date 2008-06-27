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
		$reportdefs[]=fetchReportDefinition($rids[$c]);
		}
	$pubdate=$reportdefs[0]['report']['date'];


if(isset($CFG->eportfolio_db) and $CFG->eportfolio_db!=''){
	include('lib/eportfolio_functions.php');
	$doing_epf=true;
	}

	/*doing one student at a time*/
	for($c=0;$c<sizeof($sids);$c++){
		$sid=$sids[$c];
		$Student=fetchStudent_short($sid);
		setlocale(LC_CTYPE,'en_GB');
		//$studentname=iconv('UTF-8','ASCII//TRANSLIT',$Student['DisplayFullName']['value']);
		$studentname=utf8_to_ascii($Student['DisplayFullName']['value']);
		$Contacts=fetchContacts($sid);

		$epfusername=get_epfusername($sid,$Student);
		if($doing_epf){

			$epfuid=elgg_get_epfuid($epfusername,'person',true);
			$epf_folder_id=elgg_new_folder($epfuid,'Reports','',true);

			$body="\r\n";
			$body.='This is an automatic email sent on behalf of King\'s College Madrid.'."\r\n";
			$body.='You can now find the term report for '.$studentname.' available on the ClaSSic website.'."\r\n";
			$body.='Please access the report directly using the following link:'."\r\n";
			$body.="\r\n";
			$body.='http://classforschools.com/classic/'.$epfusername.'/files/'.$epf_folder_id;
			$body.="\r\n";
			$body.='Access to the website is protected by a user-name and password, details of which you should have received previously by email.'."\r\n";
			$body.='--'."\r\n";
			$body.="\r\n";
			$body.='Este es un mensaje automatico enviado en nombre de King\'s College Madrid.'."\r\n";
			$body.='Tiene a su disposición el informe del trimestre '.$studentname.' en el website de ClaSSic'."\r\n";
			$body.='Puede acceder directamente al informe haciendo click en el siguiente enlace:'."\r\n";
			$body.="\r\n";
			$body.='http://classforschools.com/classic/'.$epfusername.'/files/'.$epf_folder_id;
			$body.="\r\n";
			$body.='El acceso al website está protegido con un nombre de usuario y una contraseña que Ud. debe haber recibido previamente por email.'."\r\n";

			$subject='Report for '.$studentname;
			$fromaddress='';
			$filename='Report'.$pubdate.'_'.$epfusername.'_'.$sid.'_'.$wrapper_rid.'.html';
			$attachments=array();
			/*
			$attachments[]=array('filepath'=>$CFG->installpath.'/reports/'.$filename,
							 'filename'=>$filename);
			*/
			while(list($index,$Contact)=each($Contacts)){
				$mailing=$Contact['ReceivesMailing']['value'];
				if(($mailing=='1' or $mailing=='2') and $Contact['EmailAddress']['value']!=''){
					//$recipient=$Contact['EmailAddress']['value'];
					$recipient='stj@laex.org';
					send_email_to($recipient,$fromaddress,$subject,$body,'',$attachments);
					$result[]=get_string('reportsemailed').': '.$recipient;
					}
				}
			}
		}

	include('scripts/results.php');
	include('scripts/redirect.php');
?>
