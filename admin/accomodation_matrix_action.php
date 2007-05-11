<?php 
/**				   	   				   accomodation_matrix_action.php
 */

$action='accomodation_matrix.php';
$action_post_vars=array('startday');

if(isset($_POST['startday']) and $_POST['startday']!=''){$startday=$_POST['startday'];}
else{$startday=0;}

include('scripts/sub_action.php');

if($sub=='Previous'){
	$startday=$startday-7;
	}
elseif($sub=='Next'){
	$startday=$startday+7;
	//if($startday>=0){$startday='';}
	}
elseif($sub=='Submit'){
	}

include('scripts/redirect.php');
?>
