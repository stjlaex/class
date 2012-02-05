<?php
/**			  					student_view_student1.php
 */

$action='student_view.php';
include('scripts/sub_action.php');

if($sub=='Submit'){
	/*Check user has permission to edit*/
	$yid=$Student['YearGroup']['value'];
	$update_flag=false;
	$perm=getYearPerm($yid);
	$neededperm='w';
	include('scripts/perm_action.php');

	while(list($key,$val)=each($Student)){
		if(isset($val['value']) and is_array($val) and isset($val['table_db'])){
			$field=$val['field_db'];
			$inname=$field;
			$inval=clean_text($_POST[$inname]);
			if($val['value']!=$inval){
				/*the value has changed, update database*/
				if($val['table_db']=='student'){
					mysql_query("UPDATE student SET $field='$inval' WHERE id='$sid'");
					$update_flag=true;
					}
				elseif($val['table_db']=='info'){
					mysql_query("UPDATE info SET $field='$inval' WHERE student_id='$sid'");
					$update_flag=true;
					}
				}
			}
		}

	/*check if the accomodation community needs to be updated*/
	if($Student['Boarder']['value']!=$_POST['boarder'] 
	   or $Student['Gender']['value']!=$_POST['gender']){
		set_accomodation($sid);
		$update_flag=true;
		}

	}
include('scripts/redirect.php');
?>
