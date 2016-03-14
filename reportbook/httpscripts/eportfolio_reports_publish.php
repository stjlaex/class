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
require_once($fullpath.'/lib/eportfolio_functions.php');

/**
 * Pssible methods for genrating PDFs:
 * (0) webkit wkthmltopdf is the winner?
 * (1) CommandLinePrint extension for Firefox
 * (2) the html2pdf scripts from html2pdf.fr
 * (3) the html2ps scripts from tufat.com
 *
 */
if(isset($CFG->wkhtml2pdf) and $CFG->wkhtml2pdf!=''){
	$pubtype='pdf';
	$pubmethod='wkhtml2pdf';
	}
elseif(isset($CFG->html2pdf) and $CFG->html2pdf!=''){
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
	$agelimit=15;//in minutes
	$d_e=mysql_query("SELECT report_id, student_id FROM report_event 
					WHERE success='0' AND time + interval $agelimit minute < now()  AND try < 4 LIMIT 20;");
	$d_u=mysql_query("UPDATE report_event  SET success='0' 
					WHERE success='0' AND time + interval $agelimit minute < now() AND try < 4 LIMIT 20;");

	while($ridsid=mysql_fetch_array($d_e,MYSQL_ASSOC)){
		$success=true;
		$wrapper_rid=$ridsid['report_id'];
		$sid=$ridsid['student_id'];
		$reportdef=(array)fetch_reportdefinition($wrapper_rid);
		$pubdate=$reportdef['report']['date'];
		$paper=$reportdef['report']['style'];
		$transform=$reportdef['report']['transform'];
		$filename='Report'.$pubdate.'_'.$sid.'_'.$wrapper_rid;
		//$filename='Report'.$pubdate.'_'.$sid.'_'.$wrapper_rid.'_'.$Student['Forename']['value'].'_'.$Student['Surname']['value'].'_'.$Student['RegistrationGroup']['value'];
		/* An array publishdata is used for the eportfolio, it incorporates
		 * an array called batch listing all of the filenames to be uploaded. 
		 */
		$postdata=array();
		$publishdata=array();
		$publish_batch=array();
		$publishdata['foldertype']='report';
		$publishdata['description']='report';
		$publishdata['title']=$reportdef['report']['title'].' - '.$pubdate;

		/* Creates the html version of the report and writes to the epf cache directory. */
		include('report_html.php');

		/* Now convert to PDF if thats the chosen method. */
		if($success and $pubtype=='pdf'){
			if($pubmethod=='wkhtml2pdf'){
				if($reportdef['report']['style']=='landscape'){$orientation='Landscape';}
				else{$orientation='Portrait';}
				$success=write_pdf($html_file,$filename,$orientation);
				}
			elseif($pubmethod=='html2pdf'){
				try{
					$margins=array(5,10,5,10);
					$html2pdf = new HTML2PDF('P', 'A4', 'en', true, 'UTF-8', $margins);
					$html2pdf->pdf->SetDisplayMode('real','SinglePage','UseNone');
					/* Wants the html without the header oddly. */
					$html2pdf->writeHTML('<style type="text/css">'.$html_css. '</style>'.$html_report);
					$html2pdf->Output($CFG->eportfolio_dataroot.'/cache/reports/'.$filename.'.pdf', 'F');
					}
				catch(HTML2PDF_exception $e){
					trigger_error($e,E_USER_WARNING);
					$success=false;
					}
				}
			unset($html_file);
			}

		$S=fetchStudent_singlefield($sid,'EPFUsername');
		$epfusername=$S['EPFUsername']['value'];

		/* Move the file into the owners eportfolio direcotry. */
		if($success){
			$targetdir='files/' . substr($epfusername,0,1) . '/' . $epfusername;
			if(!make_portfolio_directory($targetdir)){
				$success=false;
				}
			else{
				$targetpath=$CFG->eportfolio_dataroot.'/'.$targetdir.'/'.$filename.'.'.$pubtype;
				rename($CFG->eportfolio_dataroot.'/cache/reports/'.$filename.'.'.$pubtype, $targetpath);
				}
			}

		if($success){
			/* Mark the event table as succesful. */
			mysql_query("UPDATE report_event SET success='1', time=NOW(), try=try+1
						WHERE report_id='$wrapper_rid' AND student_id='$sid';");
			//trigger_error('Report publishsed for: '.$filename,E_USER_WARNING);
			}
		else{
			mysql_query("UPDATE report_event SET success='0', time=NOW(), try=try+1 
						WHERE report_id='$wrapper_rid' AND student_id='$sid';");
			$d_r=mysql_query("SELECT try FROM report_event WHERE report_id='$wrapper_rid' AND student_id='$sid';");
			if(mysql_result($d_r,0)>3){
				$messagesubject=$CFG->clientid.': Report publication failed for: '.$filename;
				send_email_to('support@'.$CFG->support,'',$messagesubject,$messagesubject,$messagesubject);
				}

			trigger_error($messagesubject,E_USER_WARNING);
			}

		/* Clean up: make sure the cache finishes empty. */
		if(file_exists($CFG->eportfolio_dataroot.'/cache/reports/'.$filename.'.html')){
			unlink($CFG->eportfolio_dataroot.'/cache/reports/'.$filename.'.html');
			}
		if(file_exists($CFG->eportfolio_dataroot.'/cache/reports/'.$filename.'.pdf')){
			unlink($CFG->eportfolio_dataroot.'/cache/reports/'.$filename.'.pdf');
			}

		}



function write_pdf($html,$filename,$orientation){
	global $CFG;

	$descriptorspec=array(
						  0 => array('pipe', 'r'), // stdin
						  1 => array('pipe', 'w'), // stdout
						  2 => array('pipe', 'w'), // stderr
						  );
	//$process=proc_open($CFG->wkhtml2pdf.' -q - -',$descriptorspec,$pipes);
	$process=proc_open($CFG->wkhtml2pdf.' --margin-left 0 --margin-right 0 --margin-bottom 0 --margin-top 4 --dpi 300 --page-size A4 --orientation '.$orientation.' -q - -',$descriptorspec,$pipes);

    // Send the HTML on stdin
    fwrite($pipes[0], $html);
    fclose($pipes[0]);
 
    // Read the outputs
    $pdf=stream_get_contents($pipes[1]);
    $errors=stream_get_contents($pipes[2]);
 
    // Close the process
    fclose($pipes[1]);
    $return_value=proc_close($process);
 
    // Output the results
    if($errors){
        trigger_error('PDF GENERATOR ERROR: ' . nl2br(htmlspecialchars($errors)),E_USER_WARNING);
		$success=false;
		}
	else{
		$file=fopen($CFG->eportfolio_dataroot.'/cache/reports/'.$filename.'.pdf', 'w');
		if(!$file){
			$error[]='Unable to open file for writing!';
			$success=false;
			}
		else{
			fputs($file,$pdf);
			fclose($file);
			$success=true;
			}
		}

	return $success;
	}


require_once($CFG->installpath.'/'.$CFG->applicationdirectory.'/scripts/cron_end_options.php');
?>
