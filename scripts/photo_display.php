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
require_once('../lib/ldap.php');

if(isset($_GET['epfu'])){$epfu=clean_text($_GET['epfu']);}else{$epfu='';}
if(isset($_GET['enrolno'])){$enrolno=clean_text($_GET['enrolno']);}else{$enrolno='';}
if(isset($_GET['type'])){$type=clean_text($_GET['type']);}else{$type='';}

$mimetype='image/jpeg';
if($type=='staff'){
	$photo_path=get_user_photo($epfu);
	}
else{
	$photo_path=get_student_photo($epfu,$enrolno);
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
	header('Expires: '. gmdate('D, d M Y H:i:s', time() + $maxage) .' GMT');
	header('Cache-Control: max-age=' . $maxage);

	readfile($photo_path);
	}

exit;
?>
