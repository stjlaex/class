<?php
/**
 *			   					httpscripts/eportfolio_reports_publish.php
 *
 */
$book='reportbook';

if(isset($CFG->eportfolio_db) and $CFG->eportfolio_db!=''){
	require_once($fullpath.'/lib/eportfolio_functions.php');
	}
else{$error[]='eportfolio not configured!';$repfail=true;}
if(isset($CFG->html2psscript) and $CFG->html2psscript!=''){
	}
else{$error[]='html2ps not configured!';$repfail=true;}

$d_e=mysql_query("SELECT report_id, student_id FROM report_event 
					WHERE success='0' AND time + interval 10 minute < now() LIMIT 5;");
$d_u=mysql_query("UPDATE report_event  SET success='0' 
					WHERE success='0' AND time + interval 10 minute < now() LIMIT 5;");
while($ridsid=mysql_fetch_array($d_e,MYSQL_ASSOC)){
	$wrapper_rid=$ridsid['report_id'];
	$sid=$ridsid['student_id'];
	$reportdef=(array)fetch_reportdefinition($wrapper_rid);
	$pubdate=$reportdef['report']['date'];
	$paper=$reportdef['report']['style'];
	$transform=$reportdef['report']['transform'];

	$filename='Report'.$pubdate.'_'.$sid.'_'.$wrapper_rid;

	/* Two arrays, one postdata is used by html2ps and inlcudes config
	 * options, a second publishdata is used for the eportfolio, both incorporate
	 * an array called batch listing all of the filenames to be processed. 
	 */
	$postdata=array();
	//$batchfiles=array();
	$publishdata=array();
	$publish_batch=array();
	$publishdata['foldertype']='report';
	$publishdata['description']='report';
	$publishdata['title']=$reportdef['report']['title'].' - '.$pubdate;

	//$batchfiles[]=$filename;//html2fpdf
   	$postdata['batch[0]']=$filename.'.html';//html2ps

	if(!isset($repfail)){
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
		curl_setopt($curl,CURLOPT_POST,1);
		curl_setopt($curl,CURLOPT_POSTFIELDS,$postdata);
		curl_exec($curl);
		curl_close($curl);
		trigger_error('PUB: '.$filename,E_USER_WARNING);
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

	if(!isset($repfail)){
		$epfusername=get_epfusername($sid);
		$publish_batch[]=array('epfusername'=>$epfusername,'filename'=>$filename.'.pdf');
		$publishdata['batchfiles']=$publish_batch;
		//elgg_upload_files($publishdata);
		/* Mark the event table as succesful. */
		mysql_query("UPDATE report_event SET success='1' 
						WHERE report_id='$wrapper_rid' AND student_id='$sid';");
		}

	}

?>