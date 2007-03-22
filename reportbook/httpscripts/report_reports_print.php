<?php
/**			   					httpscripts/report_reports_print.php
 */

require_once('../../scripts/http_head_options.php');

if(isset($_GET['sids'])){$sids=(array) $_GET['sids'];}else{$sids=array();}
if(isset($_POST['sids'])){$sids=(array) $_POST['sids'];}
if(isset($_GET['rids'])){$rids=(array) $_GET['rids'];}else{$rids=array();}
if(isset($_POST['rids'])){$rids=(array) $_POST['rids'];}

	if(sizeof($sids)==0){
		$result[]=get_string('youneedtoselectstudents');
		$returnXML=$result;
		$rootName='Error';
		}
	else{
		/*find the details, assessments, etc. specific to each report */
		$reportdefs=array();
		for($c=0;$c<sizeof($rids);$c++){
			$reportdefs[]=fetchReportDefinition($rids[$c]);
			}

		$Students=array();
		$Students['Student']=array();
		/*doing one student at a time*/
		for($c=0;$c<sizeof($sids);$c++){
			$sid=$sids[$c];
			$Student=fetchStudent_short($sid);
			list($Reports,$transform)=fetchSubjectReports($sid,$reportdefs);
			$Reports['Coversheet']='yes';
			$Student['Reports']=nullCorrect($Reports);
			/*Finished with the student's reports. Output the result as xml.*/
			//xmlechoer('Student',$Student);
			$Students['Student'][]=$Student;
			}
		$returnXML=$Students;
		$rootName='Students';
		}

require_once('../../scripts/http_end_options.php');
exit;
?>
