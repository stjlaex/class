<?php
/**			  					new_budget_action.php
 */

$action='orders.php';

include('scripts/sub_action.php');

if($sub=='Submit'){

	$gid=$_POST['gid'];
	$yearcode=-1;
	$Budget=fetchBudget();
	mysql_query("INSERT INTO orderbudget SET gid='$gid', yearcode='$yearcode';");
	$budid=mysql_insert_id();
	reset($Budget);
	while(list($index,$val)=each($Budget)){
		if(isset($val['value']) and is_array($val) and isset($val['table_db'])){
			$field=$val['field_db'];
			$inname=$field;
			$inval=clean_text($_POST[$inname]);
				if($val['table_db']=='orderbudget'){
					mysql_query("UPDATE orderbudget SET $field='$inval' WHERE id='$budid'");
					}
				}
			}

	$result[]=get_string('newbudgetadded',$book);
	}


include('scripts/results.php');
include('scripts/redirect.php');
?>
