<?php
/**						scripts/sub_action.php
 *
 */

$sub=$_POST['sub'];
if($sub=='Cancel'){
	if($cancel==''){$action=''; $choice='';}
	else{$action=$cancel;}
	include('scripts/redirect.php');
	exit;
	}
?>