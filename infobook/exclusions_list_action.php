<?php
/**									exclusions_list_action.php
 */

$action='exclusions_list.php';

$id=$_POST['id_db'];
$detail=clean_text($_POST['detail']);
$entrydate=$_POST['entrydate'];
if(isset($_POST['newyid'])){$newyid=$_POST['newyid'];}else{$newyid=$yid;}
$enddate=$_POST['date2'];
$category=$_POST['category'];

include('scripts/sub_action.php');

	if($id!=''){
		if(mysql_query("UPDATE exclusions SET student_id='$sid',
		reason='$detail', startdate='$entrydate', enddate='$enddate', 
		category='$category' WHERE id='$id'")){
		$result[]='Exclusion recorded.';
		}
		else{$error[]=mysql_error();}
		}
	else{
		if(mysql_query("INSERT INTO exclusions SET student_id='$sid',
		reason='$detail', startdate='$entrydate', enddate='$enddate', 
		category='$category'")){
		$result[]='Exclusion recorded.';
		}
		else{$error[]=mysql_error();}
		}

$_SESSION{'Student'}=fetchStudent($sid);
include('scripts/results.php');
include('scripts/redirect.php');	
?>
