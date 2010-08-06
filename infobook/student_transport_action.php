<?php
/**								student_transport_action.php
 *
 */

$action='student_view.php';
include('scripts/sub_action.php');

if($sub=='Submit'){
	/*Check user has permission to edit*/
	$yid=$Student['YearGroup']['value'];
	$perm=getYearPerm($yid, $respons);
	include('scripts/perm_action.php');

	if(isset($_POST['enrolyid'])){$enrolyid=$_POST['enrolyid'];}else{$enrolyid='';}

	$Enrolment=fetchEnrolment($sid);
	}

	include('scripts/redirect.php');
?>
