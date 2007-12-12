<?php 
/** 			   				cohort_matrix_action.php
 *
 */


$action='cohort_matrix.php';
$cancel='';

include('scripts/sub_action.php');

list($crid,$bid,$error)=checkCurrentRespon($r,$respons,'course');
if(sizeof($error)>0){include('scripts/results.php');exit;}


$cohids=(array)$_POST['cohids'];

if($sub=='Submit'){

	while(list($index,$cohid)=each($cohids)){
		if($index==0){$in1='';$in2=1;}else{$in1=$index*2;$in2=$index*2+1;}
		mysql_query("DELETE FROM cohidcomid WHERE cohort_id='$cohid'");	
		$comids1=(array)$_POST['newcomids'.$in1];
		$comids2=(array)$_POST['newcomids'.$in2];
		$comids=array_merge($comids1,$comids2);
		while(list($index,$comid)=each($comids)){
			mysql_query("INSERT INTO cohidcomid SET cohort_id='$cohid', community_id='$comid'");
			}
		}

	}

include('scripts/redirect.php');	
?>
