<?php
/**
 *			   					httpscripts/class_photo_print.php
 *
 */

require_once('../../scripts/http_head_options.php');

//if(!isset($xmlid)){print 'Failed'; exit;}

if(isset($_GET['cid'])){$cid=$_GET['cid'];}else{$cid=-1;}
if(isset($_POST['cid'])){$cid=$_POST['cid'];}

if($cid==-1){
	$result[]=get_string('youneedtoselectstudents');
	$returnXML=$result;
	$rootName='Error';
	}
else{

	if(isset($_SERVER['HTTPS'])){
		$http='https';
		}
	else{
		$http='http';
		}

	$students=(array)listin_class($cid,true);
	$Students=array();	
	$Students['Student']=array();

	foreach($students as $student){

		$Student=fetchStudent_short($student['id']);

		$Student['Photo']['url']='scripts/photo_display.php?sid='.$Student['id_db'].'&size=midi';

		$Students['Student'][]=$Student;

		}

	$Students['Date']=date('Y-m-d');
	$Students['Paper']='landscape';
	$Students['Transform']='';
	$returnXML=$Students;
	$rootName='Students';
	}

require_once('../../scripts/http_end_options.php');
exit;
?>
