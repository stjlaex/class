<?php 
/**										alumni_search_action.php
 *
 *
 */

if(isset($_POST['comids'])){$comids=(array)$_POST['comids'];}

$action_post_vars=array('selsavedview');

$sids=array();

foreach($comids as $comid){
	$d_s=mysql_query("SELECT student_id FROM comidsid WHERE community_id='$comid';");
	while($student=mysql_fetch_array($d_s,MYSQL_ASSOC)){
		$sids[]=$student['student_id'];
		}
	}


$_SESSION['infosids']=$sids;
$_SESSION['infosearchgids']=array();
if(!isset($nolist)){
	$action='student_list.php';
	include('scripts/redirect.php');
	}
?>
