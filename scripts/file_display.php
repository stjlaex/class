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
require_once('../lib/ldap.php');

if(isset($_GET['epfu'])){$epfu=clean_text($_GET['epfu']);}else{$epfu='';}
if(isset($_GET['location'])){$location=clean_text($_GET['location']);}else{$location='';}
if(isset($_GET['filename'])){$filename=clean_text($_GET['filename']);}

if($filename!=''){
	list($name,$extension)=explode($filename,'.');
	}

if($location!='' ){
	$maxage = 1200; // 20 minutes
	$filepath=$CFG->eportfolio_dataroot. '/'.$location;
	if(file_exists($filepath)){
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
?>
