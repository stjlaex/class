<?php 
/**						responables_edit_pastoral.php
 *
 */

$action='responsables.php';

$yid=$_GET{'yid'};
$gid=$_GET{'gid'};
$uid=$_GET{'uid'};

	$perm=getYearPerm($yid, $respons);
	if($perm['x']!=1){
		$error[]='You don\'t have the permissions to change this!';
		}
	else { 
		mysql_query("DELETE FROM perms WHERE uid='$uid' AND gid='$gid' LIMIT 1");
		$result[]='Removed pastoral responsibility.';
		}

include('scripts/results.php');
include('scripts/redirect.php');
?>
