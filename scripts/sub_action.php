<?php
/**						scripts/sub_action.php
 *
 */

$sub=$_POST['sub'];
if($sub=='Cancel'){
	if($cancel==''){$action='';$choice='';}
	else{$action=$cancel;}
	//	$result[]=$sub;
	//	include('scripts/results.php');
	include('scripts/redirect.php');
	exit;
	}
?>