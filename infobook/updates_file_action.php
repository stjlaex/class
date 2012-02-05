<?php
/**									updates_file_action.php
 *
 */

$action='export_students.php';
$action_post_vars=array('sids','catid');

include('scripts/sub_action.php');

$displayfields=array();
if(isset($_POST['catid'])){$catid=$_POST['catid'];}
if(isset($_POST['update'])){$update=$_POST['update'];}


if($sub=='Submit'){
	if(!empty($catid) and $catid!='uncheck'){
		trigger_error($catid);
		$sids=array();
		if($update==1){
			$d_u=mysql_query("SELECT student_id FROM update_event WHERE export='0';");
			}
		else{
			$d_u=mysql_query("SELECT student_id FROM update_event WHERE exportdate='$update';");
			}
		while($u=mysql_fetch_array($d_u)){
			$sids[]=$u['student_id'];
			}
		if($update==1){
			$todate=date('Y-m-d');
			mysql_query("UPDATE update_event SET export='1', exportdate='$todate' WHERE export='0';");
			}
		}
	else{
		$result[]='You need to select an export view.';
		$action=$choice;
		}
	}

include('scripts/results.php');	
include('scripts/redirect.php');	
?>
