<?php
/**
 *			   					httpscripts/sids_photo_print.php
 *
 */

require_once('../../scripts/http_head_options.php');

//if(!isset($xmlid)){print 'Failed'; exit;}

if(isset($_GET['cid'])){$cid=$_GET['cid'];}else{$cid=-1;}
if(isset($_POST['cid'])){$cid=$_POST['cid'];}
if(isset($_GET['comid'])){$comid=$_GET['comid'];}else{$comid=-1;}
if(isset($_POST['comid'])){$comid=$_POST['comid'];}
if(isset($_GET['sids'])){$sids=(array)$_GET['sids'];}else{$sids=-1;}
if(isset($_POST['sids'])){$sids=(array)$_POST['sids'];}

$rootName='';
if($cid!=-1){
	$students=(array)listin_class($cid,true);
	}
elseif($comid!=-1){
	$community['id']=$comid;
	$students=(array)listin_community($community);
	}
elseif($sids!=-1){
	$students=array();
	foreach($sids as $sid){
		$students['id']=$sid;
		}
	}
else{
	$result[]=get_string('youneedtoselectstudents');
	$returnXML=$result;
	$rootName='Error';
	}


if($rootName=='' or $rootName!='Error'){

	if(isset($_SERVER['HTTPS'])){
		$http='https';
		}
	else{
		$http='http';
		}

	$Students=array();
	$Students['Student']=array();

	foreach($students as $student){

		$Student=fetchStudent_short($student['id']);

		$Student['Photo']['url']='scripts/photo_display.php?sid='.$Student['id_db'].'&size=midi';

		$Students['Student'][]=$Student;

		}

	$Students['Date']=date('Y-m-d');
	$Students['Paper']='landscape';
	$Students['Transform']='class_photo_print';
	$returnXML=$Students;
	$rootName='Students';
	}

require_once('../../scripts/http_end_options.php');
exit;
?>
