<?php
/**							student_view_action.php
 *
 */

$action='student_view.php';
$action_post_vars=array('sid');
if(isset($_POST['access'])){$access=$_POST['access'];}

$key=array_search($sid,$sids);	

include('scripts/sub_action.php');

if($sub=='Previous'){
	if($key>1){$key=$key-1;}else{$key=0;}
	$sid=$sids[$key];
	$current=$action;
	}
elseif($sub=='Next'){
	$nosids=sizeof($sids);
	if($key<$nosids-1){$key=$key+1;}else{$key=$nosids-1;}
	$sid=$sids[$key];
	$current=$action;
	}


include('scripts/fees_access.php');

include('scripts/redirect.php');	
?>
