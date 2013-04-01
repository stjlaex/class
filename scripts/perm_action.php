<?php
/**						scripts/perm_action.php
 *
 * By default will checking for read access. 
 * Set $neededperm = w or x for testing other levels
 */
if(!isset($neededperm)){$neededperm='r';}
if($perm["$neededperm"]!=1){
	$result[]=get_string('nopermissions',$book);
	$action=$cancel;
	include('scripts/results.php');
	include('scripts/redirect.php');
	exit;
	}
unset($neededperm);
?>