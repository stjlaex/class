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

	$d_ie=mysql_query("SELECT id,name,comment,othertype FROM categorydef WHERE type='inf' AND subtype='student';");
	while($field=mysql_fetch_array($d_ie,MYSQL_ASSOC)){
		$fieldid=$field['id'];
		if(isset($_POST['extra_'.$fieldid]) and $_POST['extra_'.$fieldid]!=''){$newval=$_POST['extra_'.$fieldid];}else{$newval='';}
		$d_v=mysql_query("SELECT value FROM info_extra WHERE catdef_id='$fieldid' AND user_id='$sid';");
		if(mysql_num_rows($d_v)>0){mysql_query("UPDATE info_extra SET value='$newval' WHERE user_id='$sid' AND catdef_id='$fieldid';");}
		else{mysql_query("INSERT INTO info_extra (catdef_id,user_id,value) VALUES ('$fieldid','$sid','$newval');");}
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
