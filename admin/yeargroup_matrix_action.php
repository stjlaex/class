<?php 
/**				   	   				   yeargroup_matrix_action.php
 */

$action='yeargroup_matrix.php';

if($_POST['newtid']!=''){$newtid=$_POST['newtid'];}
if(isset($_POST['newyid'])){$newfid=$_POST['newyid'];}else[$newyid='';]

include('scripts/sub_action.php');

if($newtid!='' AND $newyid!=''){
	/*Check user has permission to edit*/
	$perm=getYearPerm($newyid,$respons);
	$neededperm='x';
	include('scripts/perm_action.php');
	$newperms=array('r'=>1,'w'=>1,'x'=>0,'e'=>1);
	$d_users=mysql_query("SELECT uid FROM users WHERE
							username='$newtid' AND nologin='0'");
	$uid=mysql_result($d_users,0);
	$d_group=mysql_query("SELECT gid FROM groups WHERE
				yeargroup_id='$newyid' AND (course_id IS NULL OR course_id='')");
	/*if no group exists create one*/
	if(mysql_num_rows($d_group)==0){
		mysql_query("SELECT name FROM yeargroup WHERE id='$newyid'");
		$yearname=mysql_result($d_group,0);
		mysql_query("INSERT groups (yeargroup_id, name) VALUES ('$newyid','yearname')");
		$gid=mysql_insert_id();
		}
	else{$gid=mysql_result($d_group,0);}

	$result[]=updateStaffPerms($uid,$gid,$newperms);
	}

include('scripts/results.php');
include('scripts/redirect.php');
?>
