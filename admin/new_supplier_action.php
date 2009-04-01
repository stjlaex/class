<?php
/**			  					new_supplier_action.php
 */

$action='suppliers_list.php';
$cancel='suppliers_list.php';
$supid=$_POST['supid'];
$budgetyear=$_POST['budgetyear'];

$action_post_vars=array('budgetyear');
include('scripts/sub_action.php');

if($sub=='Submit'){

	if($supid==-1){
		mysql_query("INSERT INTO ordersupplier SET name='';");
		$supid=mysql_insert_id();
		}

	$Supplier=fetchSupplier();
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

	$Address=$Supplier['Address'];
	$aid=$Address['id_db'];
	reset($Address);
	while(list($key,$val)=each($Address)){
		if(isset($val['value']) & is_array($val) and isset($val['table_db'])){
			$field=$val['field_db'];
			$inname=$field;
			if(isset($_POST[$inname])){$inval=clean_text($_POST[$inname]);}
			else{$inval='';}
			if($val['value']!=$inval){
				if($val['table_db']=='address'){
					if($aid=='-1' and $inval!=''){
						mysql_query("INSERT INTO address SET region='';");
						$aid=mysql_insert_id();
						mysql_query("UPDATE ordersupplier SET address_id='$aid' WHERE id='$supid';");
						}
					mysql_query("UPDATE address SET $field='$inval' WHERE id='$aid';");
					}
				}
			}
		}


	//$result[]=get_string('newsupplieradded',$book);
	}


include('scripts/results.php');
include('scripts/redirect.php');
?>
