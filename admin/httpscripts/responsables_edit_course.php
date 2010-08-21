<?php 
/** 									responables_edit_course.php
 */

require_once('../../scripts/http_head_options.php');

if(!isset($xmlid)){print "Failed"; exit;}

	list($crid,$bid,$uid)=explode('-',$xmlid);
	$Responsible=array();
	$Responsible['id_db']=$crid.'-'.$bid.'-'.$uid;
	$d_groups=mysql_query("SELECT gid FROM groups WHERE
				course_id='$crid' AND subject_id='$bid'");
	$gid=mysql_result($d_groups,0);

	$permc=getCoursePerm($crid, $respons);
	$permb=getSubjectPerm($bid, $respons);
	if(($permc['x']!=1 and $crid!='%') or ($permb['x']!=1 and $bid!='%')){
		$error[]='You don\'t have the permissions to change this!';
		$Responsible['exists']='true';
		}
	else{
		$newperms=array('r'=>0,'w'=>0,'x'=>0);
		update_staff_perms($uid,$gid,$newperms);
		$Responsible['exists']='false';
		}
$returnXML=$Responsible;
$rootName='Responsible';
require_once('../../scripts/http_end_options.php');
exit;
?>
