<?php
/**							student_view_action.php
 *
 */

$action='student_view.php';
$key=array_search($sid,$sids);	

include('scripts/sub_action.php');

if($sub=='Previous'){
	if($key>1){$key=$key-1;}else{$key=0;}
	$_POST['sid']=$sids[$key];
	$sid=$sids[$key];
	$current=$action;
	}
elseif($sub=='Next'){
	$nosids=sizeof($sids);
	if($key<$nosids-1){$key=$key+1;}else{$key=$nosids-1;}
	$_POST['sid']=$sids[$key];
	$sid=$sids[$key];
	$current=$action;
	}

include('scripts/redirect.php');	
?>
