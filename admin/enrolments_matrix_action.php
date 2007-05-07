<?php 
/**				   	   				   enrolments_matrix_action.php
 */

$action='enrolments_matrix.php';


include('scripts/sub_action.php');

if($sub=='Next'){
	$enrolyear=$_POST['enrolyear'];
	$enrolyear++;
	}

include('scripts/redirect.php');
?>
