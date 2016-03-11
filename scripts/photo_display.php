<?php
/**                    httpscripts/photo_display.php
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
include('../lib/fetch_student.php');
require_once('../lib/eportfolio_functions.php');

if(isset($_GET['epfu'])){$epfu=clean_text($_GET['epfu']);}else{$epfu='';}
if(isset($_GET['enrolno'])){$enrolno=clean_text($_GET['enrolno']);}else{$enrolno='';}
if(isset($_GET['sid'])){$sid=clean_text($_GET['sid']);}else{$sid='';$type='student';}
if(isset($_GET['type'])){$type=clean_text($_GET['type']);}else{$type='';}
if(isset($_GET['size'])){$size=clean_text($_GET['size']);}else{$size='';}

$mimetype='image/jpeg';
if($type=='staff'){
	$photo_path=get_photo($epfu,-1,$size);
	}
else{
	if(isset($sid) and $sid!=''){
		$field=fetchStudent_singlefield($sid,'EPFUsername');
		$epfu=$field['EPFUsername']['value'];
		}
	$photo_path=get_photo($epfu,$enrolno,$size);
	}

if($photo_path!='' and $mimetype){
	header('Content-type: ' . $mimetype);
	/* TODO: implement some sensible caching? Currently type is not used. */
	if($type=='profileiconbyid'){
		$maxage = 604800; // 1 week
		}
	else{
		$maxage = 86400; //  1 day
		}

	if (strpos($_SERVER['HTTP_USER_AGENT'], 'Trident/7.0; rv:11.0') !== false) {
		header('Cache-Control: no-cache, no-store, must-revalidate');
		}
	else{
		$last_modified=filemtime($photo_path);
		header('Cache-Control: max-age=' . $maxage);
		header('Expires: '. gmdate('D, d M Y H:i:s', time() + $maxage) .' GMT');
		header('Last-Modified: ' . gmdate('D, d M Y H:i:s', $last_modified) . ' GMT');
		}

	readfile($photo_path);
	}

exit;
?>
