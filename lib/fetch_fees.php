<?php	
/**								 lib/fetch_fees.php
 *
 *	@package	ClaSS
 *	@author		stj@laex.org
 *	@copyright	S T Johnson 2011
 *	@version	
 *	@since		
 *
 */


/**
 *
 * Can return and account either for a given guardian_id (the default) or for
 * a given account id (set idname=id). id=1 is special and not a valid value.
 * 
 * @return array
 */
function fetchAccount($gid=-1,$idname='guardian_id'){

	$Account=array();
	$acid=-1;

	if(!empty($_SESSION['accessfees'])){
		$access=$_SESSION['accessfees'];

		$d_a=mysql_query("SELECT id, AES_DECRYPT(bankname,'$access') AS bankname, AES_DECRYPT(bankbranch,'$access') AS bankbranch, 
					AES_DECRYPT(bankcode,'$access') AS bankcode,
					AES_DECRYPT(bankcountry,'$access') AS bankcountry, AES_DECRYPT(bankcontrol,'$access') 
					AS bankcontrol, AES_DECRYPT(banknumber,'$access') AS banknumber  
					FROM fees_account WHERE $idname='$gid' AND id!='1';");
		$a=mysql_fetch_array($d_a,MYSQL_ASSOC);
		if(mysql_numrows($d_a)>0){
			$acid=$a['id'];
			}
		}

	$Account['id_db']=$acid;
	$Account['BankName']=array('label' => 'name', 
							   //'inputtype'=> 'required',
							   'table_db' => 'fees_account', 
							   'field_db' => 'bankname',
							   'type_db' => 'varchar(60)', 
							   'value' => ''.$a['bankname']
							   );
	$Account['Country']=array('label' => 'country', 
							  //'inputtype'=> 'required',
							  'table_db' => 'fees_account', 
							  'field_db' => 'bankcountry',
							  'type_db' => 'char(4)', 
							  'value' => ''.$a['bankcountry']
							  );
	$Account['BankCode']=array('label' => 'bankcode', 
							   //'inputtype'=> 'required',
							   'table_db' => 'fees_account', 
							   'field_db' => 'bankcode',
							   'type_db' => 'varchar(8)', 
							   'value' => ''.$a['bankcode']
							   );
	$Account['Branch']=array('label' => 'branch', 
							 //'inputtype'=> 'required',
							 'table_db' => 'fees_account', 
							 'field_db' => 'bankbranch',
							 'type_db' => 'varchar(8)', 
							 'value' => ''.$a['bankbranch']
							 );
	$Account['Control']=array('label' => 'control', 
							  //'inputtype'=> 'required',
							  'table_db' => 'fees_account', 
							  'field_db' => 'bankcontrol',
							  'type_db' => 'varchar(4)', 
							  'value' => ''.$a['bankcontrol']
							  );
	$Account['Number']=array('label' => 'number', 
							 //'inputtype'=> 'required',
							 'table_db' => 'fees_account', 
							 'field_db' => 'banknumber',
							 'type_db' => 'varchar(20)', 
							 'value' => ''.$a['banknumber']
							 );

	return $Account;
	}


/**
 *
 * 
 * @return array
 */
function fetchConcept($conid=-1){
	if($conid==''){$conid=-1;}
	$d_con=mysql_query("SELECT id, name, inactive FROM fees_concept WHERE id='$conid';");
	$con=mysql_fetch_array($d_con,MYSQL_ASSOC);

	$Concept=array();
	$Concept['id_db']=$conid;
   	$Concept['Name']=array('label' => 'name', 
						   'inputtype'=> 'required',
						   'table_db' => 'fees_concept', 
						   'field_db' => 'name',
						   'type_db' => 'varchar(240)', 
						   'value' => ''.$con['name']
							);
   	$Concept['Inactive']=array('label' => 'inactive', 
							   //'inputtype'=> 'required',
							   'table_db' => 'fees_concept', 
							   'field_db' => 'inactive',
							   'type_db' => 'enum', 
							   'value' => ''.$con['inactive']
							   );

	$Concept['Tarifs']=array();
	$d_t=mysql_query("SELECT id, name, amount FROM fees_tarif WHERE concept_id='$conid' ORDER BY amount;");
	while($tarif=mysql_fetch_array($d_t)){
		$Concept['Tarifs'][]=fetchTarif($tarif);
		}

	return $Concept;
	}


/**
 *
 */
function fetchTarif($tarif=array('id'=>-1)){
	$Tarif=array();
	$Tarif['id_db']=$tarif['id'];
	$Tarif['Name']=array('label' => 'name', 
						 'inputtype'=> 'required',
						 'table_db' => 'fees_tarif', 
						 'field_db' => 'name',
						 'type_db' => 'varchar(240)', 
						 'value' => ''.$tarif['name']
						 );
	$Tarif['Amount']=array('label' => 'amount', 
						 'inputtype'=> 'required',
						 'table_db' => 'fees_tarif', 
						 'field_db' => 'amount',
						 'type_db' => 'smallint', 
						 'value' => ''.$tarif['amount']
						 );
	return $Tarif;
	}


/**
 *
 *
 *
 */
function fetchRemittance($remid=-1){

	if($remid==''){$remid=-1;}
	$d_c=mysql_query("SELECT id, name, concepts, year, duedate, issuedate FROM fees_remittance WHERE id='$remid' ORDER BY date DESC LIMIT 1;");
	$c=mysql_fetch_array($d_c,MYSQL_ASSOC);

	$Remittance=array();
	$Remittance['id_db']=$remid;
   	$Remittance['Name']=array('label' => 'name', 
							  'inputtype'=> 'required',
							  'table_db' => 'fees_remittance', 
							  'field_db' => 'name',
							  'type_db' => 'varchar(240)', 
							  'value' => ''.$c['name']
							  );
	$Remittance['IssueDate']=array('label' => 'issue', 
								   'type_db' => 'date', 
								   'field_db' => 'issuedate',
								   'table_db' => 'fees_remittance', 
								   'value' => ''.$c['issuedate']);
	$Remittance['PaymentDate']=array('label' => 'due', 
									 'type_db' => 'date', 
									 'field_db' => 'duedate',
									 'table_db' => 'fees_remittance', 
									 'value' => ''.$c['duedate']);
   	$Remittance['Year']=array('label' => 'year', 
							  //'table_db' => 'fees_remittance', 
							  'field_db' => 'year',
							  'type_db' => 'year', 
							  'value' => ''.$c['year']
							  );
   	$Remittance['Account']=fetchAccount($c['account_id'],'id');

	$Remittance['Concepts']=array();

	$conids=(array)explode(':::',$c['concepts']);
	foreach($conids as $conid){
		$d_a=mysql_query("SELECT SUM(t.amount) FROM fees_charge AS c JOIN fees_tarif AS t ON t.id=c.tarif_id 
							WHERE c.remittance_id='$remid' AND t.concept_id='$conid';");
		$total=mysql_result($d_a,0);
		if($total>0){
			$Concept=fetchConcept($conid);
			$Concept['TotalAmount']=array('label' => 'total', 
										  'value' => ''.$total
										  );
			$Remittance['Concepts'][]=$Concept;
			}
		}


	return $Remittance;
	}


/**
 *
 *
 *
 */
function list_concepts($status=0){

	if($feeyear==''){$feeyear=get_curriculumyear();}

	$d_c=mysql_query("SELECT id, name  FROM fees_concept WHERE inactive='$status' ORDER BY name;");

	$concepts=array();
	while($c=mysql_fetch_array($d_c)){
		$concepts[]=$c;
		}

	return $concepts;
	}


/**
 *
 *
 *
 */
function list_remittances($feeyear=''){

	if($feeyear==''){$feeyear=get_curriculumyear();}

	$d_c=mysql_query("SELECT id, name, year  FROM fees_remittance WHERE year='$feeyear' ORDER BY issuedate DESC;");

	$remittances=array();
	while($c=mysql_fetch_array($d_c)){
		$remittances[]=$c;
		}

	return $remittances;
	}


/**
 *
 *
 *
 */
function list_remittance_charges($remid,$conid=''){
	$d_c=mysql_query("SELECT c.id, c.student_id, c.tarif_id, c.quantity, t.amount 
							FROM fees_charge AS c JOIN fees_tarif AS t ON t.id=c.tarif_id 
							WHERE c.remittance_id='$remid' AND t.concept_id='$conid' ORDER BY c.student_id;");
	$charges=array();
	while($c=mysql_fetch_array($d_c)){
		$charges[]=$c;
		}

	return $charges;
	}


/**
 *
 *
 *
 */
function list_charges($conid,$status=0){

	$d_c=mysql_query("SELECT c.id, c.student_id, c.tarif_id, c.quantity, t.amount FROM fees_charge AS c 
						JOIN fees_tarif AS t ON t.id=c.tarif_id 
						WHERE t.concept_id='$conid' AND c.payment='$status' ORDER BY paymentdate;");
	$charges=array();
	while($c=mysql_fetch_array($d_c)){
		$sid=$c['student_id'];
		/* TODO: only one charge per student at the moment
		if(!array_key_exists($sid,$charges)){
			$charges[$sid]=array();
			}
		*/
		$charges[$sid]=$c;
		}

	return $charges;
	}


/**
 *
 *
 *
 */
function list_student_charges($sid,$status=0){

	$d_c=mysql_query("SELECT c.id, c.student_id, c.tarif_id, c.quantity, c.paymentdate, c.paymenttype, c.payment, t.amount, t.concept_id 
						FROM fees_charge AS c JOIN fees_tarif AS t ON t.id=c.tarif_id 
						WHERE c.student_id='$sid' AND c.payment='$status' ORDER BY paymentdate;");
	$charges=array();
	while($c=mysql_fetch_array($d_c)){
		$conid=$c['concept_id'];
		if(!array_key_exists($conid,$charges)){
			$charges[$conid]=array();
			}
		$charges[$conid][]=$c;
		}

	return $charges;
	}

?>