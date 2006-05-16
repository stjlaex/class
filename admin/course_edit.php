<?php 
/* 									course_edit.php
*/

$host="admin.php";
$current="curriculum_matrix.php";
$action="";
$choice="curriculum_matrix.php";

$exists=$_GET{'exists'};
$crid=$_GET{'crid'};
$bid=$_GET{'bid'};

include("scripts/redirect.php");
	
	
	if($exists=='Y'){
		mysql_query("DELETE FROM cridbid WHERE course_id='$crid' AND subject_id='$bid' LIMIT 1");
		}

	if($exists=='N'){
		mysql_query("INSERT INTO cridbid SET course_id='$crid', subject_id='$bid'");
		}

?>

















































