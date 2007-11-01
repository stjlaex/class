<?php
/**		   					httpscripts/report_summary_preview.php
 */

require_once('../../scripts/http_head_options.php');

if(isset($_GET['sids'])){$sids=(array) $_GET['sids'];}else{$sids=array();}
if(isset($_POST['sids'])){$sids=(array) $_POST['sids'];}
if(isset($_GET['rids'])){$rids=(array) $_GET['rids'];}else{$rids=array();}
if(isset($_POST['rids'])){$rids=(array) $_POST['rids'];}
if(isset($_GET['bid'])){$bid=$_GET['bid'];}else{$bid=array();}
if(isset($_POST['bid'])){$bid=$_POST['bid'];}
if(isset($_GET['pid'])){$pid=$_GET['pid'];}else{$bid=array();}
if(isset($_POST['pid'])){$pid=$_POST['pid'];}

	if(sizeof($sids)==0){
		$result[]=get_string('youneedtoselectstudents');
		$returnXML=$result;
		$rootName='Error';
		}
	else{
		/* Find the details, assessments, etc. specific to each report. */
		$reportdefs=array();
		$rid=$rids[0];

		$reportdefs[]=fetchReportDefinition($rid,$bid);

		$Students=array();
		$Students['Student']=array();
		/* Doing one student at a time.*/
		for($c=0;$c<sizeof($sids);$c++){
			$sid=$sids[$c];
			$Student=fetchStudent_short($sid);
			list($Reports,$transform)=fetchSubjectReports($sid,$reportdefs);
			$Reports['Coversheet']='yes';
			$Student['Reports']=nullCorrect($Reports);
			$Students['Student'][]=$Student;
			}
		$returnXML=$Students;
		$rootName='Students';
		}

require_once('../../scripts/http_end_options.php');
exit;
?>
