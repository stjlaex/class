<?php
/**
 *							scrips/fees_access.php
 *
 */

if(isset($access) AND $access=='access'){
	$access=clean_text($_POST['accessfees']);
	$d_a=mysql_query("SELECT AES_DECRYPT(bankname,'$access') FROM fees_account 
							WHERE id='1' AND guardian_id='0';");
	if(mysql_num_rows($d_a)>0 and !empty($_POST['accesstest'])){
		$accesstest=mysql_result($d_a,0);
		if($_POST['accesstest']==$accesstest){
			$_SESSION['accessfees']=$access;
			}
		}
	}
?>