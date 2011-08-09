<?php 
/**				   	   				   formgroup_matrix_action.php
 */

$action='formgroup_matrix.php';
$action_post_vars=array('newcomtype');

if(isset($_POST['newtid']) and $_POST['newtid']!=''){$newtid=$_POST['newtid'];}else{$newtid='';}
if(isset($_POST['gid']) and $_POST['gid']!=''){$gid=$_POST['gid'];}else{$gid='';}
if(isset($_POST['pastoraltype']) and $_POST['pastoraltype']!=''){$newcomtype=$_POST['pastoraltype'];}
else{$newcomtype=$_POST['newcomtype'];}

include('scripts/sub_action.php');

if($newtid!='' AND $gid!=''){
		$newperms=array('r'=>1,'w'=>1,'x'=>1,'e'=>1);
		$uid=get_uid($newtid);
		update_staff_perms($uid,$gid,$newperms);
		}

include('scripts/results.php');
include('scripts/redirect.php');
?>
