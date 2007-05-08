<?php
/**									contact_details_action.php
 *
 */

$action='student_view.php';
$action_post_vars=array('contactno');

$gid=$_POST['gid'];
$contactno=$_POST['contactno'];

include('scripts/sub_action.php');

if($sub=='Submit'){

	if($contactno>'-1'){
		/*Check user has permission to edit*/
		$yid=$Student['YearGroup']['value'];
		$perm=getYearPerm($yid, $respons);
		$neededperm='w';
		include('scripts/perm_action.php');

		/*editing exisiting contact link*/
		$action='student_view.php';
		$Contact=$Student['Contacts'][$contactno];
		$Phones=$Contact['Phones'];
		$Addresses=$Contact['Addresses'];
		}
	elseif($gid!='' and $sid!=''){
		/*pre-existing contact being linked to*/
		mysql_query("INSERT INTO gidsid SET guardian_id='$gid', student_id='$sid'");
		$Contact=fetchContact(array('guardian_id'=>$gid,'student_id'=>$sid));
		$Phones=$Contact['Phones'];
		$Addresses=$Contact['Addresses'];
		}
	elseif($gid!=''){
		/*just editing a contact within reference to a sid*/
		$action='contact_details.php';
		$Contact=fetchContact(array('guardian_id'=>$gid));
		$Phones=$Contact['Phones'];
		$Addresses=$Contact['Addresses'];
		}
	else{
		/*completely fresh contact being linked to*/
		mysql_query("INSERT INTO guardian SET surname=''");
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
				elseif($val['table_db']=='gidsid'){
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
				if(isset($_POST[$inname])){$inval=clean_text($_POST[$inname]);}
				else{$inval='';}
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
	}


elseif($sub=='Unlink'){

	if($gid!='' and $sid!=''){
		mysql_query("DELETE FROM gidsid WHERE 
						guardian_id='$gid' AND student_id='$sid'");

		}

	}

include('scripts/redirect.php');
?>
