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

		/* Find the details, assessments, etc. specific to each report */
		$reportdefs=array();
		for($c=0;$c<sizeof($rids);$c++){
			$reportdefs[]=fetch_reportdefinition($rids[$c]);
			}

		/* Doing one full report per student at a time */
		$Students=array();
		$Students['Student']=array();
		for($c=0;$c<sizeof($sids);$c++){
			$sid=$sids[$c];
			$Student=(array)fetchStudent_short($sid);
                        $Contacts=array();
			$d_g=mysql_query("SELECT guardian.language, guardian.nationality,gidsid.relationship, gidsid.priority 
								FROM gidsid JOIN guardian ON guardian.id=gidsid.guardian_id 
								WHERE gidsid.student_id='$sid' ORDER BY gidsid.priority ASC;");
			while($contact=mysql_fetch_array($d_g,MYSQL_ASSOC)){
				$Contact['Language']['value']=$contact['language'];
				$Contact['Nationality']['value']=$contact['nationality'];
				$Contact['Priority']['value']=$contact['priority'];
				$Contact['Relationship']['value']=$contact['relationship'];
				$Contacts['Contact'][]=$Contact;
				}
			$Student['Contacts']=$Contacts;
			$Reports=(array)fetchSubjectReports($sid,$reportdefs);
			/* reportdefs index 0 will be the wrapper if one is used */
			$Reports['CoverTitle']=$reportdefs[0]['report']['title'];
			$Reports['Coversheet']='yes';
			$Reports['Transform']=$reportdefs[0]['report']['transform'];
			$Reports['Paper']=$reportdefs[0]['report']['style'];
			$Student['Reports']=$Reports;
			$reportyear=$reportdefs[0]['report']['year']-1;
			if($reportdefs[0]['report']['attendancestartdate']=="0000-00-00"){$startdate=$reportyear.'-08-15';}else{$startdate=$reportdefs[0]['report']['attendancestartdate'];}
			$Student['Reports']['Attendance']=fetchAttendanceSummary($sid,$startdate,$reportdefs[0]['report']['date']);
			$Student['Reports']['Merits']['Total']=fetchMeritsTotal($sid,$reportdefs[0]['report']['year']);
			$Student['Reports']['Targets']=fetchTargets($sid);
			$Students['Student'][]=$Student;
			}
		$returnXML=$Students;
		$rootName='Students';
		}

require_once('../../scripts/http_end_options.php');
exit;
?>
