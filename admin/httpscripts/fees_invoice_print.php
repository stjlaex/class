<?php
/**			   					httpscripts/fees_invoice_print.php
 */

require_once('../../scripts/http_head_options.php');
require_once('../../lib/fetch_fees.php');

if(isset($_GET['sids'])){$invids=(array)$_GET['sids'];}else{$invids=array();}
if(isset($_POST['sids'])){$invids=(array)$_POST['sids'];}


if(sizeof($invids)==0){
	$result[]=get_string('youneedtoselectstudents');
	$returnXML=$result;
	$rootName='Error';
	}
else{
	$Invoices=array();
	$Invoices['Invoice']=array();
	foreach($invids as $invid){
		$Invoice=fetchFeesInvoice(array('id'=>$invid));
		$Invoices['Invoice'][]=$Invoice;
		}
	$Invoices['Paper']='portrait';
	$returnXML=$Invoices;
	$rootName='Invoices';
	}

require_once('../../scripts/http_end_options.php');
exit;
?>