<?php
/**			   					httpscripts/order_print.php
 */

require_once('../../scripts/http_head_options.php');
require_once('../../lib/fetch_order.php');
//if(isset($_GET['ordids'])){$ordids=(array) $_GET['ordids'];}else{$ordids=array();}
//if(isset($_POST['ordids'])){$ordids=(array) $_POST['ordids'];}
if(isset($xmlid)){$ordids[]=$xmlid;}

	if(sizeof($ordids)==0){
		$result[]=get_string('error');
		$returnXML=$result;
		$rootName='Error';
		}
	else{

		$Orders=array();
		$Orders['Order']=array();
		/*doing one student at a time*/
		for($c=0;$c<sizeof($ordids);$c++){
			$ordid=$ordids[$c];
			$Order=fetchOrder($ordid);
			$Orders['Order'][]=$Order;
			}
		$Orders['Paper']='portrait';
		$Orders['Transform']='order_form';
		$returnXML=$Orders;
		$rootName='Orders';

		}

require_once('../../scripts/http_end_options.php');
exit;
?>