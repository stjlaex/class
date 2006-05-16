<?php 
/* 									define_mark_action2.php
*/

$host="markbook.php";
$current="new_mark.php";
$action="";
$choice="class_view.php";

$name=$_POST['name'];
$type=$_POST['type'];
if(isset($_POST['tier'])){$tier=$_POST['tier'];}else{$tier='';}
if(isset($_POST['gena'])){$gena=$_POST['gena'];}else{$gena='';}
if(isset($_POST['total'])){$total=$_POST['total'];}else{$total='';}
$comment=$_POST['comment'];
$crid=$_POST['crid'];
$bid=$_POST['bid'];
if(isset($_POST['resultq'])){$resultq=$_POST['resultq'];}
if(isset($_POST['method'])){$method=$_POST['method'];}
$sub=$_POST{'sub'};

	if($sub=='Cancel'){
		$current="class_view.php";
		$result[]="Action cancelled.";
		include("scripts/results.php");
		include("scripts/redirect.php");
		exit;
		}

	if(mysql_query("INSERT INTO markdef SET
	     name='$name', scoretype='$type', tier='$tier', grading_name='$gena',
	     comment='$comment', outoftotal='$total', author='$tid', 
		course_id='$crid', subject_id='$bid'")){
				$result[]="Mark definition inserted.";
				}
	else{
				$result[]="Failed on mark definition insert!";	
		  		$error[]=mysql_error();
				}

	if(mysql_query("INSERT INTO method SET markdef_name='$name', method='$method', 
			resultqualifier='$resultq', course_id='$crid'")){
				$result[]="Linked to an assessment method.";
				}
	else{
				$result[]="Failed on method insert!";	
		  		$error[]=mysql_error();
				}

	include("scripts/results.php");
	include("scripts/redirect.php");
?>



















































