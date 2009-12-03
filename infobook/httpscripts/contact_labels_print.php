<?php
/**						httpscripts/contact_labels_print.php
 */

require_once('../../scripts/http_head_options.php');
$book='infobook';

if(isset($_POST['recipients'])){$recipients=(array)$_POST['recipients'];}else{$recipients=array();}
if(isset($_SESSION[$book.'recipients'])){$recipients=$_SESSION[$book.'recipients'];}
else{$recipients=array();}


if($recipients and sizeof($recipients)>0){
	$Students['recipients']=$recipients;
	$Students['transform']='address_labels';
	$Students['paper']='portrait';
	$returnXML=$Students;
	$rootName='Students';
	}
else{
	$result[]=get_string('youneedtoselectstudents');
	$returnXML=$result;
	$rootName='Error';
	}

require_once('../../scripts/http_end_options.php');
exit;
?>