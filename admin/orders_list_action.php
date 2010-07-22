<?php
/**									orders_list_action.php
 */

$action='orders_list.php';

$budgetyear=$_POST['budgetyear'];
if(isset($_POST['ordernumber'])){$ordernumber=$_POST['ordernumber'];}
if(isset($_POST['orderstatus'])){$orderstatus=$_POST['orderstatus'];}
if(isset($_POST['ordersupid'])){$ordersupid=$_POST['ordersupid'];}

if(isset($_POST['recordid'])){
	$ordid=$_POST['recordid'];
	$detail=$_POST['detail'.$ordid];
	}
else{$ordid=-1;}

if(isset($_POST['budid'])){
	$budid=$_POST['budid'];
	}
else{
	$buddid=-1;
	}


$entryn=-1;
$action_post_vars=array('budid','ordid','entryn','budgetyear','startno',
						'ordernumber','orderstatus','ordersupid');


include('scripts/sub_action.php');

$todate=date('Y-m-d');

if(isset($_POST['nextrow']) and $_POST['nextrow']=='plus'){
	$startno=$_POST['startno']+$_POST['nextrowstep'];
	}
elseif(isset($_POST['nextrow']) and $_POST['nextrow']=='minus'){
	$startno=$_POST['startno']-$_POST['nextrowstep'];
	}

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
elseif($sub=='process'){
	$orderaction=6;
	}

if(isset($orderaction) and $orderaction!=3){
	mysql_query("INSERT INTO orderaction SET order_id='$ordid',
		detail='$detail', actiondate='$todate', 
		action='$orderaction', teacher_id='$tid';");
	$entryn=mysql_insert_id();
	}
elseif($sub=='reopen'){
	mysql_query("DELETE FROM orderaction WHERE order_id='$ordid' AND action='5';");
	}


include('scripts/redirect.php');
?>
