#! /usr/bin/php -q
<?php
/**
 *			   					httpscripts/eportfolio_reports_publish.php
 *
 */

$book='reportbook';
$current='eportfolio_reports_publish.php';

/* The path is passed as a command line argument. */
function arguments($argv) {
    $ARGS = array();
    foreach ($argv as $arg) {
		if (ereg('--([^=]+)=(.*)',$arg,$reg)) {
			$ARGS[$reg[1]] = $reg[2];
			} 
		elseif(ereg('-([a-zA-Z0-9])',$arg,$reg)) {
            $ARGS[$reg[1]] = 'true';
			}
		}
	return $ARGS;
	}
$ARGS=arguments($_SERVER['argv']);
require_once($ARGS['path'].'/school.php');
require_once($CFG->installpath.'/'.$CFG->applicationdirectory.'/scripts/cron_head_options.php');
/**/

if(isset($CFG->eportfolio_db) and $CFG->eportfolio_db!=''){
	require_once($fullpath.'/lib/eportfolio_functions.php');
	}
else{trigger_error('eportfolio not configured!',E_USER_ERROR);}
if(isset($CFG->html2psscript) and $CFG->html2psscript!=''){
	}
else{trigger_error('html2ps not configured!',E_USER_ERROR);}

/**
 * To ensure we don't get a race condition the report_event is touched
 * to update the timestamp and each cron will limit the reports it
 * processes by the age of the event and only process a batch of ten at a time.
 *
 * The age limit also prevents the queue beng swamped will retries of any failures.
 *
 */
	$agelimit=10;//in minutes
	$d_e=mysql_query("SELECT report_id, student_id FROM report_event 
					WHERE success='0' AND time + interval $agelimit minute < now() LIMIT 10;");
	$d_u=mysql_query("UPDATE report_event  SET success='0' 
					WHERE success='0' AND time + interval $agelimit minute < now() LIMIT 10;");
	while($ridsid=mysql_fetch_array($d_e,MYSQL_ASSOC)){
		$success=true;
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
		$publishdata=array();
		$publish_batch=array();
		$publishdata['foldertype']='report';
		$publishdata['description']='report';
		$publishdata['title']=$reportdef['report']['title'].' - '.$pubdate;		
		/* Format specific to html2ps and NOT for html2fpdf. */
		$postdata['batch[0]']=$filename.'.html';
		$postdata['url']=$CFG->eportfolio_dataroot.'/cache/reports/';
		$postdata['process_mode']='batch';
		$postdata['topmargin']='10';
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


/* Alternative is using html2fpdf....
   require_once('lib/html2fpdf/html2fpdf.php');
   while(list($index,$batchfile)=each($batchfiles)){
   $htmlfile=$CFG->eportfolio_dataroot.'/cache/reports/'.$batchfile.'.html';
   $pdffile=$CFG->eportfolio_dataroot.'/cache/reports/'.$batchfile.'.pdf';
   $pdf=new HTML2FPDF();
   $pdf->AddPage();
   $fp = fopen($htmlfile,'r');
   $strContent = fread($fp, filesize($htmlfile));
   fclose($fp);
   $pdf->WriteHTML($strContent);
   $pdf->Output($pdffile);
   }
*/


		if($success){
			$S=fetchStudent_singlefield($sid,'EPFUsername');
			$publish_batch[]=array('epfusername'=>$S['EPFUsername']['value'],'filename'=>$filename.'.pdf');
			$publishdata['batchfiles']=$publish_batch;
			if(elgg_upload_files($publishdata,true)){
				//if(true){
				/* Mark the event table as succesful. */
				mysql_query("UPDATE report_event SET success='1', time=NOW()
						WHERE report_id='$wrapper_rid' AND student_id='$sid';");
				}
			else{
				$success=false;
				}
			}

		if(!$success){
			mysql_query("UPDATE report_event SET success='0', time=NOW() 
						WHERE report_id='$wrapper_rid' AND student_id='$sid';");
			trigger_error('PDF report publication failed for: '.$filename,E_USER_WARNING);
			}
		else{
			unlink($CFG->eportfolio_dataroot.'/cache/reports/'.$filename.'.html');
			}

		}

require_once($CFG->installpath.'/'.$CFG->applicationdirectory.'/scripts/cron_end_options.php');
?>