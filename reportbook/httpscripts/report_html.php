<?php
		$rids=array();
		$reportdefs=array();
		$rids[]=$wrapper_rid;
		$reportdefs[]=fetch_reportdefinition($wrapper_rid);
		$d_rid=mysql_query("SELECT categorydef_id AS report_id FROM ridcatid WHERE
				 report_id='$wrapper_rid' AND subject_id='wrapper' ORDER BY categorydef_id;");
		while($r=mysql_fetch_array($d_rid,MYSQL_ASSOC)){
			$rids[]=$r['report_id'];
			$reportdefs[]=fetch_reportdefinition($r['report_id']);
			}

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
		$imagepath='http://'.$CFG->siteaddress.$CFG->sitepath.'/images/';
/*
 * <link rel="stylesheet" type="text/css" href="http://'.$CFG->siteaddress.$CFG->sitepath.'/templates/'.$transform.'.css" />
*/
		$html_css=file_get_contents($CFG->installpath.'/templates/'.$transform.'.css');
		$html_header='<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/DTD/loose.dtd">
<html>
<head>
<title>ClaSS Report</title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<meta name="author" content="'.$CFG->version.'" />
<style type="text/css">'.
$html_css.
'</style>
</head>
<body>';
		$html_footer='</body></html>';
		$xml=xmlpreparer('student',$Student);
		$xml='<'.'?xml version="1.0" encoding="utf-8"?'.'><students>'.$xml.'</students>';
		$html_report=xmlprocessor($xml,$xsl_filename);
		$html_report=eregi_replace('../images/',$imagepath,$html_report);
		if(!empty($html_report)){
			$html_file=$html_header. $html_report . $html_footer;
			/**
			 * Write the html to a file in the reports folder.
			 */
			$filename='Report'.$pubdate.'_'.$sid.'_'.$wrapper_rid;
			$file=fopen($CFG->eportfolio_dataroot.'/cache/reports/'.$filename.'.html', 'w');
			if(!$file){
				$error[]='Unable to open file for writing!';
				}
			else{
				fputs($file,$html_file);
				fclose($file);
				}
			}
		else{
			$success=false;
			trigger_error('Report publication failed for: '.$sid. ' '.$Student['Surname']['value'],E_USER_WARNING);
			$error[]='Report publication failed for: '.$sid.' '.$Student['Surname']['value'];
			}
?>