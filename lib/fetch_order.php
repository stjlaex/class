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

function list_user_budgets($tid,$yearcode='%'){
	$uid=get_uid($tid);
	$budgets=array();
	$d_b=mysql_query("SELECT id, code, yearcode, name, costlimit,
					perms.r AS r, perms.w AS w, perms.x AS x FROM orderbudget JOIN perms ON
					perms.gid=orderbudget.gid WHERE perms.uid='$uid' AND
			   		(orderbudget.yearcode='%' OR orderbudget.yearcode LIKE '$yearcode') ORDER
			   		BY orderbudget.yearcode, orderbudget.name");
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

function list_orders($ordernumber='%'){
	$orders=array();
	list($budcode,$yearcode,$ordid)=split('-',$ordernumber);
	if($budcode==''){$budcode='%';}
	if($yearcode==''){$yearcode='%';}
	if($ordid==''){$ordid='%';}
	$supid='%';
	$d_order=mysql_query("SELECT orderorder.id FROM orderorder JOIN orderbudget ON 
				orderbudget.id=orderorder.budget_id 
				WHERE orderbudget.code LIKE '$budcode' AND 
				orderbudget.yearcode LIKE '$yearcode' AND 
				orderorder.id LIKE '$ordid' AND 
				orderorder.supplier_id LIKE '$supid' 
				ORDER BY entrydate DESC");
	//trigger_error($ordernumber.': '.$budcode.' - '.$yearcode.' - '.$ordid,E_USER_WARNING);
	while($order=mysql_fetch_array($d_order,MYSQL_ASSOC)){
		$orders[]=$order;
		}
	return $orders;
	}


function get_order_reference($ordid,$budid=''){
	if($budid==''){
		$d_b=mysql_query("SELECT code, yearcode FROM orderbudget JOIN orderorder
					ON orderorder.budget_id=orderbudget.id WHERE orderorder.id='$ordid'");
		}
	else{
		$d_b=mysql_query("SELECT code, yearcode FROM orderbudget WHERE id='$budid'");
		}

	$ref=mysql_fetch_array($d_b,MYSQL_ASSOC);
	$reference=$ref['code'].'-'.$ref['yearcode'].'-'.$ordid;
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
   	$Order['OrderType']=array('label' => 'ordertype',
							  'inputtype'=> 'required',
							  'table_db' => 'orderorder', 
							  'field_db' => 'ordertype',
							  'type_db' => 'enum', 
							  'default_value' => '0',
							  'value' => ''.$order['ordertype']
							  );
   	$Order['Currency']=array('label' => 'currency', 
							 'inputtype'=> 'required',
							 'table_db' => 'orderorder', 
							 'field_db' => 'currency',
							 'type_db' => 'enum', 
							 'default_value' => '0',
							 'value' => ''.$order['currency']
							 );
   	$Order['Date']=array('label' => 'date', 
						 'inputtype'=> 'required',
						 'table_db' => 'orderorder', 
						 'field_db' => 'entrydate',
						 'type_db' => 'date', 
						 'value' => ''.$order['entrydate']
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
							  'value' => ''.get_order_reference($ordid,$order['budget_id'])
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
	$d_m=mysql_query("SELECT entryn, quantity, unitcost, detail, refno
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
   	$Budget['Code']=array('label' => 'code', 
						  'inputtype'=> 'required',
						  'table_db' => 'orderbudget', 
						  'field_db' => 'code',
						  'type_db' => 'varchar(8)', 
						  'value' => ''.$bud['code']
						  );
   	$Budget['Name']=array('label' => 'name', 
						  'inputtype'=> 'required',
						  //'table_db' => 'orderbudget', 
						  'field_db' => 'name',
						  'type_db' => 'varchar(160)', 
						  'value' => ''.$bud['name']
						  );
   	$Budget['YearCode']=array('label' => 'year', 
							  'inputtype'=> 'required',
							  'table_db' => 'orderbudget', 
							  'field_db' => 'yearcode',
							  'type_db' => 'enum', 
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

function fetchSupplier($supid='-1'){
	$d_sup=mysql_query("SELECT * FROM ordersupplier WHERE id='$supid'");
	$sup=mysql_fetch_array($d_sup,MYSQL_ASSOC);
	$Supplier=array();
	$Supplier['id_db']=$supid;
   	$Supplier['Name']=array('label' => 'name', 
							'inputtype'=> 'required',
							'table_db' => 'ordersupplier', 
							'field_db' => 'name',
							'type_db' => 'varchar(160)', 
							'value' => ''.$sup['name']
							);
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

?>
