<?php
/**                    httpscripts/responsables_edit_formgroup.php
 */

require_once('../../scripts/http_head_options.php');

if(!isset($xmlid)){print "Failed"; exit;}

	list($fid,$uid)=split('-',$xmlid);
	$perms=getFormPerm($fid, $respons);

	$Responsible=array();
	$Responsible['id_db']=$fid.'-'.$uid;
	if($perms['x']==1){
		$d_form=mysql_query("SELECT DISTINCT yeargroup_id
						FROM form WHERE id='$fid'");
		$yid=mysql_result($d_form,0);
		$d_groups=mysql_query("SELECT DISTINCT gid
					FROM groups WHERE yeargroup_id='$yid' AND course_id IS NULL");
		$gid=mysql_result($d_groups,0);
		mysql_query("UPDATE form SET teacher_id='' WHERE id='$fid'");
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

















