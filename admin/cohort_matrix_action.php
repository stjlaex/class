<?php 
/** 			   				cohort_matrix_action.php
 *
 */


$action='cohort_matrix.php';
$cancel='';

include('scripts/sub_action.php');

list($crid,$bid,$error)=checkCurrentRespon($r,$respons,'course');
if($error!=''){include('scripts/results.php');exit;}


$cohids=(array)$_POST['cohids'];

if($sub=='Submit'){

	while(list($index,$cohid)=each($cohids)){
		$index++;
		mysql_query("DELETE FROM cohidcomid WHERE cohort_id='$cohid'");	
		$comids=(array)$_POST['comids'.$index];
		while(list($index,$comid)=each($comids)){
			mysql_query("INSERT INTO cohidcomid SET cohort_id='$cohid', community_id='$comid'");
			}
		}

	}

include('scripts/redirect.php');	
?>
