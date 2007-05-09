<?php
/**			  					new_contact_action.php
 *
 */

$action='new_contact.php';
$action_post_vars=array('pregid','sid');

if(isset($_POST['pregid']) and $_POST['pregid']!=''){
	/*don't need to do anything else*/
	$sid=$_POST['sid'];
	$pregid=$_POST['pregid'];
	}
elseif(isset($_POST['sid']) and $_POST['sid']!=''){
	$action='new_student.php';
	$cancel='new_student.php';
	$sid=$_POST['sid'];
	if(isset($_POST['gid'])){$gid=$_POST['gid'];}
	}

include('scripts/sub_action.php');

if($sub=='Submit'){

	if(isset($gid) and $gid!='-1' and $gid!=''){
		$Contact=fetchContact(array('guardian_id'=>'-1','student_id'=>'-1'));
		}
	else{
		if(isset($sid)){
			$Contact=fetchContact(array('guardian_id'=>'-1','student_id'=>'-1'));
			}
		else{
			$Contact=fetchContact(array('guardian_id'=>'-1'));
			}
		mysql_query("INSERT INTO guardian SET surname=''");
		$gid=mysql_insert_id();
		}

	if(isset($sid)){
		mysql_query("INSERT INTO gidsid SET
				guardian_id='$gid', student_id='$sid'");
		}
	reset($Contact);
	while(list($key,$val)=each($Contact)){
		if(isset($val['value']) and is_array($val) and isset($val['table_db'])){
			$field=$val['field_db'];
			$inname=$field;
			$inval=clean_text($_POST[$inname]);
			if($val['table_db']=='guardian'){
				mysql_query("UPDATE guardian SET $field='$inval' WHERE id='$gid'");
				}
			elseif($val['table_db']=='gidsid'){
				mysql_query("UPDATE gidsid SET $field='$inval'
						WHERE guardian_id='$gid' AND student_id='$sid'");
				}
			}
		}

	if(isset($_POST['addresstype']) and $_POST['addresstype']!=''){
		$atype=$_POST['addresstype'];
		$Address=fetchAddress(array('guardian_id'=>'-1'));
		mysql_query("INSERT INTO address SET country=''");
		$aid=mysql_insert_id();
		mysql_query("INSERT INTO gidaid SET guardian_id='$gid',
					address_id='$aid', addresstype='$atype'");
		reset($Address);
		while(list($key,$val)=each($Address)){
			if(isset($val['value']) and is_array($val) and isset($val['table_db'])){
				$field=$val['field_db'];
				$inname=$field;
				$inval=clean_text($_POST[$inname]);
				if($val['table_db']=='address'){
					mysql_query("UPDATE address SET $field='$inval'	WHERE id='$aid'");
					}
				elseif($val['table_db']=='gidaid'){
					mysql_query("UPDATE gidaid SET $field='$inval'
								   WHERE guardian_id='$gid' AND address_id='$aid'");
					}
				}
			}


		}

	//$result[]=get_string('newcontactadded'.$sid,$book);
	}

include('scripts/redirect.php');
?>
