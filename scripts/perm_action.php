<?php
/**						scripts/perm_action.php
 */
if(!isset($neededperm)){$neededperm='r';}
if($perm["$neededperm"]!=1){
	print '<h5 class="warn">You do not have the permissions to view this page!</h5>';
	$current=$cancel;
	include('scripts/redirect.php');
	exit;
	}
unset($neededperm);
?>