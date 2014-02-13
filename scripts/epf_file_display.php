<?php
/**                    httpscripts/file_display_classic.php
 */

require_once('../../dbh_connect.php');
require_once('../../school.php');
require_once('../classdata.php');
include('../lib/functions.php');
require_once('../lib/ldap.php');

if(isset($_GET['fileid'])){$fileid=clean_text($_GET['fileid']);}else{$fileid=-1;}
if(isset($_GET['location'])){$location=clean_text($_GET['location']);}else{$location='';}
if(isset($_GET['filename'])){$filename=clean_text($_GET['filename']);}

$mimetype='image/jpeg';
if($filename!=''){
	list($name,$extension)=explode('.',$filename);
	$extension=strtolower($extension);
	if($extension=="png"){$mimetype='image/png';}
	}

$filepath=$CFG->eportfolio_dataroot. '/'.$location;

if($filename!='' and $extension and $_SERVER['HTTP_HOST']==$CFG->eportfoliosite){
	header('Content-type: ' . $mimetype);

	readfile($filepath);
	}

exit;
?>
