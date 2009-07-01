<?php
/**									report_reports_publish.php
 *
 * Publishes the selected reports to html files
 * and to pdf if the html2ps package is available through $CFG -> html2psscript
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
	//trigger_error('wrapper:'.$wrapper_rid,E_USER_WARNING);
	}

	/* Two arrays, one postdata is used by html2ps and inlcudes config
	 * options, a second publishdata is used for the eportfolio, both incorporate
	 * an array batch of all of the filenames to be processed 
	 */
	$postdata=array();
	$batchfiles=array();
	$publishdata=array();
	$publishdata['foldertype']='report';

	/* Find the definition specific to each report */
	$reportdefs=array();
	for($c=0;$c<sizeof($rids);$c++){ 
		$reportdefs[]=fetch_reportdefinition($rids[$c]);
		}

	$pubdate=$reportdefs[0]['report']['date'];
	$paper=$reportdefs[0]['report']['style'];
	$publishdata['description']='report';
	$publishdata['title']=$reportdefs[0]['report']['title'].' - '.$pubdate;
	$transform=$reportdefs[0]['report']['transform'];

	//trigger_error('wrapper:'.$wrapper_rid.' '.$transform,E_USER_WARNING);

	for($c=0;$c<sizeof($sids);$c++){
		$sid=$sids[$c];
		$Student=fetchStudent_short($sid);
		list($Reports,$t)=fetchSubjectReports($sid,$reportdefs);
		$Reports['Coversheet']='yes';/* No longer used, always yes */
		$Reports['Paper']=$paper;

		/* reportdefs index 0 will be the wrapper if one is used */
		$Reports['CoverTitle']=$reportdefs[0]['report']['title'];
		$Reports['Coversheet']='yes';
		$Reports['Transform']=$transform;
		$Reports['Paper']=$paper;
		$Student['Reports']=nullCorrect($Reports);
		/* TODO: set a start date */
		$Student['Reports']['Attendance']=fetchAttendanceSummary($sid,'2008-09-01',$reportdefs[0]['report']['date']);

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
		$html=$html_header. $html_report . $html_footer;

		$filename='Report'.$pubdate.'_'.$sid.'_'.$wrapper_rid;
		$postdata['batch['.$c.']']=$filename.'.html';//html2ps
		$batchfiles[]=$filename;//html2fpdf
		$epfusername=get_epfusername($sid,$Student);
		$publish_batchfiles[$c]=array('epfusername'=>$epfusername,
									  'filename'=>$filename.'.pdf');//eportfolio

		$file=fopen($CFG->installpath.'/reports/'.$filename.'.html', 'w');
		if(!$file){
			$error[]='Unable to open file for writing!';
			}
		else{
			fputs($file,$html);
			fclose($file);
			}
		}


		if(isset($CFG->html2psscript) and $CFG->html2psscript!='' and !isset($error)){
			$postdata['url']='http://'.$CFG->siteaddress.$CFG->sitepath.'/reports/';
			$postdata['process_mode']='batch';
			$postdata['topmargin']='5';
			$postdata['bottommargin']='0';
			$postdata['leftmargin']='5';
			$postdata['rightmargin']='5';
			$postdata['pixels']='850';
			$postdata['scalepoints']='false';
			if($paper=='landscape'){$postdata['landscape']='true';}
			$curl=curl_init();
			curl_setopt($curl,CURLOPT_URL,$CFG->html2psscript);
			curl_setopt($curl,CURLOPT_POST, 1);
			curl_setopt($curl,CURLOPT_POSTFIELDS, $postdata);
			curl_exec($curl);
			curl_close($curl);
			}

/* Alternative is using html2fpdf....
	require_once('lib/html2fpdf/html2fpdf.php');
	while(list($index,$batchfile)=each($batchfiles)){
		$htmlfile=$CFG->installpath.'/reports/'.$batchfile.'.html';
		$pdffile=$CFG->installpath.'/reports/'.$batchfile.'.pdf';
		$pdf=new HTML2FPDF();
		$pdf->AddPage();
		$fp = fopen($htmlfile,'r');
		$strContent = fread($fp, filesize($htmlfile));
		fclose($fp);
		$pdf->WriteHTML($strContent);
		$pdf->Output($pdffile);
		trigger_error('PDF: '.$pdffile,E_USER_WARNING);
		}

*/
	if(isset($CFG->eportfolio_db) and $CFG->eportfolio_db!='' and !isset($error)){
		require_once('lib/eportfolio_functions.php');
		$publishdata['batchfiles']=$publish_batchfiles;
		//elgg_upload_files($publishdata);
		$result[]=get_string('publishedtofile',$book);
		}


	include('scripts/results.php');
	include('scripts/redirect.php');
?>