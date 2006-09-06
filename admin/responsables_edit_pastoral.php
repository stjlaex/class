<?php 
/**						responables_edit_pastoral.php
 *
 */

if(!isset($_GET['action'])){$action='responsables.php';}
else{$action=$_GET['action'];}

	$yid=$_GET['yid'];
	$uid=$_GET['uid'];
	$d_group=mysql_query("SELECT DISTINCT gid FROM groups WHERE yeargroup_id='$yid'");
	$gid=mysql_result($d_group,0);
	$perms=getYearPerm($yid, $respons);

	if($perms['x']!=1){
		$error[]='You don\'t have the permissions to change this!';
		}
	else{
		if(isset($_GET['fid'])){
			$fid=$_GET['fid'];
			$tid=$_GET['tid'];
			mysql_query("UPDATE form SET teacher_id='' WHERE 
				teacher_id='$tid' AND id='$fid'");
			$d_user=mysql_query("SELECT DISTINCT uid FROM user WHERE username='$tid'");
			$uid=mysql_result($d_user,0);
			}
		$newperms=array('r'=>0,'w'=>0,'x'=>0);
		$result[]=updateStaffPerms($uid,$gid,$newperms);
		}

include('scripts/results.php');
include('scripts/redirect.php');
?>
