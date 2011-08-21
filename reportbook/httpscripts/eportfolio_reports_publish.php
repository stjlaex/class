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
    foreach($argv as $arg){
		if(ereg('--([^=]+)=(.*)',$arg,$reg)){
			$ARGS[$reg[1]] = $reg[2];
			} 
		elseif(ereg('-([a-zA-Z0-9])',$arg,$reg)){
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
	$doingepf=false;
	}
else{
	$doingepf=false;
	trigger_error('eportfolio not configured!',E_USER_ERROR);
	}
if(isset($CFG->html2pdf) and $CFG->html2pdf!=''){
	require_once($CFG->html2pdf.'/html2pdf.class.php');
	$pubtype='pdf';
	$pubmethod='html2pdf';
	}
elseif(isset($CFG->html2ps) and $CFG->html2ps!=''){
	require_once($CFG->html2ps.'/samples/class_file.php');
	$pubtype='pdf';
	$pubmethod='html2ps';
	}
else{
	trigger_error('html2pdf is not configured!',E_USER_ERROR);
	$pubtype='html';
	}


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
					WHERE success='0' AND time + interval $agelimit minute < now()  AND try < 4 LIMIT 10;");
	$d_u=mysql_query("UPDATE report_event  SET success='0' 
					WHERE success='0' AND time + interval $agelimit minute < now() AND try < 4 LIMIT 10;");

	while($ridsid=mysql_fetch_array($d_e,MYSQL_ASSOC)){
		$success=true;
		$wrapper_rid=$ridsid['report_id'];
		$sid=$ridsid['student_id'];
		$reportdef=(array)fetch_reportdefinition($wrapper_rid);
		$pubdate=$reportdef['report']['date'];
		$paper=$reportdef['report']['style'];
		$transform=$reportdef['report']['transform'];
		$filename='Report'.$pubdate.'_'.$sid.'_'.$wrapper_rid;
		
		/* An array publishdata is used for the eportfolio, it incorporates
		 * an array called batch listing all of the filenames to be uploaded. 
		 */
		$postdata=array();
		$publishdata=array();
		$publish_batch=array();
		$publishdata['foldertype']='report';
		$publishdata['description']='report';
		$publishdata['title']=$reportdef['report']['title'].' - '.$pubdate;
		
		include('report_html.php');

		if($success and $pubtype=='pdf'){
			if($pubmethod=='html2pdf'){
				try{
					$margins=array(5,10,5,10);
					$html2pdf = new HTML2PDF('P', 'A4', 'en', true, 'UTF-8', $margins);
					$html2pdf->pdf->SetDisplayMode('real','SinglePage','UseNone');
					$html2pdf->writeHTML('<style type="text/css">'.$html_css. '</style>'.$html_report);
					$html2pdf->Output($CFG->eportfolio_dataroot.'/cache/reports/'.$filename.'.pdf', 'F');
					}
				catch(HTML2PDF_exception $e){
					trigger_error($e,E_USER_WARNING);
					$success=false;
					}
				}
			elseif($pubmethod=='html2ps'){
				/* Format specific to html2ps
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
				*/
				//convert_to_pdf($CFG->eportfolio_dataroot.'/cache/reports/'.$filename.'.html',$CFG->eportfolio_dataroot.'/cache/reports/'.$filename.'.pdf');
				//trigger_error('TEST',E_USER_WARNING);
				}
			unset($html_file);
			}

		$S=fetchStudent_singlefield($sid,'EPFUsername');
		$epfusername=$S['EPFUsername']['value'];

		if($success and $doingepf){
			$publish_batch[]=array('epfusername'=>$epfusername,'filename'=>$filename.'.'.$pubtype);
			$publishdata['batchfiles']=$publish_batch;
			if(elgg_upload_files($publishdata,true)){
				}
			else{
				$success=false;
				}
			}
		elseif($success){
			$dir='files/' . substr($epfusername,0,1) . '/' . $epfusername; 
			//rename($CFG->eportfolio_dataroot.'/cache/reports/'.$filename.'.'.$pubtype,$CFG->eportfolio_dataroot.'/'.$dir.'/'.$filename.'.'.$pubtype);
			}

		if($success){
			/* Mark the event table as succesful. */
			mysql_query("UPDATE report_event SET success='1', time=NOW(), try=try+1
						WHERE report_id='$wrapper_rid' AND student_id='$sid';");
			trigger_error('Report publishsed for: '.$filename,E_USER_WARNING);
			//unlink($CFG->eportfolio_dataroot.'/cache/reports/'.$filename.'.html');
			}
		else{
			mysql_query("UPDATE report_event SET success='0', time=NOW(), try=try+1 
						WHERE report_id='$wrapper_rid' AND student_id='$sid';");
			trigger_error('Report publication failed for: '.$filename,E_USER_WARNING);
			}

		}

require_once($CFG->installpath.'/'.$CFG->applicationdirectory.'/scripts/cron_end_options.php');
?>