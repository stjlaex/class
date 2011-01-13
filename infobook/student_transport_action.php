<?php
/**								student_transport_action.php
 *
 */

$action='student_view.php';
include('scripts/sub_action.php');

if($sub=='Submit'){
	/*Check user has permission to edit*/
	$yid=$Student['YearGroup']['value'];
	$perm=getYearPerm($yid, $respons);
	include('scripts/perm_action.php');

	$coms=list_member_communities($sid,array('id'=>'','name'=>'','type'=>'tutor'));
	$coms=array_merge($coms,list_member_communities($sid,array('id'=>'','name'=>'','type'=>'tutor'),false));
	foreach($coms as $com){
		$comid=$com['id'];
		if(isset($_POST[$comid.'fee'.$sid]) and $_POST[$comid.'fee'.$sid]!=$com['special']){
			$fee=$_POST[$comid.'fee'.$sid];
			mysql_query("UPDATE comidsid SET special='$fee' WHERE community_id='$comid' AND student_id='$sid';");
			}
		}


	}

	include('scripts/redirect.php');
?>
