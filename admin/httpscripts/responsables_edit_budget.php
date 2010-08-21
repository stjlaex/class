<?php
/**                    httpscripts/responsables_edit_budget.php
 */

require_once('../../scripts/http_head_options.php');

if(!isset($xmlid)){print "Failed"; exit;}
include('../../lib/fetch_order.php');

	list($budid,$uid)=explode('-',$xmlid);
	$perms=get_budget_perms($budid);
	$Responsible=array();
	$Responsible['id_db']=$budid.'-'.$uid;
	if($perms['x']==1){
		$newperms=array('r'=>0,'w'=>0,'x'=>0,'e'=>0);
		$result[]=update_budget_perms($uid,$budid,$newperms);
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
