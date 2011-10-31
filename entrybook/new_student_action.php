<?php
/**			  					new_student_action.php
 *
 */

$action_post_vars=array('sid');

if($_POST['boarder']!='N' and $_POST['boarder']!=''){
	/*extra fields for residencial students*/
	$Inputs[]=fetchStay();
	$action='new_boarder.php';
	}
else{
	$action='new_contact.php';
	}

include('scripts/sub_action.php');

if($sub=='Submit'){

	$Student=fetchStudent();
	$Enrolment=fetchEnrolment(-1);
	$Student[]=$Enrolment['Siblings'];
	$Student[]=$Enrolment['StaffChild'];
	mysql_query("INSERT INTO student SET surname='';");
	$sid=mysql_insert_id();
	mysql_query("INSERT INTO info SET student_id='$sid';");

	foreach($Student as $key => $val){
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

	/* Automatically set the date of application. */
	$todate=date('Y-m-d');
	mysql_query("UPDATE info SET appdate='$todate' WHERE student_id='$sid';");


	/*Figure out the community they need to join*/
	if($enrolstatus=='C' or $enrolstatus=='G'){
		/* Joining the current roll directly is special
		 * as the student must join a yeargroup community
		 */
		$community=array('id'=>'','type'=>'year','name'=>$enrolyid);
		}
	else{
		if($enrolstatus=='P'){$comtype='alumni';}
		elseif($enrolstatus=='EN'){$comtype='enquired';}
		elseif($enrolstatus=='AC'){$comtype='accepted';}
		/* All other enrolstatus values place the student within the
		 * applied category - apllication is in progress
		 */
		else{$comtype='applied';}
		//if($enrolstatus==''){$enrolstatus='EN';}/*default if blank*/
		$comname=$enrolstatus.':'.$enrolyid;
		$community=array('id'=>'','type'=>$comtype,'name'=>$comname,'year'=>$enrolyear);
		}

	join_community($sid,$community);

include('scripts/results.php');
include('scripts/redirect.php');
?>
