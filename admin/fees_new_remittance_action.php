<?php
/**			  					fees_new_remittance_action.php
 */

$action='fees_remittance_list.php';
$cancel='fees_remittance_list.php';
$conids=(array)$_POST['conids'];
$feeyear=$_POST['feeyear'];
$remid=$_POST['remid'];


$action_post_vars=array('feeyear');
include('scripts/sub_action.php');

if($sub=='Submit'){

	if($remid==-1){
		$todate=date('Y-m-d');
		mysql_query("INSERT INTO fees_remittance SET year='$feeyear', date='$todate';");
		$remid=mysql_insert_id();
		}

	$Remittance=fetchRemittance();
	foreach($Remittance as $index => $val){
		if(isset($val['value']) and is_array($val) and isset($val['table_db'])){
			$field=$val['field_db'];
			$inname=$field;
			$inval=clean_text($_POST[$inname]);
			if($val['table_db']=='fees_remittance'){
				mysql_query("UPDATE fees_remittance SET $field='$inval' WHERE id='$remid';");
				}
			}
		}

	foreach($conids as $conid){
		$d_c=mysql_query("UPDATE fees_charge AS c JOIN fees_tarif AS t ON t.id=c.tarif_id SET c.remittance_id='$remid' 
							WHERE t.concept_id='$conid';");
		}

	}

include('scripts/results.php');
include('scripts/redirect.php');
?>
