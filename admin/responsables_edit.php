<?php 
/** 									responables_edit.php
 */

$action='responsables.php';

$bid=$_GET{'bid'};
$crid=$_GET{'crid'};
$gid=$_GET{'gid'};
$uid=$_GET{'uid'};

	$permc=getCoursePerm($crid, $respons);
	$permb=getSubjectPerm($bid, $respons);
	if($permc['x']!=1 and $crid!='%'){
		$error[]='You don\'t have the permissions to change this!';
		}
	elseif($permb['x']!=1 and $bid!='%'){
		$error[]='You don\'t have the permissions to change this!';
		}
	else{
		mysql_query("DELETE FROM perms WHERE uid='$uid' AND gid='$gid' LIMIT 1");
		$result[]='Removed academic responsibility.';
		}

include('scripts/results.php');
include('scripts/redirect.php');
?>
