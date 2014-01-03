<?php
/**                    new_teacher_action.php
 */

$action='new_teacher.php';

include('scripts/sub_action.php');

$result=array();
if($sub=='Submit'){
	/*Load the teachers' details from a file*/
	$importfile=$_POST['importfile'];
	$result[]=get_string('loadingfile').$importfile;
    include('scripts/file_import_csv.php');
	while(list($index,$d)=each($inrows)){
			$user=array();
			/*
			$user['username']=$d[0];
			$user['title']=$d[3];
			$user['email']=$d[4];
			$user['role']=$d[5];
			$user['personalcode']=$d[6];
			$user['street']=$d[7];
			$user['postcode']=$d[8];
			$user['dob']=$d[11];
			$user['contractdate']=$d[12];
			$user['userno']=$d[13];
			*/


			

   		if($d[0]!=''){
			unset($user);unset($username);

			$username=$d[0];
			$user=get_user($username,'username');
			if($user==-1){
				$user=array('username'=>$username,'role'=>'office','userno'=>'1234','address_id'=>-1);
				trigger_error($username.' '.'PASSWORD!!!!!!!!!!!!',E_USER_WARNING);
				}
			if($user['address_id']>0){$addid=$user['address_id'];}
			else{$addid=-1;}

			$user['surname']=$d[1];
			$user['forename']=$d[2];
			$user['title']=$d[3];
			$user['mobilephone']=$d[4];
			$user['homephone']=$d[5];
			$user['street']=$d[6];
			$user['town']=$d[7];
			$user['country']=$d[8];
			$user['postcode']=$d[9];
			$user['role']=$d[10];
			$user['email']=$d[11];
			$user['userno']=$d[12];
			$result[]=update_user($user,'no',$CFG->shortkeyword);

			$Address=fetchAddress(array('address_id'=>$addid,'addresstype'=>''));
			foreach($Address as $key => $val){
				if(isset($val['value']) & is_array($val) and isset($val['table_db'])){
					$field=$val['field_db'];
					$inname=$field;
					if(isset($user[$inname])){$inval=clean_text($user[$inname]);}
					else{$inval='';}
					if($val['value']!=$inval){
						if($val['table_db']=='address'){
							if($addid=='-1' and $inval!=''){
								mysql_query("INSERT INTO address SET region='';");
								$addid=mysql_insert_id();
								mysql_query("UPDATE users SET address_id='$addid' WHERE username='$username';");
								}
							mysql_query("UPDATE address SET $field='$inval' WHERE id='$addid';");
							}
						}
					}
				}


			}
		}

	}

include('scripts/results.php');
include('scripts/redirect.php');	
?>
