<?php 
/** 									column_rank.php
 */

$action='class_view.php';

if(!isset($_POST['checkmid'])){
	$_SESSION['umnrank']='surname';
	$result[]='No mark selected. Ranked by surname as default.';
	}	
else{
	$checkmids=(array)$_POST['checkmid'];
	if(sizeof($checkmids)>1){
		$result[]='Choose only one column to rank!';
		}
	else{
		$_SESSION['umnrank']=$checkmids[0];
		}
	}
	include('scripts/results.php');
	include('scripts/redirect.php');
?>
