<?php
/**************								search_action1.php
	If more than one match calls student_list.php
	otherwise back to student_search.php
*/

$action='student_list.php';

	if(isset($_POST{'forename'})){$forename=$_POST{'forename'};}
	if(isset($_POST{'surname'})){$surname=$_POST{'surname'};}
	if(isset($_POST{'newyid'})){$newyid=$_POST{'newyid'};}
	if(isset($_POST{'newfid'})){$newfid=$_POST{'newfid'};}

   	include('scripts/find_sid.php');
/*		rows is the number of matching students found by find_sid*/

	$sids=array();	
	if ($rows>0){
		$c=0;
		while($student=mysql_fetch_array($d_sids,MYSQL_ASSOC)){
			$sids[$c]=$student{'id'};
			$c++;
			}
		$_SESSION{'infosids'}=$sids;
		}
		
/*
	elseif ($rows==1){
		$student=mysql_fetch_array($d_sids,MYSQL_ASSOC);
		$sid=$student{'id'};
   		$sids[0]=$student{'id'};
		$current='student_view.php';
		$_SESSION{'sid'}=$sid;
		$_SESSION{'sids'}=$sids;
		}
*/
	
	else{
		$error[]=get_string('nostudentsfoundtryanothersearch',$book);
		$action='';
		include('scripts/results.php');
	  	}
   	include('scripts/redirect.php');
?>
