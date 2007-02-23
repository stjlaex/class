<?php
/**                    httpscripts/responsables_edit_pastoral.php
 */

require_once('../../scripts/http_head_options.php');

if(!isset($xmlid)){print "Failed"; exit;}

	list($yid,$uid)=split('-',$xmlid);
	$d_groups=mysql_query("SELECT gid FROM groups WHERE
				yeargroup_id='$yid' AND course_id=''");
	$gid=mysql_result($d_groups,0);
	$perms=getYearPerm($yid,$respons);
	$Responsible=array();
	$Responsible['id_db']=$yid.'-'.$uid;
	if($perms['x']==1){
		$newperms=array('r'=>0,'w'=>0,'x'=>0);
		$result[]=update_staff_perms($uid,$gid,$newperms);
		$Responsible['exists']='false';
		}
	else{
		$Responsible['exists']='true';
		}

$returnXML=$Responsible;
$rootName='Responsible';
require_once('../../scripts/http_end_options.php');
exit;
?>

















