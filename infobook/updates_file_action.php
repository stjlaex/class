<?php
/**									updates_file_action.php
 *
 */

$action='export_students.php';
$action_post_vars=array('sids','catid');

include('scripts/sub_action.php');

if(isset($_POST['catid'])){$catid=$_POST['catid'];}
if(isset($_POST['update'])){$update=$_POST['update'];}
if(isset($_POST['format'])){$format=$_POST['format'];}

if($format=='xml'){$action='export_students_xml.php';}
if($format=='ppod'){$action='export_students_xml_ppod.php';}

if($sub=='Submit'){
	if((!empty($catid) and $catid!='uncheck') or ((empty($catid) or $catid=='uncheck') and $format!='')){
		$sids=array();
		if($update==1){
			$d_u=mysql_query("SELECT student_id FROM update_event WHERE export='0';");
			}
		else{
			$d_u=mysql_query("SELECT student_id FROM update_event WHERE export='1';");
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
