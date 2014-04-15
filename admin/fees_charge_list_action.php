<?php 
/**					fees_charge_list_action.php
 *
 */

$action='fees_charge_list.php';
$action_post_vars=array('conceptid','comids');

if(isset($_POST['conceptid']) and $_POST['conceptid']!=''){$conceptid=$_POST['conceptid'];}else{$conceptid='';}
if(isset($_POST['comids'])){$comids=$_POST['comids'];}else{$comids=array();}
if(isset($_POST['answer0'])){$flood=$_POST['answer0'];}else{$flood='no';}
if(isset($_POST['floodtarifid'])){$flood_tarifid=$_POST['floodtarifid'];}else{$flood_tarifid='';}
if(isset($_POST['sids'])){$sids=$_POST['sids'];}else{$sids=array();}

include('scripts/sub_action.php');

if($sub=='Submit'){

	$charges=(array)list_concept_fees($conceptid);

	$students=array();
	if(sizeof($comids)>0){
		foreach($comids as $comid){
			$com=get_community($comid);
			$coms[]=$com;
			$students=array_merge(listin_community($com),$students);
			}
		}

	if(count($sids)>0){
		foreach($sids as $sid){
			if(array_key_exists($sid,$charges)){
				if($charges[$sid][0]['tarif_id']!=$flood_tarifid){
					$charid=$charges[$sid][0]['id'];
					if($flood_tarifid!=''){
						mysql_query("UPDATE fees_applied SET tarif_id='$flood_tarifid' WHERE id='$charid';");
						}
					else{
						delete_fee($charid);
						}
					}
				}
			elseif($flood_tarifid!=''){
				apply_student_fee($sid,'',$flood_tarifid);
				}
			}
		}
	else{
		foreach($students as $student){
			$sid=$student['id'];

			if($flood=='yes'){
				if(array_key_exists($sid,$charges)){
					if($charges[$sid][0]['tarif_id']!=$flood_tarifid){
						$charid=$charges[$sid][0]['id'];
						if($flood_tarifid!=''){
							mysql_query("UPDATE fees_applied SET tarif_id='$flood_tarifid' WHERE id='$charid';");
							}
						else{
							delete_fee($charid);
							}
						}
					}
				elseif($flood_tarifid!=''){
					apply_student_fee($sid,'',$flood_tarifid);
					}
				}
			elseif($flood=='no' and isset($_POST['tarifid'.$sid])){
				$tarifid=$_POST['tarifid'.$sid];
				if(array_key_exists($sid,$charges)){
					if($charges[$sid][0]['tarif_id']!=$tarifid){
						$charid=$charges[$sid][0]['id'];
						if($tarifid!=''){
							mysql_query("UPDATE fees_applied SET tarif_id='$tarifid' WHERE id='$charid';");
							}
						else{
							delete_fee($charid);
							}
						}
					}
				elseif($tarifid!=''){
					apply_student_fee($sid,'',$tarifid);
					}
				}
			}
		}

	}

include('scripts/redirect.php');
?>
