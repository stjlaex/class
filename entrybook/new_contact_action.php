<?php
/**			  					new_contact_action.php
 */

$action='new_student.php';
include('scripts/sub_action.php');

if($sub=='Submit'){

	$Contact=fetchContact(array('guardian_id'=>'-1'));
	mysql_query("INSERT INTO guardian SET surname=''");
	$gid=mysql_insert_id();

	mysql_query("INSERT INTO info SET student_id='$sid'");
	reset($Contact);
	while(list($key,$val)=each($Contact)){
		if(isset($val['value']) and is_array($val) and isset($val['table_db'])){
			$field=$val['field_db'];
			$inname=$field;
			$inval=clean_text($_POST[$inname]);
				if($val['table_db']=='contact'){
					mysql_query("UPDATE contact SET $field='$inval'	WHERE id='$gid'");
					}
				elseif($val['table_db']=='info'){
					mysql_query("UPDATE info SET $field='$inval' WHERE student_id='$gid'");
					}
				}
			}
		}

	if($yid!=''){
		$comtype='year';
		$comname=$yid;
		}
	elseif($enrolstatus=='C'){
		$comtype='year';
		$comname='none';
		}
	else{
		if($enrolstatus=='P'){$comtype='alumni';}
		elseif($enrolstatus=='EN'){$comtype='enquired';}
		elseif($enrolstatus=='AC'){$comtype='accepted';}
		/*all other enrolstatus values place the student within the*/
		/*application procedure*/
		else{$comtype='applied';}
		$comname=$enrolstatus;
		}

	$community=array('id'=>'','type'=>$comtype,'name'=>$comname);
	join_community($sid,$community);
	$result[]=get_string('newstudentadded',$book);


include('scripts/results.php');
include('scripts/redirect.php');
?>
