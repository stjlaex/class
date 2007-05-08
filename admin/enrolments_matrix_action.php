<?php 
/**				   	   				   enrolments_matrix_action.php
 */

$action='enrolments_matrix.php';
$action_post_vars=array('enrolyear');

include('scripts/sub_action.php');

if($sub=='Next'){
	$enrolyear=$_POST['enrolyear'];
	$enrolyear++;
	}
elseif($sub=='Previous'){
	$enrolyear=$_POST['enrolyear'];
	$enrolyear--;
	}

include('scripts/redirect.php');
?>
