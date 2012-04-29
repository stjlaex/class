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
function fetchAccount($gid=-1,$idname='guardian_id'){

	$Account=array();
	$acid=-1;

	if(!empty($_SESSION['accessfees'])){
		$access=$_SESSION['accessfees'];
		$d_a=mysql_query("SELECT id, AES_DECRYPT(bankname,'$access') AS bankname, AES_DECRYPT(accountname,'$access') AS accountname, 
					AES_DECRYPT(bankbranch,'$access') AS bankbranch, 
					AES_DECRYPT(bankcode,'$access') AS bankcode,
					AES_DECRYPT(bankcountry,'$access') AS bankcountry, AES_DECRYPT(bankcontrol,'$access') 
					AS bankcontrol, AES_DECRYPT(banknumber,'$access') AS banknumber  
					FROM fees_account WHERE $idname='$gid' AND id!='1';");
		$a=mysql_fetch_array($d_a,MYSQL_ASSOC);
		if(mysql_numrows($d_a)>0){
			$acid=$a['id'];
			}
		}
	else{
		$a=array('bankname'=>'','accountname'=>'','bankcountry'=>'','bankcode'=>'','bankbranch'=>'','bankcontrol'=>'','banknumber'=>'');
		}

	$Account['id_db']=$acid;
	$Account['BankName']=array('label' => 'bankname', 
							   'inputtype'=> 'required',
							   'table_db' => 'fees_account', 
							   'field_db' => 'bankname',
							   'type_db' => 'varchar(120)', 
							   'value' => ''.$a['bankname']
							   );
	$Account['AccountName']=array('label' => 'name', 
							   'inputtype'=> 'required',
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
							   'inputtype'=> 'required',
							   'table_db' => 'fees_account', 
							   'field_db' => 'bankcode',
							   'type_db' => 'varchar(8)', 
							   'value' => ''.$a['bankcode']
							   );
	$Account['Branch']=array('label' => 'branch', 
							 'inputtype'=> 'required',
							 'table_db' => 'fees_account', 
							 'field_db' => 'bankbranch',
							 'type_db' => 'varchar(8)', 
							 'value' => ''.$a['bankbranch']
							 );
	$Account['Control']=array('label' => 'control', 
							  'inputtype'=> 'required',
							  'table_db' => 'fees_account', 
							  'field_db' => 'bankcontrol',
							  'type_db' => 'varchar(4)', 
							  'value' => ''.$a['bankcontrol']
							  );
	$Account['Number']=array('label' => 'number', 
							 'inputtype'=> 'required',
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
function fetchRemittance($remid=-1){

	if($remid==''){$remid=-1;}
	$d_c=mysql_query("SELECT id, name, concepts, enrolstatus, year, duedate, issuedate, account_id 
								FROM fees_remittance WHERE id='$remid' ORDER BY issuedate DESC LIMIT 1;");
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

	$conids=(array)explode(':::',$c['concepts']);
	foreach($conids as $conid){

		$Concept=fetchConcept($conid);
		if($Concept['Type']['value']==''){
			/* charges pending (not yet invoiced or exported) with payment=0*/
			$d_a=mysql_query("SELECT SUM(c.amount) FROM fees_charge AS c JOIN fees_tarif AS t ON t.id=c.tarif_id 
							WHERE c.remittance_id='$remid' AND c.payment='0' AND t.concept_id='$conid';");
			$amount_pending=mysql_result($d_a,0);
			/* charges paid with payment=1*/
			$d_a=mysql_query("SELECT SUM(c.amount) FROM fees_charge AS c JOIN fees_tarif AS t ON t.id=c.tarif_id 
							WHERE c.remittance_id='$remid' AND c.payment='1' AND t.concept_id='$conid';");
			$amount_paid=mysql_result($d_a,0);
			/* charges not paid with payment=2*/
			$d_a=mysql_query("SELECT SUM(c.amount) FROM fees_charge AS c JOIN fees_tarif AS t ON t.id=c.tarif_id 
							WHERE c.remittance_id='$remid' AND c.payment='2'  AND t.concept_id='$conid';");
			$amount_notpaid=mysql_result($d_a,0);
			}
		else{
			$comtype=strtolower($Concept['Type']['value']);
			/* charges pending (not yet invoiced or exported) with payment=0*/
			$d_a=mysql_query("SELECT SUM(c.amount) FROM fees_charge AS c JOIN community AS com ON com.id=c.community_id
							WHERE c.remittance_id='$remid' AND c.payment='0' AND com.type='$comtype';");
			$amount_pending=mysql_result($d_a,0);
			/* charges paid with payment=1*/
			$d_a=mysql_query("SELECT SUM(c.amount) FROM fees_charge AS c JOIN community AS com ON com.id=c.community_id
							WHERE c.remittance_id='$remid' AND c.payment='1' AND com.type='$comtype';");
			$amount_paid=mysql_result($d_a,0);
			/* charges not paid with payment=2*/
			$d_a=mysql_query("SELECT SUM(c.amount) FROM fees_charge AS c JOIN community AS com ON com.id=c.community_id
							WHERE c.remittance_id='$remid' AND c.payment='2' AND com.type='$comtype';");
			$amount_notpaid=mysql_result($d_a,0);
			}


		$total=$amount_pending + $amount_paid + $amount_notpaid;
		$Concept['AmountPending']=array('label' => 'pending', 
										'value' => ''.$amount_pending
										);
		$Concept['AmountPaid']=array('label' => 'paid', 
									 'value' => ''.$amount_paid
									 );
		$Concept['AmountNotPaid']=array('label' => 'notpaid', 
										'value' => ''.$amount_notpaid
										);
		$Concept['TotalAmount']=array('label' => 'total', 
									  'value' => ''.$total
									  );
		$Remittance['Concepts'][]=$Concept;
		}

   	$Remittance['EnrolmentStatus']=array('label' => 'enrolstatus', 
										 'table_db' => 'fees_remittance', 
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
function get_charge($charid){

	$d_c=mysql_query("SELECT * FROM fees_charge WHERE id='$charid';");
	$c=mysql_fetch_array($d_c);

	return $c;
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
function list_remittance_charges($remid,$conid=''){
	if($conid==''){
		$d_c=mysql_query("SELECT c.id, c.student_id, c.tarif_id, c.quantity, c.amount, c.paymenttype 
							FROM fees_charge AS c  
							WHERE c.remittance_id='$remid' ORDER BY c.student_id;");
		}
	else{
		$d_c=mysql_query("SELECT c.id, c.student_id, c.tarif_id, c.quantity, c.amount, c.paymenttype 
							FROM fees_charge AS c JOIN fees_tarif AS t ON t.id=c.tarif_id 
							WHERE c.remittance_id='$remid' AND t.concept_id='$conid' ORDER BY c.student_id;");
		}

	$charges=array();
	while($c=mysql_fetch_array($d_c)){
		$charges[]=$c;
		}

	return $charges;
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
							c.amount, t.concept_id FROM fees_charge AS c JOIN fees_tarif AS t ON t.id=c.tarif_id 
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
		$d_f=mysql_query("SELECT c.id, c.student_id, c.tarif_id, c.paymenttype, t.amount, t.concept_id 
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
		$d_a=mysql_query("SELECT COUNT(id) FROM fees_account WHERE guardian_id='$gid';");
		$g['accountsno']=mysql_result($d_a,0);
		$g['name']=displayEnum($g['relationship'], 'relationship').': '.$g['surname'];
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
?>