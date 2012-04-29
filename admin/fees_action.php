<?php 
/**				   	   			   fees_action.php
 */

$action='fees.php';
$action_post_vars=array('feeyear');

include('scripts/sub_action.php');

if(isset($_POST['access']) and $_POST['access']=='access'){
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

if($sub=='Next'){
	$feeyear=$_POST['feeyear'];
	$feeyear++;
	}
elseif($sub=='Previous'){
	$feeyear=$_POST['feeyear'];
	$feeyear--;
	}

include('scripts/redirect.php');
?>
