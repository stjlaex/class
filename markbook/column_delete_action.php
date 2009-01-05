<?php 
/** 									column_delete_action.php
 */

$action='class_view.php';
$action_post_vars=array('displaymid');


 if(isset($_POST['delete'])){$delete=clean_text($_POST['delete']);}
 $mid=clean_text($_POST['mid']);

 include('scripts/sub_action.php');

 $d_cids=mysql_query("SELECT DISTINCT class_id  FROM midcid WHERE mark_id='$mid';");

 if(sizeof($cids)==mysql_num_rows($d_cids) or $delete=='all'){
	 /*then no longer needed by other classes, delete*/
	 if(mysql_query("DELETE FROM mark WHERE id='$mid' LIMIT 1")){}
	 else{
		 $result[]='Failed mark may not exist!';
		 }
	 if(mysql_query("DELETE FROM score WHERE mark_id='$mid'")){}
	 else{
		 $result[]='Failed mark may not exist!';	
		 }
	 if(mysql_query("DELETE FROM midcid WHERE mark_id='$mid'"))
			{}
	 else{
		 $result[]='Failed mark may not exist!';
		 }
	 }
	 
	 elseif($delete=='only') {
		 for ($c=0;$c<sizeof($cids);$c++){
			 $cid=$cids[$c];
			 if(mysql_query("DELETE FROM midcid WHERE
					     mark_id='$mid' AND class_id='$cid' LIMIT 1"))
				 {}
			 else{
				 $result[]='Failed, mark may not exist!';	
				 $error[]=mysql_error();
				 }
			 
			 $d_sids=mysql_query("SELECT student_id FROM cidsid
							WHERE class_id='$cid'");
			 while($delsid=mysql_fetch_array($d_sids,MYSQL_ASSOC)){
				 $sid=$delsid{'student_id'};
				 if(mysql_query("DELETE FROM score WHERE
							mark_id='$mid' AND student_id='$sid'")){}
				 else{
					 $result[]='Failed to delete score!';	
					 $error[]=mysql_error();
					 }
				 }
			 }
		 }
	$displaymid='-1';
	include('scripts/results.php');
	include('scripts/redirect.php');
?>
