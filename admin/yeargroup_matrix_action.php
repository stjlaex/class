<?php 
/**				   	   				   yeargroup_matrix_action.php
 */

$action='yeargroup_matrix.php';

if(isset($_POST['newtid'])){$newtid=$_POST['newtid'];}else{$newtid='';}
if(isset($_POST['newyid'])){$newyid=$_POST['newyid'];}else{$newyid='';}

include('scripts/sub_action.php');

if($newtid!='' and $newyid!=''){
	/*Check user has permission to edit*/
	$perm=getYearPerm($newyid);
	$neededperm='x';
	include('scripts/perm_action.php');

	$newperms=array('r'=>1,'w'=>1,'x'=>1,'e'=>1);
	$uid=get_uid($newtid);
	$d_g=mysql_query("SELECT gid FROM groups WHERE 
							course_id='' AND subject_id='' AND community_id='0' AND yeargroup_id='$newyid' AND type='p';");
	/*if no group exists create one*/
	if(mysql_num_rows($d_g)==0){
		$d_g=mysql_query("SELECT name FROM yeargroup WHERE id='$newyid';");
		$yearname=mysql_result($d_g,0);
		mysql_query("INSERT groups (yeargroup_id, name, type) VALUES ('$newyid','$yearname','p');");
		$gid=mysql_insert_id();
		}
	else{$gid=mysql_result($d_groups,0);}
	trigger_error($gid.' : '.$newyid,E_USER_WARNING);
	$result[]=update_staff_perms($uid,$gid,$newperms);
	}

include('scripts/redirect.php');
?>
