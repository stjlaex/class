<?php
/*									comments_list_action.php

*/

$action='comments_list.php';

$id=$_POST['id_db'];
$detail=$_POST['detail'];
$entrydate = $_POST['entrydate'];
if(isset($_POST['bid'])){$bid=$_POST['bid'];}else{$bid='%';}
if(isset($_POST['catid'])){$catid=$_POST['catid'];}else{$catid=array();}
if(isset($_POST['ratvalue'])){$ratvalue=$_POST['ratvalue'];}else{$ratvalue='N';}
if(isset($_POST['newyid'])){$newyid=$_POST['newyid'];}else{$newyid='';}


/********Check user has permission to edit************
$yid=$Student['NCyearActual']['id_db'];
$perm=getYearPerm($yid, $respons);
if($perm['w']!=1){
	print '<h5 class='warn'>You don't have the permissions to edit this page!</h5>'; exit;
	}
*/

include('scripts/sub_action.php');

	//	$ncyear=fetchNCYear($sid);
	//this currently allows comments for any year not just the current one
	$d_ncyear=mysql_query("SELECT ncyear FROM yeargroup WHERE id='$newyid'");
	$ncyear=mysql_result($d_ncyear,0);

	if($bid==''){$bid='%';}
	$category='';
	for($c=0;$c<sizeof($catid);$c++){
	    $category=$category.$catid[$c].':'.$ratvalue.';';
		}


	if($id!=''){
		if(mysql_query("UPDATE comments SET student_id='$sid',
		detail='$detail', entrydate='$entrydate', ncyear='$ncyear',
		subject_id='$bid', category='$category', teacher_id='$tid'
		WHERE id='$id'")){
		$result[]='Comment recorded.';
		}
		else{$error[]=mysql_error();}
		}
	else{
		if(mysql_query("INSERT INTO comments SET student_id='$sid',
		detail='$detail', entrydate='$entrydate', ncyear='$ncyear',
		subject_id='$bid', category='$category', teacher_id='$tid'")){
		$result[]='Comment recorded.';
		}
		else{$error[]=mysql_error();}
		}

$_SESSION{'Student'}=fetchStudent($sid);
include('scripts/results.php');
include('scripts/redirect.php');	
?>
