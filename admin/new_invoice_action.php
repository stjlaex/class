<?php
/**			  					new_invoice_action.php
 *
 */

$action='orders_list.php';
$cancel='orders_list.php';
$budid=$_POST['budid'];
$ordid=$_POST['ordid'];
$entryns=(array)$_POST['sids'];
$action_post_vars=array('budid','ordid');

include('scripts/sub_action.php');

if($sub=='Submit'){

	$todate=date('Y-m-d');
	$yearcode=-1;
	$Invoice=fetchInvoice();

	mysql_query("INSERT INTO orderinvoice SET debitcost='';");
	$invid=mysql_insert_id();

	mysql_query("INSERT INTO orderaction SET order_id='$ordid',
		detail='$detail', actiondate='$todate', 
		action='3', teacher_id='$tid', invoice_id='$invid';");

	foreach($Invoice as $val){
		if(isset($val['value']) and is_array($val) and isset($val['table_db'])){
			$field=$val['field_db'];
			$inname=$field;
			$inval=clean_text($_POST[$inname]);
			if($val['table_db']=='orderinvoice'){
				mysql_query("UPDATE orderinvoice SET $field='$inval' WHERE id='$invid';");
				}
			}
		}

	foreach($entryns as $entryn){
		mysql_query("UPDATE ordermaterial SET invoice_id='$invid' WHERE order_id='$ordid' AND entryn='$entryn';");
		}

	//$result[]=get_string('newinvoicerecorded',$book);
	}


include('scripts/results.php');
include('scripts/redirect.php');
?>
