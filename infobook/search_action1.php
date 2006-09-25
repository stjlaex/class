<?php
/**************								search_action1.php
	If more than one match calls student_list.php
	otherwise back to student_search.php
*/

$action='student_list.php';

	if(isset($_POST['forename'])){$forename=clean_text($_POST['forename']);}
	if(isset($_POST['surname'])){$surname=clean_text($_POST['surname']);}
	if(isset($_POST['newyid'])){$newyid=$_POST['newyid'];}
	if(isset($_POST['newfid'])){$newfid=$_POST['newfid'];}

   	include('scripts/find_sid.php');
/*		rows is the number of matching students found by find_sid*/

	$sids=array();	
	if($rows>0){
		$c=0;
		while($student=mysql_fetch_array($d_sids,MYSQL_ASSOC)){
			$sids[$c]=$student['id'];
			$c++;
			}
		$_SESSION['infosids']=$sids;
		}
	else{
		$result[]=get_string('nostudentsfoundtryanothersearch',$book);
		$action='';
		include('scripts/results.php');
	  	}
   	include('scripts/redirect.php');
?>
