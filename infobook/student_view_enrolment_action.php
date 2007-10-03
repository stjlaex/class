<?php
/**				   				student_view_enrolment_action.php
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

	$Enrolment=fetchEnrolment($sid);

	/* Only update enrolment community if it's changed*/
	if($enrolyid!=$Enrolment['YearGroup']['value'] or 
	   $enrolyear!=$Enrolment['Year']['value'] or 
	   $enrolstatus!=$Enrolment['EnrolmentStatus']['value']){
			/*see community_list_action for the same - needs to be moved out*/
			/*crucial to the logic of enrolments*/
			if($enrolstatus=='EN'){$newtype='enquired';}
			elseif($enrolstatus=='AC'){$newtype='accepted';}
			else{$newtype='applied';}
			$newcom=array('id'=>'','type'=>$newtype, 
					  'name'=>$enrolstatus.':'.$enrolyid,'year'=>$enrolyear);
			if($enrolstatus=='C'){
				$newcom=array('id'=>'','type'=>'year', 'name'=>$enrolyid);
				}
			$oldcommunities=join_community($sid,$newcom);
			}

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
