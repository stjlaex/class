<?php 
/** 									responables_edit.php
 */

$action='responsables.php';

$bid=$_GET['bid'];
$crid=$_GET['crid'];
$gid=$_GET['gid'];
$uid=$_GET['uid'];

	$permc=getCoursePerm($crid, $respons);
	$permb=getSubjectPerm($bid, $respons);
	if($permc['x']!=1 and $crid!='%'){
		$error[]='You don\'t have the permissions to change this!';
		}
	elseif($permb['x']!=1 and $bid!='%'){
		$error[]='You don\'t have the permissions to change this!';
		}
	else{
		$staffperms=array('r'=>0,'w'=>0,'x'=>0);
		$result[]=updateStaffPerms($uid,$gid,$staffperms);
		}

include('scripts/results.php');
include('scripts/redirect.php');
?>
