<?php
/**			  					new_invoice_action.php
 */

$action='orders_list.php';
$cancel='orders_list.php';
$budid=$_POST['budid'];
$ordid=$_POST['ordid'];
$entryn=$_POST['entryn'];
$action_post_vars=array('budid');

include('scripts/sub_action.php');

if($sub=='Submit'){

	$yearcode=-1;
	$Invoice=fetchInvoice();
	mysql_query("INSERT INTO orderinvoice SET debitcost='';");
	$invid=mysql_insert_id();
	mysql_query("UPDATE orderaction SET invoice_id='$invid' WHERE
					order_id='$ordid' AND entryn='$entryn';");
	reset($Invoice);
	while(list($index,$val)=each($Invoice)){
		if(isset($val['value']) and is_array($val) and isset($val['table_db'])){
			$field=$val['field_db'];
			$inname=$field;
			$inval=clean_text($_POST[$inname]);
			if($val['table_db']=='orderinvoice'){
				mysql_query("UPDATE orderinvoice SET $field='$inval' WHERE id='$invid';");
				}
			}
		}

	$result[]=get_string('newinvoicerecorded',$book);
	}


include('scripts/results.php');
include('scripts/redirect.php');
?>
