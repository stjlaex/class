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
 * 
 * @return array
 */
function fetchAccount($gid=-1){

	$Account=array();
	$acid=-1;

	if(!empty($_SESSION['accessfees'])){
		$access=$_SESSION['accessfees'];

		$d_a=mysql_query("SELECT id, AES_DECRYPT(bankname,'$access') AS bankname, AES_DECRYPT(bankbranch,'$access') AS bankbranch, 
					AES_DECRYPT(bankcode,'$access') AS bankcode,
					AES_DECRYPT(bankcountry,'$access') AS bankcountry, AES_DECRYPT(bankcontrol,'$access') 
					AS bankcontrol, AES_DECRYPT(banknumber,'$access') AS banknumber  
					FROM fees_account WHERE guardian_id='$gid';");
		$a=mysql_fetch_array($d_a,MYSQL_ASSOC);
		if(mysql_numrows($d_a)>0){
			$acid=$a['id'];
			}
		}
	trigger_error($acid,E_USER_WARNING);
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




?>