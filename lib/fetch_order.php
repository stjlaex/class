<?php	
/**	   							 lib/fetch_order.php
 *
 *	@package	ClaSS
 *	@author		stj@laex.org
 *	@copyright	S T Johnson 2004-2008
 *	@version	
 *	@since		
 *
 *   	$Order['']=array('label' => '', 
 *							  'inputtype'=> 'required',
 *							  'table_db' => 'order', 
 *							  'field_db' => '',
 *							  'type_db' => '', 
 *							  'default_value' => 'N',
 *							  'value' => ''.$order['']
 *							  );
 */

/**
 * Returns the budgetname for that ordid from the database
 *
 * @param integer $ordid
 * @return string
 */
function get_budgetname($ordid){
	if($ordid!=' ' and $ordid!=''){
		$d_b=mysql_query("SELECT name FROM orderbudget WHERE id='$ordid';");
		$name=mysql_result($d_b,0);
		}
	else{
		$name='BLANK';
		}
	return $name;
	}


/**
 * Returns an array of users with a given permissions level
 * for the budid
 *
 * @param integer $budid
 * @param array $perms
 * @return array
 *
 */
function list_budget_users($budid,$perms){
   	$users=array();

	$r=$perms['r'];
	$w=$perms['w'];
	$x=$perms['x'];
	$d_users=mysql_query("SELECT users.uid,
			   	username, forename, surname, email, emailuser,
				nologin, role FROM users JOIN perms ON 
				users.uid=perms.uid WHERE 
				perms.gid=(SELECT gid FROM orderbudget WHERE id='$budid') 
				AND perms.r='$r' AND perms.w='$w' AND perms.x='$x' AND
				username!='administrator' 
				ORDER BY username;");
	while($user=mysql_fetch_array($d_users,MYSQL_ASSOC)){
		$uid=$user['uid'];
		$users[$uid]=$user;
		}
	return $users;
	}

/**
 *
 * Returns a list of budgets for the current budgetyear to which the
 * user has access; the perms table is by passed if this is a budget
 * admin user and all budgets for year are returned.
 *
 * @param integer $tid
 * @param integer $budgetyear
 * @return array
 */
function list_user_budgets($tid,$budgetyear='%'){
	$uid=get_uid($tid);
	$aperm=get_admin_perm('b',$uid);
	$budgets=array();
	if($budgetyear!='%'){
		$yearcode=get_budgetyearcode($budgetyear);
		}
	if($aperm==1){
		$d_b=mysql_query("SELECT id, code, yearcode, name, costlimit, overbudget_id,
				    '1' AS r, '1' AS w, '1' AS x FROM orderbudget WHERE
			   		(orderbudget.yearcode='%' OR orderbudget.yearcode LIKE '$yearcode') ORDER
			   		BY orderbudget.yearcode, orderbudget.overbudget_id ASC, 
						orderbudget.section_id, orderbudget.name;");
		}
	else{
		$d_b=mysql_query("SELECT id, code, yearcode, name, costlimit, overbudget_id,
			  perms.r AS r, perms.w AS w, perms.x AS x FROM orderbudget JOIN perms ON
			  perms.gid=orderbudget.gid WHERE perms.uid='$uid' AND
			  (orderbudget.yearcode='%' OR orderbudget.yearcode LIKE '$yearcode') ORDER
			   		BY orderbudget.yearcode, orderbudget.overbudget_id ASC, 
						orderbudget.section_id, orderbudget.name;");
		}
	while($budget=mysql_fetch_array($d_b,MYSQL_ASSOC)){
		if($budget['overbudget_id']==0){
			$budgets[$budget['id']]=array();
			$budgets[$budget['id']]['subbudgets']=array();
			$budgets[$budget['id']]['subbudgets'][]=$budget;
			}
		else{
			$budgets[$budget['overbudget_id']]['subbudgets'][]=$budget;
			}
		}
	return $budgets;
	}


/**
 *
 * Returns a list of subbudgets for the given budid.
 *
 * @param integer $budid
 * @return array
 */
function list_subbudgets($budid){
	$d_b=mysql_query("SELECT id, code, yearcode, name, costlimit, overbudget_id
			  FROM orderbudget WHERE overbudget_id='$budid' ORDER
			   		BY orderbudget.section_id, orderbudget.name;");
	while($budget=mysql_fetch_array($d_b,MYSQL_ASSOC)){
		$budgets[$budget['id']]=$budget;
		}
	return $budgets;
	}


/**
 *
 * @param integer $budid
 * @return array
 */
function list_budget_orders($budid){
	$orders=array();
	$d_order=mysql_query("SELECT id FROM orderorder WHERE budget_id='$budid' ORDER
			   		BY entrydate DESC");
	while($order=mysql_fetch_array($d_order,MYSQL_ASSOC)){
		$orders[]=$order;
		}
	return $orders;
	}

/**
 *
 * @param integer $invoicenumber
 * @return array
 */
function list_invoice_orders($invoicenumber){
	$orders=array();
	$d_o=mysql_query("SELECT DISTINCT orderaction.order_id AS id FROM orderaction JOIN orderinvoice ON
				orderaction.invoice_id=orderinvoice.id WHERE
				orderinvoice.reference='$invoicenumber' ORDER BY orderaction.order_id;");
	while($order=mysql_fetch_array($d_o,MYSQL_ASSOC)){
		$orders[]=$order;
		}
	return $orders;
	}


/**
 *
 * @param integer $ordernumber
 * @param integer $orderstatus
 * @param integer $ordersupid
 * @return array
 */
function list_orders($ordernumber='%',$orderstatus='%',$ordersupid='%'){
	$orders=array();
	if($ordersupid==''){$ordersupid='%';}
	if($orderstatus==''){$orderstatus='%';}
	if($ordernumber!='%' and $ordernumber!=''){
		list($budcode,$staffcode,$yearcode,$ordid)=explode('-',$ordernumber);
		}
	if(!isset($budcode) or $budcode==''){$budcode='%';}
	if(!isset($staffcode) or $staffcode==''){$staffcode='%';}
	if(!isset($yearcode) or $yearcode==''){$yearcode='%';}
	if(!isset($ordid) or $ordid==''){$ordid='%';}
	$d_order=mysql_query("SELECT orderorder.id FROM orderorder JOIN orderbudget ON 
				orderbudget.id=orderorder.budget_id 
				WHERE orderbudget.code LIKE '$budcode' AND 
				orderbudget.yearcode LIKE '$yearcode' AND 
				orderorder.teacher_id LIKE '$staffcode' AND 
				orderorder.id LIKE '$ordid' AND 
				orderorder.supplier_id LIKE '$ordersupid' 
				ORDER BY entrydate DESC;");
	while($order=mysql_fetch_array($d_order,MYSQL_ASSOC)){
		if($orderstatus!='%' and $orderstatus!=''){
			$ordid=$order['id'];
			$d_a=mysql_query("SELECT action FROM orderaction
				 WHERE order_id='$ordid' ORDER BY entryn DESC LIMIT 1;");
			if(mysql_num_rows($d_a)>0){
				$action=mysql_result($d_a,0);
				if($action==$orderstatus){$orders[]=$order;}
				}
			else{
				if($orderstatus==0){$orders[]=$order;}
				}
			}
		else{
			$orders[]=$order;
			}
		}
	return $orders;
	}

/**
 * Given an ordid then returns the reference number.
 * This function defines the formula for generating these order references. It could be
 * anything. But here the budget code and the yearcode are prepended
 * to the unique order id. 
 *
 * @param integer $ordid
 * @return string
 */
function get_order_reference($ordid){
	$d_b=mysql_query("SELECT orderbudget.code,
		orderbudget.yearcode, orderorder.teacher_id FROM orderbudget JOIN orderorder
					ON orderorder.budget_id=orderbudget.id WHERE orderorder.id='$ordid';");

	$ref=mysql_fetch_array($d_b,MYSQL_ASSOC);
	/* This gives an order reference of format: budget-teacher-year-id */
	$reference=strtoupper($ref['code'].'-'.$ref['teacher_id'].'-'.$ref['yearcode'].'-'.$ordid);
	return $reference;
	}


/**
 *
 * @param integer $ordid
 * @return array
 */
function fetchOrder($ordid='-1'){
	$Order=array();
	$d_o=mysql_query("SELECT budget_id, supplier_id, entrydate,
						currency, teacher_id, detail 
						FROM orderorder WHERE id='$ordid';");
	$order=mysql_fetch_array($d_o,MYSQL_ASSOC);
	$Order=array();
	$Order['id_db']=$ordid;
   	$Order['Date']=array('label' => 'date', 
						 'inputtype'=> 'required',
						 'table_db' => 'orderorder', 
						 'field_db' => 'entrydate',
						 'type_db' => 'date', 
						 'default_value' => date('Y-m-d'),
						 'value' => ''.$order['entrydate']
						 );
   	$Order['Currency']=array('label' => 'currency', 
							 'inputtype'=> 'required',
							 'table_db' => 'orderorder', 
							 'field_db' => 'currency',
							 'type_db' => 'enum', 
							 'default_value' => '0',
							 'value' => ''.$order['currency']
							 );
	$Order['Detail']=array('label' => 'note',
						   'table_db' => 'orderorder', 
						   'field_db' => 'detail',
						   'type_db' => 'text', 
						   'value' => ''.$order['detail']
						   );
   	$Order['Lodged']=array('label' => 'lodgedby', 
						   'field_db' => 'teacher_id',
						   'type_db' => 'varchar(14)', 
						   'value_db' => ''.$order['teacher_id'],
						   'value' => ''.get_teachername($order['teacher_id'])
						 );
   	$Order['Budget']=array('label' => 'budget',
						   'value_db' => ''.$order['budget_id'],
						   'value' => ''.get_budgetname($order['budget_id'])
						   );
   	$Order['Reference']=array('label' => 'reference', 
							  'value' => ''.get_order_reference($ordid)
							  );
	$Order['Supplier']=(array)fetchSupplier($order['supplier_id']);
	$Order['Materials']=(array)fetchMaterials($ordid);
	$status='lodged';
	$d_act=mysql_query("SELECT * FROM orderaction
				WHERE order_id='$ordid' ORDER BY entryn");
	$Actions=array();
	while($action=mysql_fetch_array($d_act)){
		$Action=array();
		$Action['no_db']=$action['entryn'];
		$acttid=$action['teacher_id'];
		$Action['Teacher']=array('username' => ''.$acttid, 
								 'label' => 'teacher',
								 'value' => get_teachername($acttid));
		$Action['Detail']=array('label' => 'note',
								 'type_db' => 'text', 
								 'value' => ''.$action['detail']
								 );
		$Action['Date']=array('label' => 'date', 
							  'type_db' => 'date', 
							  'value' => ''.$action['actiondate']);
		$status=displayEnum($action['action'],'action');
		$Action['Category']=array('label' => 'cateogry', 
								  'value_db' => ''.$action['action'],
								  'value' => ''.$status);
		if($action['invoice_id']!=0){
			$Action['Invoice']=fetchInvoice($action['invoice_id']);
			}
		$Actions['Action'][]=$Action;
		}
	$Order['Actions']=(array)$Actions;
	/* The last action's status establishes the current status of the
	 * order. 
	 */
	$Order['Status']=array('label' => 'status',
						   'value' => ''.$status
						   );
	return $Order;
	}


/**
 *
 * @param integer $ordid
 * @return array
 */
function fetchMaterials($ordid){
	$Materials=array();
	$Materials['Material']=array();
	$d_m=mysql_query("SELECT entryn, quantity, unitcost, detail,
			   refno, materialtype FROM ordermaterial WHERE order_id='$ordid' ORDER BY entryn;");
	list($ratingnames,$catdefs)=fetch_categorydefs('mat');
	while($mat=mysql_fetch_array($d_m,MYSQL_ASSOC)){
		$Material=(array)fetchMaterial($mat,$catdefs);
		$Materials['Material'][]=$Material;
		}
	return $Materials;
	}


/**
 *
 * @param array $mat
 * @return array
 */
function fetchMaterial($mat=array('entryn'=>'','materialtype'=>'','detail'=>'', 
								  'quantity'=>'','unitcost'=>'','refno'=>''),$catdefs=array()){

	if(sizeof($catdefs)==0){list($ratingnames,$catdefs)=fetch_categorydefs('mat');}
	if(array_key_exists($mat['materialtype'],$catdefs)){
		$materialtype_name=$catdefs[$mat['materialtype']]['name'];
		}
	else{$materialtype_name='';}
	$Material=array();
	$Material['id_db']=$mat['entryn'];
   	$Material['Type']=array('label' => 'type',
							//'inputtype'=> 'required',	
							'table_db' => 'ordermaterial', 
							'field_db' => 'materialtype',
							'type_db' => 'char(2)', 
							'default_value' => '',
							'value_db' => ''.$mat['materialtype'],
							'value' => ''.$materialtype_name
							);
	$Material['Quantity']=array('label' => 'quantity', 
								//'inputtype'=> 'required',
								'table_db' => 'ordermaterial', 
								'field_db' => 'quantity',
								'type_db' => 'smallint', 
								'value' => ''.$mat['quantity']
								);
	$Material['Unitcost']=array('label' => 'unitcost', 
								//'inputtype'=> 'required',
								'table_db' => 'ordermaterial', 
								'field_db' => 'unitcost',
								'type_db' => 'decimal', 
								'value' => ''.$mat['unitcost']
								);
	$Material['SupplierReference']=array('label' => 'supplierreference', 
										 'table_db' => 'ordermaterial', 
										 'field_db' => 'refno',
										 'type_db' => 'varchar(240)', 
										 'value' => ''.$mat['refno']
										 );
	$Material['Detail']=array('label' => 'detail', 
							  //'inputtype'=> 'required',
							  'table_db' => 'ordermaterial', 
							  'field_db' => 'detail',
							  'type_db' => 'varchar(600)', 
							  'value' => ''.$mat['detail']
							  );
	return $Material;
	}

/**
 *
 * @param integer $budid
 * @return array
 */
function fetchBudget($budid='-1'){
	$d_bud=mysql_query("SELECT * FROM orderbudget WHERE id='$budid';");
	$bud=mysql_fetch_array($d_bud,MYSQL_ASSOC);
	$Budget=array();
	$Budget['id_db']=$budid;
	$Budget['gid_db']=$bud['gid'];
	$Budget['overbudid_db']=$bud['overbudget_id'];
   	$Budget['Section']=array('label' => 'section', 
							 'inputtype'=> 'required',
							 //'table_db' => 'orderbudget', 
							 'field_db' => 'section_id',
							 'type_db' => 'smallint', 
							 'value_db' => ''.$bud['section_id'],
							 'value' => ''.get_sectionname($bud['section_id'])
							 );
	/* The budget code has to be unique for this yearcode so don't
	 * just allow updates with first checking!
	 */
   	$Budget['Code']=array('label' => 'code', 
						  'inputtype'=> 'required',
						  'table_db' => 'orderbudget', 
						  'field_db' => 'code',
						  'type_db' => 'varchar(8)', 
						  'value' => ''.$bud['code']
						  );
	/* Generated automatically to be meaningful based on the scope of the budget*/
   	$Budget['Name']=array('label' => 'name', 
						  'inputtype'=> 'required',
						  'table_db' => 'orderbudget', 
						  'field_db' => 'name',
						  'type_db' => 'varchar(160)', 
						  'value' => ''.$bud['name']
						  );
	/* The yearcode should only be generated calling get_budgetyearcode.*/
   	$Budget['YearCode']=array('label' => 'year', 
							  'inputtype'=> 'required',
							  //'table_db' => 'orderbudget', 
							  'field_db' => 'yearcode',
							  'type_db' => 'char(2)', 
							  'value' => ''.$bud['yearcode']
							  );
   	$Budget['Limit']=array('label' => 'limit', 
						   //'inputtype'=> 'required',
						   'table_db' => 'orderbudget', 
						   'field_db' => 'costlimit',
						   'type_db' => 'decimal', 
						   'value' => ''.$bud['costlimit']
						   );
	/*
   	$Budget['Currency']=array('label' => 'currency', 
							  'inputtype'=> 'required',
							  'table_db' => 'orderbudget', 
							  'field_db' => 'currency',
							  'type_db' => 'enum', 
							  'value' => ''.$bud['currency']
							  );
	*/
	return $Budget;
	}

/**
 *
 * @param integer $budid
 * @return array
 */
function fetchSupplier($supid=-1){
	if($supid==''){$supid=-1;}
	$d_sup=mysql_query("SELECT * FROM ordersupplier WHERE id='$supid' ORDER BY name;");
	$sup=mysql_fetch_array($d_sup,MYSQL_ASSOC);
	$aid=$sup['address_id'];
	if($aid==0){$aid=-1;}

	$Supplier=array();
	$Supplier['id_db']=$supid;
   	$Supplier['Name']=array('label' => 'name', 
							'inputtype'=> 'required',
							'table_db' => 'ordersupplier', 
							'field_db' => 'name',
							'type_db' => 'varchar(160)', 
							'value' => ''.$sup['name']
							);
   	$Supplier['Inactive']=array('label' => 'inactive', 
								//'inputtype'=> 'required',
								'table_db' => 'ordersupplier', 
								'field_db' => 'inactive',
								'type_db' => 'enum', 
								'value' => ''.$sup['inactive']
								);
   	$Supplier['Email']=array('label' => 'email', 
							 //'inputtype'=> 'required',
							 'table_db' => 'ordersupplier', 
							 'field_db' => 'email',
							 'type_db' => 'varchar(240)', 
							 'value' => ''.$sup['email']
							 );
   	$Supplier['Phone']=array('label' => 'phonenumber', 
							 //'inputtype'=> 'required',
							 'table_db' => 'ordersupplier', 
							 'field_db' => 'phonenumber1',
							 'type_db' => 'varchar(22)', 
							 'value' => ''.$sup['phonenumber1']
							 );
   	$Supplier['Fax']=array('label' => 'faxnumber', 
						   //'inputtype'=> 'required',
						   'table_db' => 'ordersupplier', 
						   'field_db' => 'phonenumber2',
						   'type_db' => 'varchar(22)', 
						   'value' => ''.$sup['phonenumber2']
						   );

	$Supplier['Address']=fetchAddress(array('address_id'=>$aid,'addresstype'=>''));


	return $Supplier;
	}


/**
 * This returns a budget's remaining balance calcualted from the
 * current balance minus the value of all outstanding orders
 * 
 * @param integer $budid
 * @return float
 */
function get_budget_projected($budid=-1){

	$currencyrates=get_budget_currencyrates($budid);

	$sum=0;
	while(list($currency,$rate)=each($currencyrates)){
		/* Projected values come from the material table entered by users */
		$d_0=mysql_query("SELECT SUM(unitcost*quantity) FROM
				ordermaterial JOIN orderorder ON
				orderorder.id=ordermaterial.order_id WHERE
				orderorder.budget_id='$budid' AND
				orderorder.currency='$currency';");
		/* Want to exclude closed (5) and cancelled (4) orders */
		$d_3=mysql_query("SELECT SUM(unitcost*quantity) FROM
				ordermaterial WHERE 
				ordermaterial.order_id=ANY(SELECT
				DISTINCT order_id FROM orderaction JOIN orderorder ON
				orderorder.id=orderaction.order_id  
				WHERE orderorder.budget_id='$budid' AND 
				orderorder.currency='$currency' AND
				(orderaction.action='5' OR orderaction.action='4'));");
		$value=mysql_result($d_0,0)-mysql_result($d_3,0);

		$sum+=$rate*$value;
		}

	$current=get_budget_current($budid);
	$costremain=round($current-$sum,0);

	return $costremain;
	}


/**
 * Sum all the invoices (linked to action=3 delivered) for orders
 * closed (action=5) and calculate the remaining balance. 
 *
 * @param integer $budid
 * @return float
 */
function get_budget_current($budid=-1){

	global $CFG;

	$currencyrates=get_budget_currencyrates($budid);

	$d_bud=mysql_query("SELECT costlimit FROM orderbudget WHERE id='$budid';");
	if(mysql_num_rows($d_bud)>0){
		$sum=0;
		$costlimit=mysql_result($d_bud,0);
		/* Iterate over each currency in use */
		while(list($currency,$rate)=each($currencyrates)){
			$d_closed=mysql_query("SELECT id, supplier_id FROM orderorder
				JOIN orderaction ON orderaction.order_id=orderorder.id
				WHERE orderorder.budget_id='$budid' AND
				orderorder.currency='$currency' AND orderaction.action='5';");
			while($closed_order=mysql_fetch_array($d_closed,MYSQL_ASSOC)){
				$closed_ordid=$closed_order['id'];
				$supid=$closed_order['supplier_id'];
				$d_special=mysql_query("SELECT specialaction FROM
									ordersupplier WHERE id='$supid';");
				$specialaction=mysql_result($d_special,0);
				if($specialaction==0){
					/*Add invoices*/
					$d_sum=mysql_query("SELECT SUM(debitcost) FROM
						orderinvoice JOIN orderaction ON
						orderinvoice.id=orderaction.invoice_id WHERE
						orderinvoice.credit='0' AND orderaction.action='3' AND
						orderaction.order_id='$closed_ordid';");
					$sum+=$rate*mysql_result($d_sum,0);
					/*Subtract credit notes*/
					$d_sum=mysql_query("SELECT SUM(debitcost) FROM
						orderinvoice JOIN orderaction ON
						orderinvoice.id=orderaction.invoice_id WHERE
						orderinvoice.credit='1' AND orderaction.action='3' AND
						orderaction.order_id='$closed_ordid';");
					$sum-=$rate*mysql_result($d_sum,0);
					}
				else{
					/*Add any specials which don't have an invoice*/
					$d_sum=mysql_query("SELECT SUM(unitcost*quantity) FROM
						ordermaterial WHERE ordermaterial.order_id='$closed_ordid';");
					$sum+=$rate*mysql_result($d_sum,0);
					}
				}
			}
		/* Iterate over any sub-budgets */
		$subsum=0;
		$d_bud=mysql_query("SELECT costlimit FROM orderbudget WHERE overbudget_id='$budid';");
		while($bud=mysql_fetch_array($d_bud,MYSQL_ASSOC)){
			$subsum+=$bud['costlimit'];
			}
		$costremain=round($costlimit-$sum-$subsum,0);
		}
	else{
		$costremain='';
		}
	return $costremain;
	}



/**
 *
 * @param integer $invid
 * @return array
 */
function fetchInvoice($invid='-1'){
	$d_inv=mysql_query("SELECT * FROM orderinvoice WHERE id='$invid';");
	$inv=mysql_fetch_array($d_inv,MYSQL_ASSOC);
	$Invoice=array();
	$Invoice['id_db']=$invid;
   	$Invoice['Reference']=array('label' => 'reference', 
								'inputtype'=> 'required',
								'table_db' => 'orderinvoice', 
								'field_db' => 'reference',
								'type_db' => 'varchar(40)', 
								'value' => ''.$inv['reference']
								);
   	$Invoice['Date']=array('label' => 'date', 
						 //'inputtype'=> 'required',
						 'table_db' => 'orderinvoice', 
						 'field_db' => 'invoicedate',
						 'type_db' => 'date', 
						 'default_value' => date('Y-m-d'),
						 'value' => ''.$inv['invoicedate']
						 );
   	$Invoice['DeliveryCost']=array('label' => 'deliverycost', 
								   //'inputtype'=> 'required',
								   'table_db' => 'orderinvoice', 
								   'field_db' => 'deliverycost',
								   'type_db' => 'decimal', 
								   'value' => ''.$inv['deliverycost']
								   );
   	$Invoice['TaxCost']=array('label' => 'tax', 
							  //'inputtype'=> 'required',
							  'table_db' => 'orderinvoice', 
							  'field_db' => 'taxcost',
							  'type_db' => 'decimal', 
							  'value' => ''.$inv['taxcost']
							  );
   	$Invoice['DiscountCost']=array('label' => 'discount', 
								   //'inputtype'=> 'required',
								   'table_db' => 'orderinvoice', 
								   'field_db' => 'discountcost',
								   'type_db' => 'decimal', 
								   'value' => ''.$inv['deliverycost']
						   );
   	$Invoice['TotalCost']=array('label' => 'total', 
								//'inputtype'=> 'required',
								'table_db' => 'orderinvoice', 
								'field_db' => 'totalcost',
								'type_db' => 'decimal', 
								'value' => ''.$inv['totalcost']
								);
   	$Invoice['DebitCost']=array('label' => 'totalinvoiced', 
								'inputtype'=> 'required',
								'table_db' => 'orderinvoice', 
								'field_db' => 'debitcost',
								'type_db' => 'decimal', 
								'value' => ''.$inv['debitcost']
								);
   	$Invoice['Currency']=array('label' => 'currency', 
							   'inputtype'=> 'required',
							   'table_db' => 'orderinvoice', 
							   'field_db' => 'currency',
							   'type_db' => 'enum', 
							   'default_value' => '0',
							   'value' => ''.$inv['currency']
							   );
   	$Invoice['Credit']=array('label' => 'credit', 
							 'inputtype'=> 'required',
							 'table_db' => 'orderinvoice', 
							 'field_db' => 'credit',
							 'type_db' => 'enum',
							 'default_value' => '0',
							 'value' => ''.$inv['credit']
							 );
	return $Invoice;
	}


/**
 * Returns the current budgetary year.
 * Named after the calendar year that the current budget year ends. 
 * 2008 is 2007/08 when displayed and understood by the user.
 * Display can be done using display_curriculumyear()
 *
 * @param string $yearcode
 * @return string
 */
function get_budgetyear($yearcode=''){

	global $CFG;

	if($yearcode==''){
		if(isset($CFG->budget_endmonth)){$endmonth=$CFG->budget_endmonth;}
		else{$endmonth='5';}
		$thismonth=date('m');
		$thisyear=date('Y');
		if($thismonth>$endmonth){$thisyear++;}
		}
	else{
		$yearcodes=getEnumArray('budgetyearcode');
		$thisyear=array_search($yearcode,$yearcodes);
		}
	return $thisyear;
	}

/**
 * Where the year works on the same principle as curriculum year ie. 2008 is
 * 2007/08. The codes could be amended to be as cryptic as needed.
 *
 * @param string $budgetyear
 * @return string
 */
function get_budgetyearcode($budgetyear){
	$yearcodes=getEnumArray('budgetyearcode');
	if(array_key_exists($budgetyear,$yearcodes)){$yearcode=$yearcodes[$budgetyear];}
	else{$yearcode=-1;}
	return $yearcode;
	}

/**
 * Return perms for the active user for given budid.
 * x is special and needed to authorise and configure.
 * w is needed to edit and lodge and purchase etc.
 *
 * @param integer $budid
 * @return array
 */
function get_budget_perms($budid){
	$perms['r']=0;
	$perms['w']=0;
	$perms['x']=0;
	$uid=$_SESSION['uid'];
	$aperm=get_admin_perm('b',$uid);
  	$d_p=mysql_query("SELECT r,w,x,e FROM perms JOIN orderbudget 
						ON perms.gid=orderbudget.gid WHERE
						orderbudget.id='$budid' AND perms.uid='$uid';");
	if(mysql_num_rows($d_p)>0){
		$perms=mysql_fetch_array($d_p,MYSQL_ASSOC);
		}
	/* The following means administrators can do anything and all office staff 
	 * can do everything except for authorise an order and configure
	 * the budget
	 */
	if($_SESSION['role']=='office' or $_SESSION['role']=='admin'){$perms['r']=1;$perms['w']=1;}
	if($aperm==1){$perms['r']=1;$perms['w']=1;$perms['x']=1;}
	return $perms;
	}


/**
 *
 */
function update_budget_perms($uid,$budid,$perms){
	$d_b=mysql_query("SELECT gid FROM orderbudget WHERE id='$budid'");
	$gid=mysql_result($d_b,0);
	update_staff_perms($uid,$gid,$perms);
	}

/**
 *
 * The enum currency array for exchange rates. This defaults to
 * currency '0' is 'euros' and '1' is 'pounds'.  An exchange rate
 * needs to be set in categorydef (currently manually in the db!) for
 * Pounds to Euros once for each budget year code.
 * name=description, type=exc, subtype=1, rating_name=yearcode, comment=rate
 *
 */
function get_budget_currencyrates($budid){

	$rates=array();
	$rates[0]=1;
	$d_b=mysql_query("SELECT subtype AS currency, comment AS rate FROM categorydef 
						JOIN orderbudget ON orderbudget.yearcode=categorydef.rating_name 
						WHERE orderbudget.id='$budid' AND categorydef.type='exc';");
	while($b=mysql_fetch_array($d_b,MYSQL_ASSOC)){
		$rates[$b['currency']]=$b['rate'];
		}
	return $rates; 
	}
?>
