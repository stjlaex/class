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

$mimetype='application/pdf';

if($location!='' ){
		$filepath=$CFG->eportfolio_dataroot. '/'.$location;
		if(file_exists($filepath)){
			header('Content-type: ' . $mimetype);
			$maxage = 1200; // 20 minutes
			header('Expires: '. gmdate('D, d M Y H:i:s', time() + $maxage) .' GMT');
			header('Cache-Control: max-age=' . $maxage);
			header('Content-Disposition: attachment; filename="report.pdf"');
			readfile($filepath);
			}
		else{
			print 'File not found.';
			}
	}

exit;
?>
