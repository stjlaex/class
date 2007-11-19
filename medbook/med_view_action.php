<?php
/**									med_view_action.php
 */

$action='med_view.php';

include('scripts/sub_action.php');

	/*Check user has permission to edit*/
	$yid=$Student['YearGroup']['value'];
	$perm=getMedicalPerm($yid,$respons);
	$neededperm='w';
	include('scripts/perm_action.php');

if($sub=='medstatus'){
	if($Student['MedicalFlag']['value']=='Y'){
		mysql_query("UPDATE info SET medical='N' WHERE student_id='$sid'");
		$action='med_student_list.php';
		}
	elseif($Student['MedicalFlag']['value']=='N'){
		mysql_query("UPDATE info SET medical='Y' WHERE student_id='$sid'");
		}
	}
elseif($sub=='Submit'){
	$Notes=$Medical['Notes'];
	while(list($index,$Note)=each($Notes['Note'])){
		$todate=date('Y-m-d');
		$cattype=$Note['MedicalCategory']['value_db'];
		$inname='detail'.$index;
		$inval=clean_text($_POST[$inname]);
		if($Note['Detail']['value']!=$inval){
			$noteid=$Note['id_db'];
			if($noteid==''){
				mysql_query("INSERT INTO background
						(student_id,detail,type,entrydate) 
						VALUES ('$sid','$inval','$cattype','$todate');");
				}
			else{
				mysql_query("UPDATE background SET detail='$inval', entrydate='$todate'
									WHERE id='$noteid';");
				}
			}
		}
	}
include('scripts/redirect.php');
?>