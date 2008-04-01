<?php 
/**									 community_capacity_action.php
 */

$action='enrolments_matrix.php';
$action_post_vars=array('enrolyear');

if(isset($_POST['comid'])){$comid=$_POST['comid'];}
if(isset($_POST['capacity'])){$capacity=$_POST['capacity'];}
if(isset($_POST['enrolyear'])){$enrolyear=$_POST['enrolyear'];}

include('scripts/sub_action.php');

if($sub=='Submit'){
	$community=get_community($comid);
	$communityfresh=$community;
	$communityfresh['capacity']=$capacity;
	$comid=update_community($community,$communityfresh);
	}

include('scripts/redirect.php');
?>
