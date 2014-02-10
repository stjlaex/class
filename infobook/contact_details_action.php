<?php
/**									contact_details_action.php
 */

$action='student_view.php';
$action_post_vars=array('contactno');

if(isset($_POST['access'])){$access=$_POST['access'];}
$gid=$_POST['gid'];
$contactno=$_POST['contactno'];

include('scripts/sub_action.php');

if($sub=='Submit' or $access=='access'){
	$update_flag=false;

	if($contactno>-1){
		if($sid!=''){
			/*Check user has permission to edit*/
			$yid=$Student['YearGroup']['value'];
			$perm=getYearPerm($yid, $respons);
			$neededperm='w';
			include('scripts/perm_action.php');
			}

		/*editing exisiting contact link*/
		$action='student_view.php';
		$Contact=$Student['Contacts'][$contactno];
		$Phones=$Contact['Phones'];
		$Addresses=$Contact['Addresses'];
		}
	elseif($contactno==-1 and $gid==-1){
		/*completely fresh contact being linked to*/
		mysql_query("INSERT INTO guardian SET surname='';");
		$gid=mysql_insert_id();
		mysql_query("INSERT INTO gidsid SET guardian_id='$gid', student_id='$sid';");
		$Contact=fetchContact();
		$update_flag=true;
		}
	elseif($gid>=-1 and $sid!=''){
		/*pre-existing contact being linked to*/
		mysql_query("INSERT INTO gidsid SET guardian_id='$gid', student_id='$sid';");
		$gidsid=array('guardian_id'=>$gid,'student_id'=>$sid,
					  'priority'=>'','mailing'=>'','relationship'=>'');
		$Contact=fetchContact($gidsid);
		$Phones=(array)$Contact['Phones'];
		$Addresses=$Contact['Addresses'];
		$update_flag=true;
		}
	elseif($gid>=-1){
		/*just editing a contact without reference to a sid*/
		$action='contact_details.php';
		$Contact=fetchContact(array('guardian_id'=>$gid));
		$Phones=(array)$Contact['Phones'];
		$Addresses=$Contact['Addresses'];
		$update_flag=true;
		}

	while(sizeof($Phones)<4){$Phones[]=fetchPhone();}
	$Addresses[]=fetchAddress();

	foreach($Contact as $key =>$val){
		if(isset($val['value']) and is_array($val) and isset($val['table_db'])){
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
				$update_flag=true;
				}
			}
		}

	/* Have to do this seperate because it has no table_db field to
	 * keep it out of the main form. 
	 */
	$inval=clean_text($_POST['note']);
	if($Contact['Note']['value']!=$inval){
		mysql_query("UPDATE guardian SET note='$inval' WHERE id='$gid'");
		$update_flag=true;
		}

	foreach($Phones as $phoneno => $Phone){
		$phoneid=$Phone['id_db'];
		while(list($key,$val)=each($Phone)){
			if(is_array($val) and isset($val['value']) and isset($val['table_db'])){	
				$field=$val['field_db'];
				$inname=$field. $phoneno;
				$inval=clean_text($_POST["$inname"]);
				if($val['value']!=$inval){
					if($phoneid=='-1' and $inval!='' and $field!='privatephone'){
						mysql_query("INSERT INTO phone SET some_id='$gid';");
						$phoneid=mysql_insert_id();
						$update_flag=true;
						}
					if($phoneid!='-1'){
						mysql_query("UPDATE phone SET $field='$inval'
							WHERE some_id='$gid' AND id='$phoneid';");
						$update_flag=true;
						}
					}
				}
			}
		}

	foreach($Addresses as $addressno => $Address){
		$aid=$Address['id_db'];
		foreach($Address as $key => $val){
			if(isset($val['value']) & is_array($val) and isset($val['table_db'])){
				$field=$val['field_db'];
				$inname=$field. $addressno;
				if(isset($_POST[$inname])){$inval=clean_text($_POST[$inname]);}
				else{$inval='';}
				if($val['value']!=$inval){
					if($val['table_db']=='address'){
						if($aid=='-1' and $inval!='' and $field!='privateaddress'){
							mysql_query("INSERT INTO address SET region='';");
							$aid=mysql_insert_id();
							mysql_query("INSERT INTO gidaid SET guardian_id='$gid', address_id='$aid';");
							$update_flag=true;
							}
						if($aid!='-1'){
							mysql_query("UPDATE address SET $field='$inval', lat='0', lng='0' WHERE id='$aid';");
							$update_flag=true;
							}
						}
					if($val['table_db']=='gidaid' and $aid!='-1'){
						mysql_query("UPDATE gidaid SET $field='$inval' WHERE address_id='$aid' AND guardian_id='$gid';");
						$update_flag=true;
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
		$update_flag=true;
		}

	}

if($sub=='Previous'){
	if($contactno>1){$contactno=$contactno-1;}else{$contactno=0;}
	$action="contact_details.php";
	}
elseif($sub=='Next'){
	$nogids=sizeof($gids);
	if($contactno<$nogids-1){$contactno=$contactno+1;}else{$contactno=$nogids-1;}
	$action="contact_details.php";
	}

if(isset($access) AND $access=='access'){
	$action='contact_details.php';
	include('scripts/fees_access.php');
	}
elseif(!empty($_SESSION['accessfees']) and $gid!=-1){
	require_once('lib/fetch_fees.php');
	$access=$_SESSION['accessfees'];
	$Account=fetchAccount($gid);
	$accid=$Account['id_db'];
	foreach($Account as $key => $val){
		if(isset($val['value']) & is_array($val) and isset($val['table_db'])){
			$field=$val['field_db'];
			$inname=$field;
			if(isset($_POST[$inname])){$inval=clean_text($_POST[$inname]);}
			else{$inval='';}
			if($val['value']!=$inval and $val['table_db']=='fees_account'){
				if($accid=='-1' and $inval!='' and $gid>0){
					mysql_query("INSERT INTO fees_account SET guardian_id='$gid';");
					$accid=mysql_insert_id();
					}
				mysql_query("UPDATE fees_account SET $field=AES_ENCRYPT('$inval','$access') WHERE id='$accid';");
				$Account[$key]['value']=$inval;
				$update_flag=true;
				}
			}
		}
	if($update_flag){
		$valid=check_account_valid($Account);
		mysql_query("UPDATE fees_account SET valid='$valid' WHERE id='$accid';");
		}
	$action='contact_details.php';
	}


include('scripts/redirect.php');
?>
