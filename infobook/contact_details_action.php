<?php
/**									contact_details_action.php
 *
 */

$action='student_view.php';

$gid=$_POST['contactgid'];
$contactno=$_POST['contactno'];
$Contact=$Student['Contacts'][$contactno];
$sid=$Student['id_db'];

include('scripts/sub_action.php');

/*Check user has permission to edit*/
$yid=$Student['YearGroup']['value'];
$perm=getYearPerm($yid, $respons);
$neededperm='w';
include('scripts/perm_action.php');


if($sub=='Submit'){

	if($contactno!='-1'){
		$Contact=$Student['Contacts'][$contactno];
		$Phones=$Contact['Phones'];
		$Addresses=$Contact['Addresses'];
		}
	else{
		mysql_query("INSERT INTO guardian SET surname='';");
		$gid=mysql_insert_id();
		mysql_query("INSERT INTO gidsid SET guardian_id='$gid', student_id='$sid'");
		$Contact=fetchContact();
		}
	$Phones[]=fetchPhone();
	$Addresses[]=fetchAddress();

	reset($Contact);
	while(list($key,$val)=each($Contact)){
		if(isset($val['value']) & is_array($val)){
			$field=$val['field_db'];
			$inname=$field;
			$inval=clean_text($_POST["$inname"]);
			if($val['value']!=$inval){
				if($val['table_db']=='guardian'){
					mysql_query("UPDATE guardian SET $field='$inval' WHERE id='$gid'");
					}
				if($val['table_db']=='gidsid'){
					mysql_query("UPDATE gidsid SET $field='$inval'
						WHERE guardian_id='$gid' AND student_id=$sid");
					}
				}
			}
		}

	reset($Phones);
	while(list($phoneno,$Phone)=each($Phones)){
		$phoneid=$Phone['id_db'];
		while(list($key,$val)=each($Phone)){
			if(isset($val['value']) & is_array($val)){	
				$field=$val['field_db'];
				$inname=$field.$phoneno;
				$inval=clean_text($_POST["$inname"]);
				if($val['value']!=$inval){
					if($phoneid=='-1' and $inval!=''){
						mysql_query("INSERT INTO phone SET some_id='$gid'");
						$phoneid=mysql_insert_id();
						}
					mysql_query("UPDATE phone SET $field='$inval'
					WHERE some_id='$gid' AND id='$phoneid'");
					}
				}
			}
		}

	reset($Addresses);
	while(list($addressno,$Address)=each($Addresses)){
		$aid=$Address['id_db'];
		reset($Address);
		while(list($key,$val)=each($Address)){
			if(isset($val['value']) & is_array($val)){
				$field=$val['field_db'];
				$inname=$field.$addressno;
				$inval=clean_text($_POST["$inname"]);
				if($val['value']!=$inval){
					if($val['table_db']=='address'){
						if($aid=='-1' and $inval!=''){
							mysql_query("INSERT INTO address SET town=''");
							$aid=mysql_insert_id();
							mysql_query("INSERT INTO gidaid SET
											guardian_id='$gid', address_id='$aid'");
							}
						mysql_query("UPDATE address SET $field='$inval' WHERE id='$aid'");
						}
					
					if($val['table_db']=='gidaid'){
						mysql_query("UPDATE gidaid SET $field='$inval'
						WHERE address_id='$aid' AND guardian_id='$gid'");
						}
					}
				}
			}
		}

	$Student['Conacts']=fetchContacts($sid);
	$_SESSION['Student']=$Student;
	}


elseif($sub=='Delete Checked'){
	if(isset($_POST{'unpids'})){$unpids=$_POST{'unpids'};
		for($c=0;$c<sizeof($unpids);$c++){
			$pid=$unpids[$c];
			mysql_query("DELETE FROM phone WHERE id='$pid' LIMIT 1");
			}
		}

	if(isset($_POST{'unaids'})){$unaids=$_POST{'unaids'};
		for($c=0;$c<sizeof($unaids);$c++){
			$aid=$unaids[$c];
			mysql_query("DELETE FROM address WHERE id='$aid' LIMIT 1");
			mysql_query("DELETE FROM gidaid WHERE address_id='$aid'");
			}
		}

	if(isset($_POST{'ungidaids'})){$ungidaids=$_POST{'ungidaids'};
		for($c=0;$c<sizeof($ungidaids);$c++){
			list($ungid,$unaid)=explode(':',$ungidaids[$c]);
			$d_gidaid=mysql_query("SELECT guardian_id FROM gidaid WHERE address_id='$unaid'");
			if(mysql_num_rows($d_gidaid)==1){mysql_query("DELETE FROM address 
												WHERE id='$unaid' LIMIT 1");}
			mysql_query("DELETE FROM gidaid WHERE 
						address_id='$unaid' AND guardian_id='$ungid'");
			}
		}

   	}

include('scripts/redirect.php');
?>
