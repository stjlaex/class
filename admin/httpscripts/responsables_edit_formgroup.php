<?php
/**                    httpscripts/responsables_edit_formgroup.php
 */

require_once('../../scripts/http_head_options.php');



if(!isset($xmlid)){print "Failed"; exit;}

list($gid,$uid)=explode('-',$xmlid);
$d_groups=mysql_query("SELECT yeargroup_id FROM groups WHERE gid='$gid';");
$yid=mysql_result($d_groups,0);
$perms=getYearPerm($yid);
$perms['x'];
$Responsible=array();
$Responsible['id_db']=$xmlid;
if($perms['w']==1){
	$newperms=array('r'=>0,'w'=>0,'x'=>0);
	$result[]=update_staff_perms($uid,$gid,$newperms);
	$Responsible['exists']='false';
	}
else{
	$Responsible['exists']='true';
	}

$returnXML=$Responsible;
$rootName='Responsible';
$xmlechoer=true;
require_once('../../scripts/http_end_options.php');
exit;
?>

















