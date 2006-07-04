<?php
/**									contact_details_action.php
 *
 */

$action='student_view.php';

$gid=$_POST{'contactgid'};
$contactno=$_POST{'contactno'};
$Contact=$Student['Contacts'][$contactno];

$sid=$Student['id_db'];

include('scripts/sub_action.php');

if($sub=='Submit'){
	/*Check user has permission to edit*/
	$yid=$Student['NCyearActual']['id_db'];
	$perm=getYearPerm($yid, $respons);
	$neededperm='w';
	include('scripts/perm_action.php');

	$in=0;	
	while(list($key,$val)=each($Contact)){
		if(isset($val['value']) & is_array($val)){
			$field=$val['field_db'];
			$inname=$field.$in;
			$inval=clean_text($_POST{"$inname"});
			if($val['value']!=$inval){
//				the value has changed, update database
				$result[]=$val['label'].' : '.$inval;

				if($val['table_db']=='guardian'){
					if(mysql_query("UPDATE guardian SET $field='$inval' WHERE id='$gid'")){
					$Student['Contacts'][$contactno][$key]['value']=$inval;	
					}
					else{$error[]=mysql_error();}
					}
				if($val['table_db']=='gidsid'){
					if(mysql_query("UPDATE gidsid SET $field='$inval'
					WHERE guardian_id='$gid' AND student_id=$sid")){
					$Student['Contacts'][$contactno][$key]['value']=$inval;	
					}
					else{$error[]=mysql_error();}
					}
				}
			$in++;		
			}
		}

	$Addresses=$Contact['Addresses'];
	while(list($addressno,$Address)=each($Addresses)){
		$aid=$Address{'id_db'};
		while(list($key,$val)=each($Address)){
		if(isset($val['value']) & is_array($val)){

			$field=$val['field_db'];
			$inname=$field.$addressno.$in;
			$inval=clean_text($_POST{"$inname"});
			if($val['value']!=$inval){
//				the value has changed, update database
				$result[]=$val['label'].' : '.$inval;

				if($val['table_db']=='address'){
					if(mysql_query("UPDATE address SET $field='$inval' WHERE id='$aid'")){
					$Student['Contacts'][$contactno]['Addresses'][$addressno][$key]['value']=$inval;	
					}
					else{$error[]=mysql_error();}
					}

				if($val['table_db']=='gidaid'){
					if(mysql_query("UPDATE gidaid SET $field='$inval'
					WHERE address_id='$aid' AND guardian_id='$gid'")){
					$Student['Contacts'][$contactno]['Addresses'][$addressno][$key]['value']=$inval;	
					}
					else{$error[]=mysql_error();}
					}
				}
			$in++;	
			}
			}
		}


	$Phones=$Contact['Phones'];
	while(list($phoneno,$Phone)=each($Phones)){
		$phoneid=$Phone['id_db'];
		while(list($key,$val)=each($Phone)){
		if(isset($val['value']) & is_array($val)){

			$field=$val['field_db'];
			$inname=$field.$phoneno.$in;
			$inval=clean_text($_POST{"$inname"});
			if($val['value']!=$inval){
//				the value has changed, update database
				$result[]=$val['label'].' : '.$inval;

				if($val['table_db']=='phone'){
					if(mysql_query("UPDATE phone SET $field='$inval'
					WHERE some_id='$gid' AND id='$phoneid'")){
					$Student['Contacts'][$contactno]['Phones'][$phoneno][$key]['value']=$inval;	
					}
					else{$error[]=mysql_error();}
					}
				}
			$in++;	
			}
			}
		}

	$_SESSION{'Student'}=$Student;
	}

elseif($sub=='New Address'){
   	mysql_query("INSERT INTO address SET town=''");
   	$new_aid=mysql_insert_id();
   	mysql_query("INSERT INTO gidaid SET guardian_id='$gid', address_id='$new_aid'");
	}

elseif($sub=='New Phone'){
   	mysql_query("INSERT INTO phone SET some_id='$gid'");
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
			print $aid;
			mysql_query("DELETE FROM address WHERE id='$aid' LIMIT 1");
			mysql_query("DELETE FROM gidaid WHERE address_id='$aid'");
			print mysql_error();
			}
		}

	if(isset($_POST{'ungidaids'})){$ungidaids=$_POST{'ungidaids'};
		for($c=0;$c<sizeof($ungidaids);$c++){
			list($ungid,$unaid)=explode(':',$ungidaids[$c]);
			$d=mysql_query("SELECT guardian_id FROM gidaid WHERE address_id='$unaid'");
			if(mysql_num_rows($d)==1){mysql_query("DELETE FROM address 
												WHERE id='$unaid' LIMIT 1");}
			mysql_query("DELETE FROM gidaid WHERE 
						address_id='$unaid' AND guardian_id='$ungid'");
			}
		}

   	}

include('scripts/results.php');
include('scripts/redirect.php');
?>
