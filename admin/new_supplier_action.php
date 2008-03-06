<?php
/**			  					new_supplier_action.php
 */

$action='suppliers_list.php';
$cancel='suppliers_list.php';
$budgetyear=$_POST['budgetyear'];

$action_post_vars=array('budgetyear');
include('scripts/sub_action.php');

if($sub=='Submit'){

	$Supplier=fetchSupplier();
	mysql_query("INSERT INTO ordersupplier SET name='';");
	$supid=mysql_insert_id();
	reset($Supplier);
	while(list($index,$val)=each($Supplier)){

		if(isset($val['value']) and is_array($val) and isset($val['table_db'])){
			$field=$val['field_db'];
			$inname=$field;
			$inval=clean_text($_POST[$inname]);
				if($val['table_db']=='ordersupplier'){
					mysql_query("UPDATE ordersupplier SET $field='$inval' WHERE id='$supid'");
					}
				}
			}

	$result[]=get_string('newsupplieradded',$book);
	}


include('scripts/results.php');
include('scripts/redirect.php');
?>
