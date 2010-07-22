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
function get_busname($ordid){
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
 *
 * @param integer $budid
 * @return array
 *
 */
function list_buses($direction='%',$day='%'){
	$buses=array();
	$d_b=mysql_query("SELECT * FROM transport_bus WHERE direction IS LIKE '$direction' AND day IS LIKE '$day' 
			ORDER BY name DESC");
	while($bus=mysql_fetch_array($d_b,MYSQL_ASSOC)){
		$buses[]=$bus;
		}
	return $buses;
	}

/**
 *
 * @param integer $budid
 * @return array
 *
 */
function list_busnames(){
	$buses=array();
	$d_b=mysql_query("SELECT DISTINCT name FROM transport_bus ORDER BY name DESC");
	while($bus=mysql_fetch_array($d_b,MYSQL_ASSOC)){
		$buses[]=$bus;
		}
	return $buses;
	}


/**
 *
 * @param integer $ordid
 * @return array
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
 */





/**
 *
 * @param integer $invid
 * @return array
 */
function fetchBus($invid='-1'){
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
 * Where the year works on the same principle as curriculum year ie. 2008 is
 * 2007/08. The codes could be amended to be as cryptic as needed.
 *
 * @param string $budgetyear
 * @return string
function get_budgetyearcode($budgetyear){
	$yearcodes=getEnumArray('budgetyearcode');
	if(array_key_exists($budgetyear,$yearcodes)){$yearcode=$yearcodes[$budgetyear];}
	else{$yearcode=-1;}
	return $yearcode;
	}
 */
?>