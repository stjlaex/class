<?php 
/**									responsables_action.php
 *
 */

$action='responsables.php';
include('scripts/sub_action.php');

$crid=$_POST['crid'];
$bid=$_POST['bid'];
$yid=$_POST['yid'];
$newuid=$_POST['user'];
$perm=$_POST['privilege'];
$email=$_POST['email'];


/* the permissions allowed by change, edit, or view*/
if($perm=='x'){$newperms=array('r'=>1,'w'=>1,'x'=>1);}
if($perm=='w'){$newperms=array('r'=1,'w'=>1,'x'=0);}
if($perm=='r'){$newperms=array('r'=1,'w'=>0,'x'=>0);}
if($email=='yes'){$newperms['e']=1;}else{$newperms['e']=0;}

$result=array();

if($yid!=''){
	$perm=getYearPerm($yid, $respons);
	if($perm['x']==1){
		$d_group=mysql_query("SELECT gid FROM groups WHERE
				yeargroup_id='$yid' AND (course_id IS NULL OR course_id='')");
		/*if no group exists create one*/
		if(mysql_num_rows($d_group)==0){
				mysql_query("SELECT name FROM yeargroup WHERE id='$yid'");
				$yearname=mysql_result($d_group,0);
				mysql_query("INSERT groups (yeargroup_id, name) VALUES ('$yid','yearname')");
				$gid=mysql_insert_id();
				}
		else{$gid=mysql_result($d_group,0);}

		if($gid==0){print 'Failed on group!'; exit;}

		$result[]=updateStaffPerms($uid,$gid,$newperms);

		}
	elseif($perm['x']!=1){
		$error[]='You don\'t have the permissions to change this!';
		}
	}

elseif($bid!='' and $crid!=''){
	$permc=getCoursePerm($crid, $respons);
	$permb=getSubjectPerm($bid, $respons);

	if(($permc['x']==1 and $crid!='%') or ($permb['x']==1 and $bid!='%' and $crid=='%')){
		$d_group=mysql_query("SELECT gid FROM groups WHERE
				subject_id='$bid' AND course_id='$crid' AND yeargroup_id IS NULL");
		if (mysql_num_rows($d_group)==0){
			/*if no group exists create one for this combination*/
				if ($crid!='%' and $bid!='%'){$name=$crid.'/'.$bid;}
				else if ($crid!='%'){$name=$crid;}
				else {$name=$bid;}
				mysql_query("INSERT groups (course_id, subject_id,
					name) VALUES ('$crid', '$bid', '$name')");
				$gid=mysql_insert_id();
				}
		else{$gid=mysql_result($d_group,0);}

		if($gid==0){print 'Failed on group!'; exit;}

		$result[]=updateStaffPerms($uid,$gid,$newperms);

		}
	elseif($permc['x']!=1 and $crid!='%'){
		$error[]='You don\'t have the permissions for this course!';
		}
	else if($permb['x']!=1 and $bid!='%' and $crid=='%'){
		$error[]='You don\'t have the permissions for this subject!';
		}
	}
else{
	$error[]='You need to select both a Course and a Subject for academic
		priviliges.';
	}
include('scripts/results.php');
include('scripts/redirect.php');
?>
