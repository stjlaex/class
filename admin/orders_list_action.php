<?php
/**									orders_list_action.php
 */

$action='orders_list.php';

$budgetyear=$_POST['budgetyear'];

if(isset($_POST['recordid'])){
	$ordid=$_POST['recordid'];
	$detail=$_POST['detail'.$ordid];
	}
else{$ordid=-1;}
if(isset($_POST['budid'])){
	$budid=$_POST['budid'];
	}
else{$buddid=-1;}
$entryn=-1;
$action_post_vars=array('budid','ordid','entryn','budgetyear');

include('scripts/sub_action.php');

$todate=date('Y-m-d');

if($sub=='edit'){
	$action='new_order.php';
	}
elseif($sub=='authorise'){
	/* Check if this is just pettycash or some other non-supplier
	 * who we are authorising and if so it simply needs to be closed.
	 */
	$d_s=mysql_query("SELECT ordersupplier.specialaction FROM ordersupplier JOIN
					orderorder ON orderorder.supplier_id=ordersupplier.id 
					WHERE orderorder.id='$ordid';");
	$special=mysql_result($d_s,0);
	if($special==1){$orderaction=5;}
	else{$orderaction=1;}
	}
elseif($sub=='place'){
	$orderaction=2;
	}
elseif($sub=='delivery'){
	$orderaction=3;
	$action='new_invoice.php';
	}
elseif($sub=='cancel'){
	$orderaction=4;
	}
elseif($sub=='close'){
	$orderaction=5;
	}

if(isset($orderaction)){
	mysql_query("INSERT INTO orderaction SET order_id='$ordid',
		detail='$detail', actiondate='$todate', 
		action='$orderaction', teacher_id='$tid'");
	$entryn=mysql_insert_id();

	//	$result[]=get_string('order',$book).': '. 
	//		get_string(displayEnum($orderaction,'action'),$book);
	}


include('scripts/results.php');
include('scripts/redirect.php');
?>
