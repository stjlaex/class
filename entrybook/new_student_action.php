<?php
/**			  					new_student_action.php
 */

$action='new_student.php';
include('scripts/sub_action.php');

if($sub=='Submit'){

	$Student=fetchStudent();
	mysql_query("INSERT INTO student SET surname=''");
	$sid=mysql_insert_id();
	mysql_query("INSERT INTO info SET student_id='$sid'");
	reset($Student);
	while(list($key,$val)=each($Student)){
		if(isset($val['value']) and is_array($val) and isset($val['table_db'])){
			$field=$val['field_db'];
			$inname=$field;
			$inval=clean_text($_POST[$inname]);
				if($val['table_db']=='student'){
					mysql_query("UPDATE student SET $field='$inval'	WHERE id='$sid'");
					}
				elseif($val['table_db']=='info'){
					mysql_query("UPDATE info SET $field='$inval' WHERE student_id='$sid'");
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
		elseif($enrolstatus=='AP'){$comtype='applied';}
		elseif($enrolstatus=='AC'){$comtype='accepted';}
		$comname=$comtype;
		}

	$community=array('id'=>'','type'=>$comtype,'name'=>$comname);
	join_community($sid,$community);
	$result[]=get_string('newstudentadded',$book);


include('scripts/results.php');
include('scripts/redirect.php');
?>
