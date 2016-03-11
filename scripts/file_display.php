<?php
/**                    httpscripts/file_display.php
 */

require_once('../../dbh_connect.php');
require_once('../../school.php');
require_once('../classdata.php');
require_once('../logbook/session.php');
$db=db_connect();
mysql_query("SET NAMES 'utf8'");
start_class_phpsession();
require_once('../logbook/authenticate.php');
if(!isset($_SESSION['uid'])){session_defaults();} 
$user=new user($db);
if($_SESSION['uid']==0){exit;}
include('../lib/functions.php');
require_once('../lib/eportfolio_functions.php');

if(isset($_GET['fileid'])){$fileid=clean_text($_GET['fileid']);}else{$fileid=-1;}
if(isset($_GET['location'])){$location=clean_text($_GET['location']);}else{$location='';}
if(isset($_GET['filename'])){$filename=clean_text($_GET['filename']);}

if($filename!=''){
	list($name,$extension)=explode('.',$filename);
	$extension=strtolower($extension);
	}

if($location!='' ){
	$maxage = 1200; // 20 minutes
	$filepath=$CFG->eportfolio_dataroot. '/'.$location;

	/* Convert html to a pdf if we are configured for that. */
	if(file_exists($filepath) and $extension=='html' and isset($CFG->wkhtml2pdf) and $CFG->wkhtml2pdf!=''){
		display_pdf($filepath,'Report.pdf');
		}
	elseif(file_exists($filepath)){
		/*resize image if the width or height are higher than the default ones*/
		if($extension=='jpg' or $extension=='jpeg' or $extension=='png' or $extension=='gif'){
			$resize=resize_image($filepath);
			}
		switch ($extension) {
			case "html": $ctype="text/html"; break;
			case "pdf": $ctype="application/pdf"; break;
			case "exe": $ctype="application/octet-stream"; break;
			case "zip": $ctype="application/zip"; break;
			case "doc": $ctype="application/msword"; break;
			case "xls": $ctype="application/vnd.ms-excel"; break;
			case "ppt": $ctype="application/vnd.ms-powerpoint"; break;
			case "gif": $ctype="image/gif"; break;
			case "png": $ctype="image/png"; break;
			case "jpeg":
			case "jpg": $ctype="image/jpg"; break;
			}

		header('Content-type: ' . $ctype);
		header('Expires: '. gmdate('D, d M Y H:i:s', time() + $maxage) .' GMT');
		header('Cache-Control: max-age=' . $maxage);
		header('Content-Disposition: attachment; filename="'.$filename.'"');
		readfile($filepath);
		exit;
		}
	else{
		header("HTTP/1.0 404 Not Found");
		}
	}
exit;

/**
 *
 */
function display_pdf($filepath,$filename){
 
	$html_file=fopen($filepath,'r');
	$html=fread($html_file,filesize($filepath));
	fclose($html_file);

    $descriptorspec=array(
						  0 => array('pipe', 'r'), // stdin
						  1 => array('pipe', 'w'), // stdout
						  2 => array('pipe', 'w'), // stderr
						  );
    $process=proc_open('wkhtmltopdf -q - -',$descriptorspec,$pipes);
 
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
        trigger_error('PDF GENERATOR ERROR:<br />' . nl2br(htmlspecialchars($errors)),E_USER_WARNING);
		} 
	else{
        header('Content-Type: application/pdf');
        header('Cache-Control: public, must-revalidate, max-age=0'); // HTTP/1.1
        header('Pragma: public');
        header('Expires: Sat, 26 Jul 1997 05:00:00 GMT'); // Date in the past
        header('Last-Modified: ' . gmdate('D, d M Y H:i:s').' GMT');
        header('Content-Length: ' . strlen($pdf));
        header('Content-Disposition: inline; filename="' . $filename . '";');
        echo $pdf;
		}
	}

?>
