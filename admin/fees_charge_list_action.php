<?php 
/**					fees_charge_list_action.php
 *
 */

$action='fees_charge_list.php';
$action_post_vars=array('conceptid','comids');

if(isset($_POST['conceptid']) and $_POST['conceptid']!=''){$conceptid=$_POST['conceptid'];}else{$conceptid='';}
if(isset($_POST['comids'])){$comids=$_POST['comids'];}else{$comids=array();}

include('scripts/sub_action.php');

if($sub=='Submit'){

	$charges=(array)list_charges($conceptid);

	$students=array();
	if(sizeof($comids)>0){
		foreach($comids as $comid){
			$com=get_community($comid);
			$coms[]=$com;
			$students=array_merge(listin_community($com),$students);
			}
		}

	foreach($students as $student){
		$sid=$student['id'];
		if(isset($_POST['tarifid'.$sid])){
			$tarifid=$_POST['tarifid'.$sid];
			if(array_key_exists($sid,$charges)){
				if($charges[$sid]['tarif_id']!=$tarifid){
					mysql_query("UPDATE fees_charge SET tarif_id='$tarifid' WHERE student_id='$sid';");
					}
				}
			elseif($tarifid!=''){
				mysql_query("INSERT INTO fees_charge (student_id,tarif_id) VALUES ('$sid','$tarifid');");
				}
			}


		}

	}

include('scripts/redirect.php');
?>