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
 * Can return an account either for a given guardian_id (the default) or for
 * a given account id (set idname=id). account_id=1 is special and not a valid value.
 * 
 * @return array
 */
function fetchAccount($fetchid=-1,$idname='guardian_id'){

	$Account=array();
	$accid=-1;
	$gid=-1;

	if(!empty($_SESSION['accessfees'])){
		$access=$_SESSION['accessfees'];
		$d_a=mysql_query("SELECT id, valid, guardian_id,
					AES_DECRYPT(bankname,'$access') AS bankname, 
					AES_DECRYPT(accountname,'$access') AS accountname, 
					AES_DECRYPT(bankbranch,'$access') AS bankbranch, 
					AES_DECRYPT(bankcode,'$access') AS bankcode,
					AES_DECRYPT(bankcountry,'$access') AS bankcountry, 
					AES_DECRYPT(bankcontrol,'$access') AS bankcontrol, 
					AES_DECRYPT(banknumber,'$access') AS banknumber, 
					AES_DECRYPT(iban,'$access') AS iban,
					AES_DECRYPT(bic,'$access') AS bic
					FROM fees_account WHERE $idname='$fetchid' AND id!='1';");
		$a=mysql_fetch_array($d_a,MYSQL_ASSOC);
		if(mysql_numrows($d_a)>0){
			$accid=$a['id'];
			$gid=$a['guardian_id'];
			}
		elseif($idname='guardian_id' and $fetchid>0){
			mysql_query("INSERT INTO fees_account SET guardian_id='$fetchid', valid='0';");
			$accid=mysql_insert_id();
			$gid=$fetchid;
			$a=array('bankname'=>'','accountname'=>'','bankcountry'=>'','bankcode'=>'','bankbranch'=>'','bankcontrol'=>'','banknumber'=>'','iban'=>'','bic'=>'');
			}

		if($a['accountname']=='' and $gid!=-1){
			/* Want plain ascii text for account name. 
			$d_g=mysql_query("SELECT CAST(CONCAT(surname,', ',forename) AS CHAR CHARACTER SET ASCII) FROM guardian WHERE id='$gid';");
			*/
			$d_g=mysql_query("SELECT CAST(CONCAT(surname,', ',forename) AS CHAR CHARACTER SET UTF8) FROM guardian WHERE id='$gid';");
			$a['accountname']=mysql_result($d_g,0);
			}

		}
	else{
		$a=array('bankname'=>'','accountname'=>'','bankcountry'=>'','bankcode'=>'','bankbranch'=>'','bankcontrol'=>'','banknumber'=>'','iban'=>'','bic'=>'');
		}

	$Account['id_db']=$accid;
	$Account['guardian_id_db']=$gid;
	$Account['BankName']=array('label' => 'bankname', 
							   //'inputtype'=> 'required',
							   'table_db' => 'fees_account', 
							   'field_db' => 'bankname',
							   'type_db' => 'varchar(120)', 
							   'value' => ''.$a['bankname']
							   );
	$Account['AccountName']=array('label' => 'name', 
								  //'inputtype'=> 'required',
							   'table_db' => 'fees_account', 
							   'field_db' => 'accountname',
							   'type_db' => 'varchar(120)', 
							   'value' => ''.$a['accountname']
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
	$Account['Iban']=array('label' => 'iban', 
							 //'inputtype'=> 'required',
							 'table_db' => 'fees_account', 
							 'field_db' => 'iban',
							 'type_db' => 'varchar(35)', 
							 'value' => ''.$a['iban']
							 );
	$Account['Bic']=array('label' => 'bic', 
							 //'inputtype'=> 'required',
							 'table_db' => 'fees_account', 
							 'field_db' => 'bic',
							 'type_db' => 'varchar(12)', 
							 'value' => ''.$a['bic']
							 );
	return $Account;
	}


/**
 *
 * 
 * @return array
 */
function fetchFeesInvoice($invoice=array('id'=>'-1')){

	$invid=$invoice['id'];
	$Invoice=array();
	if($invid!='-1' and !isset($invoice['reference'])){
		$d_i=mysql_query("SELECT i.series, i.reference, i.account_id, i.remittance_id, r.issuedate, r.duedate FROM fees_invoice AS i  
							JOIN fees_remittance AS r ON r.id=i.remittance_id  
							 WHERE i.id='$invid';");
		if(mysql_numrows($d_i)>0){
			$invoice=mysql_fetch_array($d_i,MYSQL_ASSOC);
			}
		else{
			$invid=-1;
			}
		}
	if($invid=='-1'){
		$invoice=array('id'=>'-1','reference'=>'','duedate'=>'','issuedate'=>'','account_id'=>'','remittance_id'=>'');
		$Address=array();
		$Student=array();
		$Account=array();
		}
	else{
		$d_c=mysql_query("SELECT SUM(c.amount) AS totalamount, c.paymenttype, c.student_id 
							FROM fees_charge AS c WHERE c.invoice_id='$invid';");
		$c=mysql_fetch_array($d_c,MYSQL_ASSOC);
		$sid=$c['student_id'];
		$Student=(array)fetchStudent_short($sid);
		$charges=(array)list_invoice_charges($invid);
		$Account=fetchAccount($invoice['account_id'],'id');
		$gid=$Account['guardian_id_db'];
		$d_gidaid=mysql_query("SELECT * FROM gidaid WHERE guardian_id='$gid' ORDER BY priority;");
		$gidaid=mysql_fetch_array($d_gidaid,MYSQL_ASSOC);
		$Address=(array)fetchAddress($gidaid);
		}

	$Invoice['id_db']=$invid;
	$Invoice['student_id_db']=$sid;
	$Invoice['account_id_db']=$invoice['account_id'];
	$Invoice['TotalAmount']=array('label' => 'amount', 
								  'value' => ''.$c['totalamount']
								  );
	$Invoice['PaymentType']=array('label' => 'amount', 
								  'value' => ''.$c['paymenttype']
								  );
	$Invoice['PaymentDate']=array('label' => 'date', 
								  'value' => ''.$invoice['duedate']
								  );
	$Invoice['IssueDate']=array('label' => 'date', 
								'value' => ''.$invoice['issuedate']
								);
	$Invoice['Reference']=array('label' => 'reference', 
								'value' => ''.$invoice['reference']
								);
	$Invoice['StudentName']=array('label' => 'student', 
								  'value' => ''.$Student['DisplayFullSurname']['value']
								  );
	$Invoice['StudentTutorGroup']=array('label' => 'formgroup', 
											   'value' => ''.$Student['TutorGroup']['value']
											   );
	$Invoice['AccountName']=array('label' => 'name', 
								  'value' => ''.$Account['AccountName']['value']
								  );
	$Invoice['Address']=$Address;
	$Invoice['Charges']=array();
	$Invoice['Charges']['Charge']=array();
	foreach($charges as $charge){
		$Charge=array();
		$Charge['Detail']=array('label'=>'tarif','value'=>''.$charge['name']);
		$Charge['Amount']=array('label'=>'amount','value'=>''.$charge['amount']);
		$Invoice['Charges']['Charge'][]=$Charge;
		}

	return $Invoice;
	}

/**
 *
 * 
 * @return array
 */
function fetchConcept($conid=-1){
	if($conid==''){$conid=-1;}
	$d_con=mysql_query("SELECT id, name, inactive, community_type FROM fees_concept WHERE id='$conid';");
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
   	$Concept['Type']=array('label' => 'type', 
						   //'inputtype'=> 'required',
						   'table_db' => 'fees_concept', 
						   'field_db' => 'community_type',
						   'type_db' => 'enum', 
						   'value' => ''.$con['community_type']
						   );
	$Concept['Tarif']=array();
	$d_t=mysql_query("SELECT id, name, amount FROM fees_tarif WHERE concept_id='$conid' ORDER BY name ASC;");
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
						 'type_db' => 'decimal', 
						 'value' => ''.$tarif['amount']
						 );
	return $Tarif;
	}



/**
 *
 *
 *
 */
function get_tarif($tarid){

	$d_t=mysql_query("SELECT id, name, amount, concept_id FROM fees_tarif WHERE id='$tarid';");
	$t=mysql_fetch_array($d_t);

	return $t;
	}


/**
 *
 *
 *
 */
function fetchRemittance($remid=-1){

	if($remid==''){$remid=-1;}
	$d_c=mysql_query("SELECT id, name, concepts, yeargroups, enrolstatus, year, duedate, issuedate, account_id 
								FROM fees_remittance WHERE id='$remid' ORDER BY issuedate DESC LIMIT 1;");
	$c=mysql_fetch_array($d_c,MYSQL_ASSOC);
	$paymenttypes=getEnumArray('paymenttype');

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
								   'inputtype'=> 'required',
								   'field_db' => 'issuedate',
								   'table_db' => 'fees_remittance', 
								   'value' => ''.$c['issuedate']);
	$Remittance['PaymentDate']=array('label' => 'payment',
									 'type_db' => 'date',
									 'inputtype'=> 'required',
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
	$Remittance['TotalAmounts']=array();

	$conids=(array)explode(':::',$c['concepts']);
	foreach($conids as $conid){
		$Concept=fetchConcept($conid);
		$concept_total=0;$concept_paid=0;$concept_notpaid=0;$concept_pending=0;

		foreach($paymenttypes as $paytype => $payname){

			if(!isset($$payname)){$$payname=0;}//this is the total for this paymenttype

			if($Concept['Type']['value']==''){
				/* charges pending (not yet invoiced or exported) with payment=0 */
				$d_a=mysql_query("SELECT SUM(c.amount) FROM fees_charge AS c JOIN fees_tarif AS t ON t.id=c.tarif_id 
							WHERE c.remittance_id='$remid' AND c.payment='0' AND c.paymenttype='$paytype' AND t.concept_id='$conid';");
				$amount_pending=mysql_result($d_a,0);
				/* charges paid with payment=1 */
				$d_a=mysql_query("SELECT SUM(c.amount) FROM fees_charge AS c JOIN fees_tarif AS t ON t.id=c.tarif_id 
							WHERE c.remittance_id='$remid' AND c.payment='1' AND c.paymenttype='$paytype' AND t.concept_id='$conid';");
				$amount_paid=mysql_result($d_a,0);
				/* charges not paid with payment=2 */
				$d_a=mysql_query("SELECT SUM(c.amount) FROM fees_charge AS c JOIN fees_tarif AS t ON t.id=c.tarif_id 
							WHERE c.remittance_id='$remid' AND c.payment='2' AND c.paymenttype='$paytype' AND t.concept_id='$conid';");
				$amount_notpaid=mysql_result($d_a,0);
				}
			else{
				/* charges linked to a community */
				$comtype=strtolower($Concept['Type']['value']);
				/* charges pending (not yet invoiced or exported) with payment=0*/
				$d_a=mysql_query("SELECT SUM(c.amount) FROM fees_charge AS c JOIN community AS com ON com.id=c.community_id
							WHERE c.remittance_id='$remid' AND c.payment='0' AND c.paymenttype='$paytype' AND com.type='$comtype';");
				$amount_pending=mysql_result($d_a,0);
				/* charges paid with payment=1*/
				$d_a=mysql_query("SELECT SUM(c.amount) FROM fees_charge AS c JOIN community AS com ON com.id=c.community_id
							WHERE c.remittance_id='$remid' AND c.payment='1' AND c.paymenttype='$paytype' AND com.type='$comtype';");
				$amount_paid=mysql_result($d_a,0);
				/* charges not paid with payment=2*/
				$d_a=mysql_query("SELECT SUM(c.amount) FROM fees_charge AS c JOIN community AS com ON com.id=c.community_id
							WHERE c.remittance_id='$remid' AND c.payment='2' AND c.paymenttype='$paytype' AND com.type='$comtype';");
				$amount_notpaid=mysql_result($d_a,0);
				}

			$concept_pending+=$amount_pending;
			$concept_paid+=$amount_paid;
			$concept_notpaid+=$amount_notpaid;

			$$payname+=$amount_pending + $amount_paid + $amount_notpaid;
			}

		$concept_total=$concept_pending + $concept_paid + $concept_notpaid;
		$Concept['AmountPending']=array('label' => 'pending', 
										'value' => ''.$concept_pending
										);
		$Concept['AmountPaid']=array('label' => 'paid', 
									 'value' => ''.$concept_paid
									 );
		$Concept['AmountNotPaid']=array('label' => 'notpaid', 
										'value' => ''.$concept_notpaid
										);
		$Concept['TotalAmount']=array('label' => 'total', 
									  'value' => ''.$concept_total
									  );
		$Remittance['Concepts'][]=$Concept;
		}

	foreach($paymenttypes as $paytype => $payname){
		$Remittance['TotalAmounts'][$payname]=array('label' => $payname,
													'paymenttype' => $paytype, 
													'value' => ''.$$payname
													);
		}

	$yids=(array)explode(':::',$c['yeargroups']);
	foreach($yids as $yid){
		$Remittance['YearGroups'][]=array('id_db' => $yid);
		}
   	$Remittance['EnrolmentStatus']=array('label' => 'enrolstatus', 
										 'table_db' => 'fees_remittance', 
										 'inputtype'=> 'required',
										 'field_db' => 'enrolstatus', 
										 'type_db' => 'enum', 
										 'value' => ''.$c['enrolstatus']
										 );

	return $Remittance;
	}


/**
 *
 *
 *
 */
function list_concepts($status='0'){

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
function get_concept($conid){

	$d_c=mysql_query("SELECT id, name, inactive, community_type FROM fees_concept WHERE id='$conid';");
	$c=mysql_fetch_array($d_c);

	return $c;
	}


/**
 *
 *
 *
 */
function get_invoice($invid){

	$d_i=mysql_query("SELECT id, series, reference, account_id, remittance_id FROM fees_invoice 
						WHERE id='$invid';");
	if(mysql_numrows($d_i)>0){
		$invoice=mysql_fetch_array($d_i,MYSQL_ASSOC);
		}
	else{
		$invoice=array();
		}

	return $invoice;
	}


/**
 * Delete a single fee from the fees_applied table.
 *
 * @param integer feeid
 * @return logical 1 on success
 *
 */
function delete_fee($feeid){

	$done=0;
	if($feeid!=''){
		mysql_query("DELETE FROM fees_applied WHERE id='$feeid' LIMIT 1;");
		$done=mysql_affected_rows();
		}

	return $done;
	}


/**
 *
 *
 *
 */
function get_charge($charid){

	$d_c=mysql_query("SELECT id, student_id, note, quantity, budget_id, community_id, paymenttype, 
						payment, paymentdate, amount, remittance_id, tarif_id, invoice_id 
						FROM fees_charge WHERE id='$charid';");
	$c=mysql_fetch_array($d_c);

	return $c;
	}

/**
 *
 *
 *
 */
function get_remittance($remid){

	$d_r=mysql_query("SELECT id, name, concepts, yeargroups, enrolstatus, duedate, issuedate, year, account_id
						FROM fees_remittance WHERE id='$remid';");
	$r=mysql_fetch_array($d_r);

	return $r;
	}


/**
 *
 * Updates the payment state of a charge and dates the change. 
 * Links to an invoice if the invoice id is passed.
 * The payment states are 0=pending, 1=paid and 2=not paid
 *
 *
 */
function set_charge_payment($charid,$payment,$invid='',$date=''){

	if($charid!='' and ($payment=='0' or $payment=='1' or $payment=='2')){

		/* If payment pending then date should be blank. */
		if($payment=='0'){$date=='0000-00-00';}
		elseif($date==''){$date=date('Y-m-d');}

		/* If no invoice set then careful not to overwrite. */
		if($invid!='' and $invid!='0'){
			$invoiceset="invoice_id='$invid', ";
			}
		else{
			$invoiceset='';
			}

		mysql_query("UPDATE fees_charge SET $invoiceset paymentdate='$date', payment='$payment' 
							WHERE id='$charid';");

		$done=mysql_affected_rows();
		}

	return $done;
	}


/**
 *
 *
 *
 */
function list_accounts($gid=0){

	if(!empty($_SESSION['accessfees'])){
		$access=$_SESSION['accessfees'];
		$d_a=mysql_query("SELECT id, CONCAT(AES_DECRYPT(accountname,'$access'), ' - ', AES_DECRYPT(bankname,'$access')) AS name, AES_DECRYPT(banknumber,'$access') AS banknumber FROM fees_account WHERE guardian_id='$gid' AND id>1 ORDER BY name;");

		$accounts=array();
		while($a=mysql_fetch_array($d_a)){
			$accounts[]=$a;
			}
		}

	return $accounts;
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
function list_remittance_charges($remid,$conid='',$payment=''){

	if($payment=='0' or $payment=='1' or $payment=='2'){
		$payment="AND c.payment='$payment'";
		}
	else{
		$payment='';
		}

	if($conid==''){
		$d_c=mysql_query("SELECT c.id, c.student_id, c.tarif_id, c.quantity, c.amount, c.payment, c.paymenttype, c.invoice_id, c.remittance_id 
							FROM fees_charge AS c JOIN student AS s ON s.id=c.student_id
							WHERE c.remittance_id='$remid' $payment ORDER BY s.surname, s.id;");
		}
	else{
		$d_c=mysql_query("SELECT c.id, c.student_id, c.tarif_id, c.quantity, c.amount, c.payment, c.paymenttype, c.invoice_id, c.remittance_id 
							FROM fees_charge AS c JOIN student AS s ON s.id=c.student_id
							WHERE c.remittance_id='$remid' $payment AND c.tarif_id=ANY(SELECT t.id FROM fees_tarif AS t WHERE t.concept_id='$conid')
							ORDER BY s.surname, s.id;");
		/*
		$d_c=mysql_query("SELECT c.id, c.student_id, c.tarif_id, c.quantity, c.amount, c.payment, c.paymenttype, c.invoice_id 
							FROM fees_charge AS c JOIN fees_tarif AS t ON t.id=c.tarif_id 
							WHERE c.remittance_id='$remid' $payment AND t.concept_id='$conid' ORDER BY c.student_id;");
		*/
		}

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
function list_invoice_charges($invid){

	$d_c=mysql_query("SELECT c.id, c.student_id, c.tarif_id, c.quantity, c.amount, c.payment, c.paymenttype, c.invoice_id, t.name 
							FROM fees_charge AS c JOIN fees_tarif AS t ON c.tarif_id=t.id WHERE c.invoice_id='$invid' ORDER BY c.paymentdate;");
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
function list_remittance_invoices($remid,$paymenttype=''){


	$d_r=mysql_query("SELECT issuedate, duedate FROM fees_remittance WHERE id='$remid';");
	$r=mysql_fetch_array($d_r);

	if($paymenttype!=''){
		$d_i=mysql_query("SELECT DISTINCT i.id, i.series, i.reference, i.account_id, i.remittance_id 
							FROM fees_invoice AS i JOIN fees_charge AS c ON c.invoice_id=i.id  
							WHERE i.remittance_id='$remid' AND c.paymenttype='$paymenttype' ORDER BY i.reference;");
		//							WHERE i.remittance_id='$remid' AND c.paymenttype='$paymenttype' ORDER BY i.reference;");
		}
	else{
		/*
		$d_i=mysql_query("SELECT i.id, i.series, i.reference, i.account_id, i.remittance_id, r.issuedate, r.duedate 
							FROM fees_invoice AS i JOIN fees_remittance AS r ON r.id=i.remittance_id  
							WHERE i.remittance_id='$remid' ORDER BY i.reference;");
		*/
		$d_i=mysql_query("SELECT id, reference, account_id, remittance_id FROM fees_invoice
							WHERE remittance_id='$remid' ORDER BY reference;");
		}


	$invoices=array();
	while($i=mysql_fetch_array($d_i)){
		$i['issuedate']=$r['issuedate'];
		$i['duedate']=$r['duedate'];
		$invoices[]=$i;
		}

	return $invoices;
	}




/**
 *
 *
 *
 */
function list_invoices($refno,$seriesno=''){

	if($seriesno==''){
		$feeyear=get_curriculumyear();
		/* Take last 2 digits of year to be the series number. */
		$seriesno=substr($feeyear,2,2);
		}

	$d_i=mysql_query("SELECT i.id, i.series, i.reference, i.account_id, i.remittance_id, r.issuedate, r.duedate FROM fees_invoice AS i  
							JOIN fees_remittance AS r ON r.id=i.remittance_id  
							 WHERE i.series='$seriesno' AND i.reference LIKE '%$refno' ORDER BY i.reference;");

	$invoices=array();
	while($i=mysql_fetch_array($d_i)){
		$invoices[]=$i;
		}

	return $invoices;
	}





/**
 * Listing actual charges already made. They will be
 * associated with a remittance. Their status may be pending
 * payment (status=P,payment=0), paid (status=1,payment=1) or
 * not paid (status=2,payment=2). The amount is fixed when the
 * remittance was created.
 *
 */
function list_student_charges($sid,$status,$remid=-1){

	if($status=='P'){
		$payment=0;
		}
	else{
		$payment=$status;
		}
	if($remid>0){
		$remittance="AND c.remittance_id='$remid'";
		}
	else{
		$remittance="AND c.remittance_id>'0'";
		}
	$d_c=mysql_query("SELECT c.id, c.student_id, c.tarif_id, c.quantity, c.paymentdate, c.paymenttype, c.payment, 
							c.amount, c.remittance_id, c.invoice_id, t.concept_id FROM fees_charge AS c JOIN fees_tarif AS t ON t.id=c.tarif_id 
							WHERE c.student_id='$sid' AND c.payment='$payment' $remittance ORDER BY c.paymentdate;");

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



/**
 *
 *
 *
 */
function list_student_fees($sid){

		/* Listing the possible fees applied to this student for
		 * future charges... not existing ones.  They are listed from
		 * the fees_applied table. The amount is not fixed and open to
		 * changes to the associated tarif.
		 */
		$d_f=mysql_query("SELECT c.id, c.student_id, c.tarif_id, c.paymenttype, c.note, t.amount, t.concept_id 
						FROM fees_applied AS c JOIN fees_tarif AS t ON t.id=c.tarif_id 
						WHERE c.student_id='$sid' ORDER BY t.concept_id;");
	$fees=array();
	while($f=mysql_fetch_array($d_f)){
		$conid=$f['concept_id'];
		if(!array_key_exists($conid,$fees)){
			$fees[$conid]=array();
			}
		$fees[$conid][]=$f;
		}

	return $fees;
	}


/**
 *
 *
 *
 */
function list_concept_fees($conid){

	$d_f=mysql_query("SELECT a.id, a.student_id, a.tarif_id, t.amount FROM fees_applied AS a 
						JOIN fees_tarif AS t ON t.id=a.tarif_id 
						WHERE t.concept_id='$conid' ORDER BY tarif_id ASC;");
	$fees=array();
	while($f=mysql_fetch_array($d_f)){
		$sid=$f['student_id'];
		/* TODO: only lists the first fee per student at the moment */
		if(!array_key_exists($sid,$fees)){
			$fees[$sid]=array();
			}
		$fees[$sid][]=$f;
		}

	return $fees;
	}


/**
 * Will return a list of id, name pairs for given sid. If one is set
 * then the first guardian will be the set payee.
 *
 * @param integer $sid
 * @return array
 */
function list_student_payees($sid){
	$guardians=array();
	$d_g=mysql_query("SELECT guardian.id AS id, gidsid.relationship, guardian.surname, guardian.forename, gidsid.paymenttype FROM gidsid, guardian WHERE gidsid.guardian_id=guardian.id AND gidsid.student_id='$sid' ORDER BY paymenttype DESC;");
	while($g=mysql_fetch_array($d_g,MYSQL_ASSOC)){
		$gid=$g['id'];
		/* Search for valid bank accounts. */
		$d_a=mysql_query("SELECT COUNT(id) FROM fees_account WHERE guardian_id='$gid' AND valid='1';");
		$g['accountsno']=mysql_result($d_a,0);/* This counts the number of valid bank accounts. */
		$g['name']=get_string(displayEnum($g['relationship'], 'relationship'),'infobook').': '.$g['surname'].', '.$g['forename'];
		/* Add the guardian to the list of payees even if no valid accounts because they could use other methods. */
		$guardians[]=$g;
		}

	return $guardians;
	}


/**
 *
 * Will update the payment status of guardians for given sid. Setting
 * one and only one gidsid entry as the defualt payee and their
 * preferred payment type.
 *
 * @param integer $sid
 * @param integer $gid
 * @param integer $paymenttype
 * @return logical $success
 */
function update_student_payee($sid,$gid,$paymenttype){
	$success=false;
	$guardians=(array)list_student_payees($sid);
	foreach($guardians as $guardian){
		$othergid=$guardian['id'];
		if($othergid==$gid){
			mysql_query("UPDATE gidsid SET paymenttype='$paymenttype' WHERE guardian_id='$gid' AND student_id='$sid';");
			$success=true;
			//trigger_error($gid.' '.$paymenttype,E_USER_WARNING);
			}
		else{
			mysql_query("UPDATE gidsid SET paymenttype='0' WHERE guardian_id='$othergid' AND student_id='$sid';");
			}
		}

	return $success;
	}




/**
 *
 * Will add a new charge for sid and conceptid.
 *
 * @param integer $sid
 * @param integer $conceptid
 * @param integer $tarifid
 * @param integer $paymenttype
 * @return logical $charid
function add_student_charge($sid,$conceptid='',$tarifid='',$paymenttype=''){

	if($tarifid=='' and $conceptid!=''){
		$d_t=mysql_query("SELECT id, amount FROM fees_tarif WHERE concept_id='$conceptid' ORDER BY amount ASC LIMIT 1;");
		$tarifid=mysql_result($d_t,0,1);
		$amount=mysql_result($d_t,0,2);
		}
	else{
		$d_t=mysql_query("SELECT amount FROM fees_tarif WHERE id='$tarifid';");
		$amount=mysql_result($d_t,0);
		}

	if($paymenttype==''){
		$guardians=(array)list_student_payees($sid);
		if(sizeof($guardians)>0 and $guardians[0]['paymenttype']>0){
			$paymenttype=$guardians[0]['paymenttype'];
			}
		else{
			$paymenttype='0';
			}
		}

	if($tarifid!='' and $sid!=''){
		mysql_query("INSERT INTO fees_charge (student_id,tarif_id,paymenttype,amount) VALUES ('$sid','$tarifid','$paymenttype','$amount');");
		$charid=mysql_insert_id();
		}
	else{
		$charid=-1;
		}

	return $charid;
	}
 */



/**
 *
 * Will add a new charge for sid and conceptid.
 *
 * @param integer $accid
 * @param integer $remid
 *
 * @return $reference
 */
function create_invoice($accid,$remid){

	$d_r=mysql_query("SELECT year FROM fees_remittance WHERE id='$remid';");
	$year=mysql_result($d_r,0);
	/* Take last 2 digits of year to be the series number. */
	$seriesno=substr($year,2,2);
	$d_i=mysql_query("SELECT MAX(CAST(reference AS UNSIGNED)) FROM fees_invoice WHERE series='$seriesno';");
	/* Just sequential numbers... */
	$maxno=mysql_result($d_i,0);
	$idno=$maxno + 1;
	$ref=sprintf("%05s",$idno);// number format 00001-99999
	mysql_query("INSERT INTO fees_invoice (series,reference,account_id,remittance_id) VALUES ('$seriesno','$ref','$accid','$remid');");
	$invid=mysql_insert_id();

	return array('id'=>$invid,'series'=>$seriesno,'reference'=>$ref);
	}




/**
 *
 * Will add a new charge for sid and conceptid.
 *
 * @param integer $sid
 * @param integer $conceptid
 * @param integer $tarifid
 * @param integer $paymenttype
 * @return logical $charid
 */
function apply_student_fee($sid,$conceptid,$tarifid='',$paymenttype=''){
	if($tarifid=='' and $conceptid!=''){
		$d_t=mysql_query("SELECT id FROM fees_tarif WHERE concept_id='$conceptid' ORDER BY amount ASC LIMIT 1;");
		$tarifid=mysql_result($d_t,0);
		}

	if($paymenttype==''){
		$guardians=(array)list_student_payees($sid);
		if(sizeof($guardians)>0 and $guardians[0]['paymenttype']>0){
			$paymenttype=$guardians[0]['paymenttype'];
			}
		else{
			$paymenttype='0';
			}
		}

	if($tarifid!='' and $sid!=''){
		mysql_query("INSERT INTO fees_applied (student_id,tarif_id,paymenttype) VALUES ('$sid','$tarifid','$paymenttype');");
		$appid=mysql_insert_id();
		}
	else{
		$appid=-1;
		}

	return $appid;
	}


/**
 *
 * Will add a new charge for sid for the community.
 *
 * @param integer $sid
 * @param integer $comid
 * @param integer $remid
 * @param integer $paymenttype
 * @return logical $charid
 */
function add_student_community_charge($sid,$comid,$remid,$amount,$paymenttype=''){

	if($paymenttype==''){
		$guardians=(array)list_student_payees($sid);
		if(sizeof($guardians)>0 and $guardians[0]['paymenttype']>0){
			$paymenttype=$guardians[0]['paymenttype'];
			}
		else{
			$paymenttype='0';
			}
		}

	if($comid!='' and $sid!=''){
		mysql_query("INSERT INTO fees_charge (student_id, community_id, remittance_id, paymenttype, amount) 
										VALUES ('$sid','$comid','$remid','$paymenttype','$amount');");
		$charid=mysql_insert_id();
		}
	else{
		$charid=-1;
		}

	return $charid;
	}

/**
 * TODO:
 */
function check_account_valid($Account){
	if($Account['BankName']['value']!='' and $Account['Number']['value']!='' and $Account['Control']['value']!='' and $Account['Branch']['value']!='' and $Account['BankCode']['value']!=''){ 
		$valid=1;
		}
	else{
		$valid=0;
		}
	return $valid;
	}

?>
