<?php 
/**				   	   				   formgroup_matrix_action.php
 */

$action='formgroup_matrix.php';

if($_POST{'tid'}!=''){$newtid=$_POST{'tid'};}
if(isset($_POST{'newfid'})){$newfid=$_POST{'newfid'};} else{$newfid='';}

include('scripts/sub_action.php');

if($newtid!='' AND $newfid!=''){
		$d_test=mysql_query("SELECT id, yeargroup_id FROM form WHERE teacher_id='$newtid'");
		$rows=mysql_num_rows($d_test);

		/*Check user has permission to edit*/
		$d_test=mysql_query("SELECT yeargroup_id FROM form WHERE id='$newfid'");
		$formyid=mysql_result($d_form,0);
		$perm=getYearPerm($yid,$respons);
		$neededperm='w';
		include('scripts/perm_action.php');

		if($rows==0){
			if(mysql_query("UPDATE form SET teacher_id='$newtid' WHERE id='$newfid'")){
				$d_form=mysql_query("SELECT DISTINCT yeargroup_id
						FROM form WHERE id='$newfid'");
				$yid=mysql_result($d_form,0);
				$d_users=mysql_query("SELECT DISTINCT uid
						FROM users WHERE username='$newtid'");
				$uid=mysql_result($d_users,0);
				$d_groups=mysql_query("SELECT DISTINCT gid
					FROM groups WHERE yeargroup_id='$yid' AND course_id IS NULL");
				$gid=mysql_result($d_groups,0);
				mysql_query("INSERT perms (uid, gid, r, w, x) 
					VALUES('$uid','$gid','1','1','0')");
				$result[]='Assigned form';
				}
			else{$error[]=mysql_error();}	
			}
		else{$result[]='Teacher '.$newtid.' already has been assigned a form!';}
		}

include('scripts/results.php');
include('scripts/redirect.php');
?>
