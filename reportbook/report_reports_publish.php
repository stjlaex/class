<?php
/**									report_reports_publish.php
 * publishes the selected reports to html files
 * and to pdf if the html2ps package is available through $CFG -> html2psscript
 */

$action='report_reports.php';

if(isset($_POST['sids'])){$sids=(array) $_POST['sids'];}else{$sids=array();}
if(isset($_POST['rids'])){$rids=(array) $_POST['rids'];}else{$rids=array();}
if(isset($_POST['yid'])){$yid=$_POST['yid'];}else{$yid='';}
if(isset($_POST['fid'])){$fid=$_POST['fid'];}else{$fid='';}

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

   	$result[]=get_string('publishedtofile');

	/*doing one student at a time*/
	$postdata=array();
	for($c=0;$c<sizeof($sids);$c++){
		$sid=$sids[$c];
		$Student=fetchStudent_short($sid);

		list($Reports,$transform)=fetchSubjectReports($sid,$reportdefs);
		$Reports['Coversheet']='yes';/*no longer used, always yes*/
		$Student['Reports']=nullCorrect($Reports);

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
		$xml='<?xml version="1.0" encoding="utf-8"?'.'><students>'.$xml.'</students>';
		$html_report=xmlprocessor($xml,$xsl_filename);
		$html=$html_header. $html_report . $html_footer;

		$filename='Report_'.$Student['Surname']['value'].'_'.$sid.'_'.$wrapper_rid.'.html';
		$postdata['batch['.$c.']']=$filename;

		$file=fopen($CFG->installpath.'/reports/'.$filename, 'w');
		if(!$file){
			$error[]='Unable to open file for writing!';
			}
		else{
			fputs($file, $html);
			fclose($file);
			}
		}

	/*call the conversion for pdf*/
	if(isset($CFG->html2psscript) and $CFG->html2psscript!=''){
		$postdata['url']='http://'.$CFG->siteaddress.$CFG->sitepath.'/reports/';
		$postdata['process_mode']='batch';
		$curl=curl_init();
		curl_setopt($curl, CURLOPT_URL,$CFG->html2psscript);
		curl_setopt($curl, CURLOPT_POST, 1);
		curl_setopt($curl, CURLOPT_POSTFIELDS, $postdata);
		curl_exec($curl);
		curl_close($curl);
		}

	include('scripts/results.php');
	include('scripts/redirect.php');
?>
