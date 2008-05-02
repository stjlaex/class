<?php
/**                    httpscripts/responsables_edit_pastoral.php
 */

require_once('../../scripts/http_head_options.php');

if(!isset($xmlid)){print "Failed"; exit;}
include('lib/fetch_order.php');

	list($budid,$uid)=split('-',$xmlid);
//$perms=getYearPerm($yid,$respons);
	$Responsible=array();
	$Responsible['id_db']=$budid.'-'.$uid;
//	if($perms['x']==1){
		$newperms=array('r'=>0,'w'=>0,'x'=>0);
		$result[]=update_budget_perms($uid,$gid,$newperms);
		$Responsible['exists']='false';
//		}
//	else{
//		$Responsible['exists']='true';
//		}

$returnXML=$Responsible;
$rootName='Responsible';
require_once('../../scripts/http_end_options.php');
exit;
?>

















