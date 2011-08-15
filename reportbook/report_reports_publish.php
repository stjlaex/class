<?php
/**
 *									report_reports_publish.php
 *
 * Publishes the selected reports to html files
 * and then schedules a cron event for pdf conversion 
 * (if the html2ps package is available through $CFG -> html2psscript)
 *
 */

$action='report_reports.php';
$action_post_vars=array('rids');

if(isset($_POST['sids'])){$sids=(array) $_POST['sids'];}else{$sids=array();}
if(isset($_POST['rids'])){$rids=(array) $_POST['rids'];}else{$rids=array();}

if(isset($_POST['wrapper_rid'])){$wrapper_rid=$_POST['wrapper_rid'];}else{$wrapper_rid=$rids[0];}

include('scripts/sub_action.php');

	if(sizeof($sids)==0){
		$result[]=get_string('youneedtoselectstudents');
   		include('scripts/results.php');
   		include('scripts/redirect.php');
		exit;
		}

	if($wrapper_rid!=''){
		$d_rid=mysql_query("SELECT categorydef_id AS report_id FROM ridcatid WHERE
				 report_id='$wrapper_rid' AND subject_id='wrapper' ORDER BY categorydef_id;");
		$rids=array();
		$rids[]=$wrapper_rid;
		while($rid=mysql_fetch_array($d_rid,MYSQL_ASSOC)){
			$rids[]=$rid['report_id'];
			}
		}


	/* Find the definition specific to each report */
	$reportdefs=array();
	for($c=0;$c<sizeof($rids);$c++){ 
		$reportdefs[]=fetch_reportdefinition($rids[$c]);
		}

	/* reportdefs index 0 will be the wrapper if one is used */
	$pubdate=$reportdefs[0]['report']['date'];
	$paper=$reportdefs[0]['report']['style'];
	$transform=$reportdefs[0]['report']['transform'];

	for($c=0;$c<sizeof($sids);$c++){
		$sid=$sids[$c];
		$Student=(array)fetchStudent_short($sid);
		$Reports=(array)fetchSubjectReports($sid,$reportdefs);
		$Reports['Coversheet']='yes';/* No longer used, always yes */
		$Reports['Paper']=$paper;

		$Reports['CoverTitle']=$reportdefs[0]['report']['title'];
		$Reports['Coversheet']='yes';
		$Reports['Transform']=$transform;
		$Reports['Paper']=$paper;
		$Student['Reports']=$Reports;
		$reportyear=$reportdefs[0]['report']['year']-1;
		$startdate=$reportyear.'-08-15';//Does the whole academic year
		$Student['Reports']['Attendance']=fetchAttendanceSummary($sid,$startdate,$pubdate);
		$Student['Reports']['Merits']['Total']=fetchMeritsTotal($sid,$reportdefs[0]['report']['year']);
		$Student['Reports']['Targets']=fetchTargets($sid);

		/*Finished with the student's reports. Output the result as xml.*/
		$xsl_filename=$transform.'.xsl';
		$html_header='<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/DTD/loose.dtd">
<html>
<head>
<title>ClaSS Report</title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<meta name="author" content="'.$CFG->version.'" />
<base href="http://'.$CFG->siteaddress.$CFG->sitepath.'/reports/" />
<link rel="stylesheet" type="text/css" href="http://'.$CFG->siteaddress.$CFG->sitepath.'/templates/'.$transform.'.css" />
</head>
<body>';
		$html_footer='</body></html>';
		$xml=xmlpreparer('student',$Student);
		$xml='<'.'?xml version="1.0" encoding="utf-8"?'.'><students>'.$xml.'</students>';
		$html_report=xmlprocessor($xml,$xsl_filename);
		if(!empty($html_report)){
			$html=$html_header. $html_report . $html_footer;

			/**
			 * Write the html to as a file in the reports folder.
			 */
			$filename='Report'.$pubdate.'_'.$sid.'_'.$wrapper_rid;
			$file=fopen($CFG->eportfolio_dataroot.'/cache/reports/'.$filename.'.html', 'w');
			if(!$file){
				$error[]='Unable to open file for writing!';
				}
			else{
				fputs($file,$html);
				fclose($file);
				/* Log to the event table for publication. */
				if(mysql_query("INSERT INTO report_event SET report_id='$wrapper_rid', 
							student_id='$sid',date='$pubdate',success='0';")){}
				else{mysql_query("UPDATE report_event SET success='0' 
					WHERE report_id='$wrapper_rid' AND student_id='$sid';");}
				}
			}
		else{
			trigger_error('Report publication failed for: '.$sid. ' '.$Student['Surname']['value'],E_USER_WARNING);
			$error[]='Report publication failed for: '.$sid.' '.$Student['Surname']['value'];
			}
		}

if(!isset($error)){
	$result[]=get_string('scheduledforpublication',$book);
	}

include('scripts/results.php');
include('scripts/redirect.php');
?>