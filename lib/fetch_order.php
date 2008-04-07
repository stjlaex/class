<?php	
/**	   							 fetch_order.php
 *
 */	

	/*
   	$Order['']=array('label' => 'boarder', 
							  'inputtype'=> 'required',
							  'table_db' => 'order', 
							  'field_db' => '',
							  'type_db' => '', 
							  'default_value' => 'N',
							  'value' => ''.$order['']
							  );
	*/

/* Returns the budgetname for that ordid from the database*/
function get_budgetname($ordid){
	if($ordid!=' ' and $ordid!=''){
		$d_b=mysql_query("SELECT name FROM orderbudget WHERE id='$ordid'");
		$name=mysql_result($d_b,0);
		}
	else{
		$name='BLANK';
		}
	return $name;
	}


function list_user_budgets($tid,$budgetyear='%'){
	$uid=get_uid($tid);
	$budgets=array();
	if($budgetyear!='%'){
		$yearcode=get_budgetyearcode($budgetyear);
		}
	$d_b=mysql_query("SELECT id, code, yearcode, name, costlimit,
					perms.r AS r, perms.w AS w, perms.x AS x FROM orderbudget JOIN perms ON
					perms.gid=orderbudget.gid WHERE perms.uid='$uid' AND
			   		(orderbudget.yearcode='%' OR orderbudget.yearcode LIKE '$yearcode') ORDER
			   		BY orderbudget.yearcode, orderbudget.section_id, orderbudget.name");
	while($budget=mysql_fetch_array($d_b,MYSQL_ASSOC)){
		$budgets[]=$budget;
		}
	return $budgets;
	}

function list_budget_orders($budid){
	$orders=array();
	$d_order=mysql_query("SELECT id FROM orderorder WHERE budget_id='$budid' ORDER
			   		BY entrydate DESC");
	while($order=mysql_fetch_array($d_order,MYSQL_ASSOC)){
		$orders[]=$order;
		}
	return $orders;
	}

function list_orders($ordernumber='%',$orderstatus='%'){
	$orders=array();
	if($ordernumber!='%' and $ordernumber!=''){
		list($budcode,$staffcode,$yearcode,$ordid)=split('-',$ordernumber);
		}
	if(!isset($budcode) or $budcode==''){$budcode='%';}
	if(!isset($staffcode) or $staffcode==''){$staffcode='%';}
	if(!isset($yearcode) or $yearcode==''){$yearcode='%';}
	if(!isset($ordid) or $ordid==''){$ordid='%';}
	if(!isset($supid) or $supid==''){$supid='%';}
	$d_order=mysql_query("SELECT orderorder.id FROM orderorder JOIN orderbudget ON 
				orderbudget.id=orderorder.budget_id 
				WHERE orderbudget.code LIKE '$budcode' AND 
				orderbudget.yearcode LIKE '$yearcode' AND 
				orderorder.teacher_id LIKE '$staffcode' AND 
				orderorder.id LIKE '$ordid' AND 
				orderorder.supplier_id LIKE '$supid' 
				ORDER BY entrydate DESC;");
	while($order=mysql_fetch_array($d_order,MYSQL_ASSOC)){
		if($orderstatus!='%' and $orderstatus!=''){
			$ordid=$order['id'];
			$d_a=mysql_query("SELECT action FROM orderaction
				 WHERE order_id='$ordid' ORDER BY entryn DESC LIMIT 1;");
			if(mysql_num_rows($d_a)>0){
				$action=mysql_result($d_a,0);
				if($action==$orderstatus){$orders[]=$order;}
				trigger_error($orderstatus.': '.$action,E_USER_WARNING);
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
 * Given and ordid then returns the reference number.
 * This function defines the formula for generating these order references. It could be
 * anything. But here the budget code and the yearcode are prepended
 * to the unique order id. 
 *
 */
function get_order_reference($ordid){
	$d_b=mysql_query("SELECT orderbudget.code,
		orderbudget.yearcode, orderorder.teacher_id FROM orderbudget JOIN orderorder
					ON orderorder.budget_id=orderbudget.id WHERE orderorder.id='$ordid'");

	$ref=mysql_fetch_array($d_b,MYSQL_ASSOC);
	/* This gives an order reference of format: budget-teacher-year-id */
	$reference=strtoupper($ref['code'].'-'.$ref['teacher_id'].'-'.$ref['yearcode'].'-'.$ordid);
	return $reference;
	}

function fetchOrder($ordid='-1'){
	$Order=array();
	$d_o=mysql_query("SELECT budget_id, supplier_id, entrydate,
						ordertype, currency, teacher_id 
						FROM orderorder WHERE id='$ordid'");
	$order=mysql_fetch_array($d_o,MYSQL_ASSOC);
	$Order=array();
	$Order['id_db']=$ordid;
   	$Order['Date']=array('label' => 'date', 
						 'inputtype'=> 'required',
						 'table_db' => 'orderorder', 
						 'field_db' => 'entrydate',
						 'type_db' => 'date', 
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
		$Actions['Action'][]=$Action;
		}
	$Order['Actions']=(array)$Actions;
	$Order['Status']=array('label' => 'status',
						   'value' => ''.$status
						   );
	return $Order;
	}


function fetchMaterials($ordid){
	$Materials=array();
	$Materials['Material']=array();
	$d_m=mysql_query("SELECT entryn, quantity, unitcost, detail,
							refno, materialtype
					FROM ordermaterial WHERE order_id='$ordid' ORDER BY entryn;");
	while($mat=mysql_fetch_array($d_m,MYSQL_ASSOC)){
		$Material=(array)fetchMaterial($mat);
		$Materials['Material'][]=$Material;
		}
	return $Materials;
	}

function fetchMaterial($mat=array()){
	$Material=array();
	$Material['id_db']=$mat['entryn'];
   	$Material['Type']=array('label' => 'type',
							//'inputtype'=> 'required',
							'table_db' => 'ordermaterial', 
							'field_db' => 'materialtype',
							'type_db' => 'enum', 
							'default_value' => '0',
							'value' => ''.$mat['materialtype']
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
							  'type_db' => 'text', 
							  'value' => ''.$mat['detail']
							  );
	return $Material;
	}

function fetchBudget($budid='-1'){
	$d_bud=mysql_query("SELECT * FROM orderbudget WHERE id='$budid'");
	$bud=mysql_fetch_array($d_bud,MYSQL_ASSOC);
	$Budget=array();
	$Budget['id_db']=$budid;
	$Budget['gid_db']=$bud['gid'];
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
						  //'table_db' => 'orderbudget', 
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
						   'inputtype'=> 'required',
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

function fetchSupplier($supid=-1){
	if($supid==''){$supid=-1;}
	$d_sup=mysql_query("SELECT * FROM ordersupplier WHERE id='$supid'");
	$sup=mysql_fetch_array($d_sup,MYSQL_ASSOC);
	$Supplier=array();
	$Supplier['id_db']=$supid;
   	$Supplier['Name']=array('label' => 'name', 
							//'inputtype'=> 'required',
							'table_db' => 'ordersupplier', 
							'field_db' => 'name',
							'type_db' => 'varchar(160)', 
							'value' => ''.$sup['name']
							);
	/* TODO: use address table
	$Supplier['Street']=array('label' => 'street',
						   'table_db' => 'ordersupplier', 
						   'field_db' => 'street',
						   'type_db' => 'varchar(160)', 
						   'value' => ''.$sup['street']);
	$Supplier['Neighbourhood']=array('label' => 'neighbourhood',
									'table_db' => 'ordersupplier', 
									'field_db' => 'neighbourhood',
									'type_db' => 'varchar(160)', 
									'value' => ''.$sup['neighbourhood']);
	$Supplier['Town']=array('label' => 'town/city', 
						   'table_db' => 'ordersupplier', 
						   'field_db' => 'region',
						   'type_db' => 'varchar(160)', 
						   'value' => ''.$sup['region']);
	$Supplier['Country']=array('label' => 'country', 
							 'table_db' => 'ordersupplier', 
							 'field_db' => 'country',
							 'type_db' => 'enum',
							  'value_display' => 
							get_string(displayEnum($sup['country'], 'country'),'infobook'), 
							 'value' => ''.$sup['country']);
	$Supplier['Postcode']=array('label' => 'postcode',
							   'table_db' => 'ordersupplier', 
							   'field_db' => 'postcode',
							   'type_db' => 'varchar(8)', 
							   'value' => ''.$sup['postcode']);
	$Supplier['Phonenumber']=array('label' => 'phonenumber',
							   'table_db' => 'ordersupplier', 
							   'field_db' => 'phonenumber1',
							   'type_db' => 'varchar(22)', 
							   'value' => ''.$sup['phonenumber1']);
	$Supplier['Faxnumber']=array('label' => 'faxnumber',
							   'table_db' => 'ordersupplier', 
							   'field_db' => 'phonenumber2',
							   'type_db' => 'varchar(22)', 
							   'value' => ''.$sup['phonenumber2']);
*/
	return $Supplier;
	}

function get_budget_projected($budid=-1){
	$d_bud=mysql_query("SELECT costlimit FROM orderbudget WHERE id='$budid'");
	if(mysql_num_rows($d_bud)>0){
		$costlimit=mysql_result($d_bud,0);
		$d_mat=mysql_query("SELECT SUM(unitcost*quantity) FROM
				ordermaterial JOIN orderorder ON
				orderorder.id=ordermaterial.order_id WHERE
				orderorder.budget_id='$budid'");
		$sum=mysql_result($d_mat,0);
		$costremain=$costlimit-$sum;
		}
	else{
		$costremain='';
		}
	return $costremain;
	}

function get_budget_current($budid=-1){
	$d_bud=mysql_query("SELECT costlimit FROM orderbudget WHERE id='$budid'");
	if(mysql_num_rows($d_bud)>0){
		$costlimit=mysql_result($d_bud,0);
		mysql_query("CREATE TEMPORARY TABLE inv (SELECT invoice_id FROM
				orderaction JOIN orderorder ON
				orderorder.id=orderaction.order_id WHERE
				orderorder.budget_id='$budid' AND orderaction.action='2');");
		$d_sum=mysql_query("SELECT SUM(totalcost) FROM
				orderinvoice JOIN inv ON orderinvoice.id=inv.invoice_id;");
		$sum=mysql_result($d_sum,0);
		$costremain=$costlimit-$sum;
		}
	else{
		$costremain='';
		}
	return $costremain;
	}


function fetchInvoice($invid='-1'){
	$d_inv=mysql_query("SELECT * FROM orderinvoice WHERE id='$invid'");
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
   	$Invoice['DeliveryCost']=array('label' => 'deliverycost', 
						   'inputtype'=> 'required',
						   'table_db' => 'orderinvoice', 
						   'field_db' => 'deliverycost',
						   'type_db' => 'decimal', 
						   'value' => ''.$inv['deliverycost']
						   );
   	$Invoice['TaxCost']=array('label' => 'tax', 
						   'inputtype'=> 'required',
						   'table_db' => 'orderinvoice', 
						   'field_db' => 'taxcost',
						   'type_db' => 'decimal', 
						   'value' => ''.$inv['taxcost']
						   );
   	$Invoice['DiscountCost']=array('label' => 'discount', 
						   'inputtype'=> 'required',
						   'table_db' => 'orderinvoice', 
						   'field_db' => 'discountcost',
						   'type_db' => 'decimal', 
						   'value' => ''.$inv['deliverycost']
						   );
   	$Invoice['TotalCost']=array('label' => 'total', 
						   'inputtype'=> 'required',
						   'table_db' => 'orderinvoice', 
						   'field_db' => 'totalcost',
						   'type_db' => 'decimal', 
						   'value' => ''.$inv['totalcost']
						   );
	/*
   	$Invoice['Currency']=array('label' => 'currency', 
							  'inputtype'=> 'required',
							  'table_db' => 'orderinvoice', 
							  'field_db' => 'currency',
							  'type_db' => 'enum', 
							  'value' => ''.$bud['currency']
							  );
	*/
	return $Invoice;
	}

/* Returns the current budgetary year.
 * Named after the calendar year that the current budget year ends. 
 * 2008 is 2007/08 when displayed and understood by the user.
 * Display can be done using display_curriculumyear()
 */
function get_budgetyear(){
	$endmonth='3';/*TODO: should be in school.php*/
	$thismonth=date('m');
	$thisyear=date('Y');
	if($thismonth>$endmonth){$thisyear++;}
	return $thisyear;
	}

/* Where the year works on the same principle as curriculum year ie. 2008 is */
/* 2007/08. The codes could be amended to whatever is needed.*/
function get_budgetyearcode($budgetyear){
	$yearcodes=array('2007' => '07', 
					 '2008' => '08', 
					 '2009' => '09', 
					 '2010' => '10', 
					 '2011' => '11', 
					 );
	if(array_key_exists($budgetyear,$yearcodes)){$yearcode=$yearcodes[$budgetyear];}
	else{$yearcode=-1;}
	return $yearcode;
	}

/**
 * Return perms for the active user for given budid.
 * x is special and needed to authorise and order.
 * w is needed to edit and lodge and purchase etc.
 */
function get_budget_perms($budid){
	$perms['r']=0;
	$perms['w']=0;
	$perms['x']=0;
	$uid=$_SESSION['uid'];
  	$d_p=mysql_query("SELECT r,w,x,e FROM perms JOIN orderbudget 
						ON perms.gid=orderbudget.gid WHERE
						orderbudget.id='$budid' AND perms.uid='$uid';");
	if(mysql_num_rows($d_p)>0){
		$perms=mysql_fetch_array($d_p,MYSQL_ASSOC);
		}
	/* This means administrators can do anything and all office staff 
	 *  can do everything except for authorise an order
	 */
	if($_SESSION['role']=='admin'){$perms['r']=1;$perms['w']=1;$perms['x']=1;}
	if($_SESSION['role']=='office'){$perms['r']=1;$perms['w']=1;$perms['x']=0;}
	return $perms;
	}
?>
