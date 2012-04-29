<?php
/**			   					httpscripts/fees_invoice_print.php
 */

require_once('../../scripts/http_head_options.php');
require_once('../../lib/fetch_fees.php');
if(isset($_GET['id_db'])){$ordid=$_GET['id_db'];}else{$id_db='';}
if(isset($_POST['id_db'])){$ordid=$_POST['id_db'];}
if(isset($xmlid)){$ordids[]=$xmlid;}

	if($ordid==''){
		$result[]=get_string('error');
		$returnXML=$result;
		$rootName='Error';
		}
	else{
		$Orders=array();
		$Orders['Order']=array();
		//$Order=fetchOrder($ordid);
		$Orders['Order'][]=$Order;
		$Orders['Paper']='portrait';
		$Orders['Transform']='order_form';
		$returnXML=$Orders;
		$rootName='Orders';
		}

require_once('../../scripts/http_end_options.php');
exit;
?>