<?php
/**			  					fees_new_tarif_action.php
 */

$action='fees_concept_list.php';
$cancel='fees_concept_list.php';
$conid=$_POST['conid'];
$tarid=$_POST['tarid'];
$feeyear=$_POST['feeyear'];

$action_post_vars=array('feeyear');
include('scripts/sub_action.php');

if($sub=='Submit'){

	if($tarid==-1){
		mysql_query("INSERT INTO fees_tarif SET concept_id='$conid';");
		$tarid=mysql_insert_id();
		}

	$Tarif=fetchTarif();
	foreach($Tarif as $index => $val){
		if(isset($val['value']) and is_array($val) and isset($val['table_db'])){
			$field=$val['field_db'];
			$inname=$field;
			$inval=clean_text($_POST[$inname]);
			if($val['table_db']=='fees_tarif'){
				mysql_query("UPDATE fees_tarif SET $field='$inval' WHERE id='$tarid';");
				}
			}
		}

	}


include('scripts/results.php');
include('scripts/redirect.php');
?>
