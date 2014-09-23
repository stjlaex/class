<?php
/**                    staff_details_action.php
 */

$action='staff_list.php';
include('scripts/sub_action.php');

if(isset($_POST['seluid']) and $_POST['seluid']!=''){$seluid=$_POST['seluid'];}


if($sub=='Submit' and $seluid!=''){
	$seluid=$_POST['seluid'];
   	$user=array();
   	$user['username']=clean_text($_POST['username']);
   	$user['surname']=clean_text($_POST['surname']);
   	$user['forename']=clean_text($_POST['forename']);
   	$user['title']=$_POST['title'];
   	$user['email']=$_POST['email'];
   	$user['role']=$_POST['role'];
   	$user['senrole']=$_POST['senrole'];
   	$user['medrole']=$_POST['medrole'];
   	$user['firstbookpref']=clean_text($_POST['book']);
   	$user['homephone']=clean_text($_POST['homephone']);
   	$user['mobilephone']=clean_text($_POST['mobilephone']);
   	$user['personalcode']=clean_text($_POST['personalcode']);
   	$user['personalemail']=clean_text($_POST['personalemail']);
   	$user['jobtitle']=clean_text($_POST['jobtitle']);
   	$user['contractdate']=clean_text($_POST['contractdate']);
   	$user['education']=clean_text($_POST['education']);
   	$user['education2']=clean_text($_POST['education2']);
   	$user['dob']=clean_text($_POST['dob']);
   	$user['worklevel']=$_POST['worklevel'];
   	if(isset($_POST['nologin'])){$user['nologin']=$_POST['nologin'];}
	else{$user['nologin']='0';}
	if(isset($_POST['pin1']) and $_POST['pin1']!=''){
		if($_POST['pin1']==$_POST['pin2']){
			/* Update the users' password. */
			$user['userno']=clean_text($_POST['pin1']);
			update_user($user,'yes',$CFG->shortkeyword);
			}
		else{
			$error[]=get_string('mistakematchingpasswords',$book);
			}
		}
	else{
		/* Update the user but not the password. */
		update_user($user,'yes');
		}

	$aperm=get_admin_perm('u',$_SESSION['uid']);

	if($_SESSION['role']=='admin'){
		/* Update special access permissions */
		$agroups=(array)list_admin_groups();
		foreach($agroups as $agroup){
			$agid=$agroup['gid'];
			if(isset($_POST["a$agid"]) and $_POST["a$agid"]==1){
				$newperms=array('r'=>1,'w'=>1,'x'=>1);
				}
			else{
				$newperms=array('r'=>0,'w'=>0,'x'=>0);
				}
			update_staff_perms($seluid,$agid,$newperms);
			}
		}
	if($_SESSION['role']=='admin' or $_SESSION['role']=='office' or $aperm==1){
		/* Update access restrictions for sections. */
		$agroups=(array)list_sections();

		foreach($agroups as $agroup){
			$agid=$agroup['gid'];
			//trigger_error($agid.' : '.$_POST["a$agid"],E_USER_WARNING);
			if(isset($_POST["a$agid"]) and $_POST["a$agid"]==1){
				$newperms=array('r'=>1,'w'=>0,'x'=>0);
				}
			else{
				$newperms=array('r'=>0,'w'=>0,'x'=>0);
				}
			update_staff_perms($seluid,$agid,$newperms);
			}
		}


	if(isset($_POST['addid']) and $_POST['addid']!=''){
		$addid=$_POST['addid'];
		$addressno='0';/*Only doing one address.*/
		$Address=fetchAddress(array('address_id'=>$addid,'addresstype'=>''));
		foreach($Address as $key => $val){
			if(isset($val['value']) & is_array($val) and isset($val['table_db'])){
				$field=$val['field_db'];
				$inname=$field. $addressno;
				if(isset($_POST[$inname])){$inval=clean_text($_POST[$inname]);}
				else{$inval='';}
				if($val['value']!=$inval){
					if($val['table_db']=='address'){
						if($addid=='-1' and $inval!=''){
							mysql_query("INSERT INTO address SET region='';");
							$addid=mysql_insert_id();
							mysql_query("UPDATE users SET address_id='$addid' WHERE uid='$seluid';");
							}
						mysql_query("UPDATE address SET $field='$inval' WHERE id='$addid';");
						}
					}
				}
			}
		}

	$d_ie=mysql_query("SELECT id,name,comment,othertype FROM categorydef WHERE type='inf' AND subtype='staff';");
	while($field=mysql_fetch_array($d_ie,MYSQL_ASSOC)){
		$fieldid=$field['id'];
		if(isset($_POST['extra_'.$fieldid]) and $_POST['extra_'.$fieldid]!=''){$newval=$_POST['extra_'.$fieldid];}else{$newval='';}
		$d_v=mysql_query("SELECT value FROM info_extra WHERE catdef_id='$fieldid' AND user_id='$seluid';");
		if(mysql_num_rows($d_v)>0){mysql_query("UPDATE info_extra SET value='$newval' WHERE user_id='$seluid' AND catdef_id='$fieldid';");}
		else{mysql_query("INSERT INTO info_extra (catdef_id,user_id,value) VALUES ('$fieldid','$seluid','$newval');");}
		}

	include('scripts/results.php');
   	}

include('scripts/redirect.php');	
?>
