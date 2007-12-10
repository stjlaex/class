<?php
/**			   					httpscripts/report_reports_print.php
 */

require_once('../../scripts/http_head_options.php');

if(isset($_GET['sids'])){$sids=(array) $_GET['sids'];}else{$sids=array();}
if(isset($_POST['sids'])){$sids=(array) $_POST['sids'];}
if(isset($_GET['rids'])){$rids=(array) $_GET['rids'];}else{$rids=array();}
if(isset($_POST['rids'])){$rids=(array) $_POST['rids'];}

if(isset($_GET['wrapper_rid'])){$wrapper_rid=$_GET['wrapper_rid'];}else{$wrapper_rid='';}
if(isset($_POST['wrapper_rid'])){$wrapper_rid=$_POST['wrapper_rid'];}

	if(sizeof($sids)==0){
		$result[]=get_string('youneedtoselectstudents');
		$returnXML=$result;
		$rootName='Error';
		}
	else{
		if($wrapper_rid!=''){
			$d_rid=mysql_query("SELECT categorydef_id AS report_id FROM ridcatid WHERE
				 report_id='$wrapper_rid' AND subject_id='wrapper' ORDER BY categorydef_id");
			$rids=array();
			$rids[]=$wrapper_rid;
			while($rid=mysql_fetch_array($d_rid,MYSQL_ASSOC)){
				$rids[]=$rid['report_id'];
				}
			}

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
			$Reports['Transform']=$transform;
			$Reports['Paper']=$reportdefs[0]['report']['style'];
			$Student['Reports']=nullCorrect($Reports);
			$Students['Student'][]=$Student;
			}
		$returnXML=$Students;
		$rootName='Students';
		}

require_once('../../scripts/http_end_options.php');
exit;
?>