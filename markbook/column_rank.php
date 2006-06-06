<?php 
/** 									column_rank.php
 */

$action="class_view.php";

if(!isset($_POST{'checkmid'})){
	$_SESSION{'umnrank'}='surname';
	$result[]="No mark selected. Ranked by surname as default.";
	}	
else {
	$checkmid=$_POST{'checkmid'};
	if(sizeof($checkmid)>1){
		$result[]="Choose only one column to rank!";
		}
	else{
		$_SESSION{'umnrank'}=$checkmid[0];
		}
	}
	include("scripts/results.php");
	include("scripts/redirect.php");
?>
