<?php
/*****									student_view_mecial1.php
 *
 */

$action='student_view_medical.php';
include('scripts/sub_action.php');

if($sub=='MedicalStatus'){
/********Check user has permission to edit*************/
	$yid=$Student['NCyearActual']['id_db'];
	$perm=getYearPerm($yid, $respons);
	if($perm['w']!=1){
		$error[]='You don\'t have the permissions to edit this page!'; 
		include('scripts/results.php');
		include('scripts/redirect.php');
		exit;
		}

	if($Student['MedicalFlag']['value']=='Y'){
		mysql_query("UPDATE info SET medical='N' WHERE student_id='$sid'");
		$result[]='Updated student Medical status.';
		}

	else if($Student['MedicalFlag']['value']=='N'){
		mysql_query("UPDATE info SET medical='Y' WHERE student_id='$sid'");
		$result[]='Updated student Medical status.';

		/*	Set up first blank record for the profile*/
		$todate = date('Y').'-'.date('n').'-'.date('j');
		}
	$_SESSION{'infoStudent'}=fetchStudent($_SESSION{'sid'});
	}



	include('scripts/results.php');
	include('scripts/redirect.php');
?>

