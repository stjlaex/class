<?php
/**									exclusions_list_action.php
 */

$action='exclusions_list.php';

$id=$_POST['id_db'];
$detail=clean_text($_POST['detail']);
$startdate=$_POST['startdate'];
if(isset($_POST['newyid'])){$newyid=$_POST['newyid'];}else{$newyid=$yid;}
$enddate=$_POST['enddate'];
$category=$_POST['category'];

include('scripts/sub_action.php');

	if($id!=''){
		mysql_query("UPDATE exclusions SET 
		reason='$detail', startdate='$startdate', enddate='$enddate', 
		category='$category' WHERE student_id='$sid' AND startdate='$id'");
		}
	else{
		mysql_query("INSERT INTO exclusions SET student_id='$sid',
		reason='$detail', startdate='$startdate', enddate='$enddate', 
		category='$category'");
		}

include('scripts/redirect.php');	
?>
