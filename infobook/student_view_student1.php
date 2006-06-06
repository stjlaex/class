<?php
/**			  					student_view_student1.php
 */

$action='student_view.php';
include('scripts/sub_action.php');

if($sub=='Submit'){
	/*Check user has permission to edit*/
	$yid=$Student['NCyearActual']['id_db'];
	$perm=getYearPerm($yid,$respons);
	$neededperm='w';
	include('scripts/perm_action.php');

	$in=0;
	while(list($key,$val)=each($Student)){
		if(isset($val['value']) & is_array($val)){
			$field=$val['field_db'];
			$inname=$field.$in;
			$inval=clean_text($_POST{"$inname"});
			if($val['value']!=$inval){
//				the value has changed, update database
				$result[]=$val['label']." : ".$inval."<br />";
				if($val['table_db']=='' OR $val['table_db']=='student'){
					if(mysql_query("UPDATE student SET $field='$inval'
									WHERE id='$sid'")){
					$Student[$key]['value']=$inval;	
					}
					else{$error[]=mysql_error();}
					}
				else if($val['table_db']=='info'){
					if(mysql_query("UPDATE info SET $field='$inval'
									WHERE student_id='$sid'")){
					$Student[$key]['value']=$inval;	
					}
					else{$error[]="Info table:".mysql_error();}
					}					
				}	
			$in++;	
			}
		}
	$_SESSION{'Student'}=$Student;
	}
include('scripts/results.php');
include('scripts/redirect.php');
?>
