<?php
/*****									student_view_enrolment_action.php
 *
 */

$action='student_view_enrolment.php';
include('scripts/sub_action.php');

if($sub=='Submit'){
	/*Check user has permission to edit*/
	$yid=$Student['YearGroup']['value'];
	$perm=getYearPerm($yid, $respons);
	//$neededperm='w';
	include('scripts/perm_action.php');

	if(isset($_POST['enrolstatus'])){$enrolstatus=$_POST['enrolstatus'];}
	if(isset($_POST['enrolyear'])){$enrolyear=$_POST['enrolyear'];}else{$enrolyear='';}
	if(isset($_POST['enrolyid'])){$enrolyid=$_POST['enrolyid'];}else{$enrolyid='';}

	if($enrolstatus=='EN'){$newtype='enquired';}
	elseif($enrolstatus=='AC' or $enrolstatus=='C'){$newtype='accepted';}
	else{$newtype='applied';}
	//trigger_error($enrolstatus.' '.$enrolyear.' '.$enrolyid.'-'.$newtype,E_USER_WARNING);
	$newcom=array('id'=>'','type'=>$newtype, 
					  'name'=>$enrolstatus.':'.$enrolyid,'year'=>$enrolyear);

	$oldcommunities=join_community($sid,$newcom);


	$Enrolment=fetchEnrolment();
	reset($Enrolment);
	while(list($key,$val)=each($Enrolment)){
		if(isset($val['value']) and is_array($val) and isset($val['table_db'])){
			$field=$val['field_db'];
			$inname=$field;
			$inval=clean_text($_POST[$inname]);
			if($val['table_db']=='info'){
				mysql_query("UPDATE info SET
							$field='$inval' WHERE student_id='$sid'");
				}
			}
		}

	}

	include('scripts/redirect.php');
?>
