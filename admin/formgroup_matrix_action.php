<?php 
/**				   	   				   formgroup_matrix_action.php
 */

$action='formgroup_matrix.php';

if($_POST['newtid']!=''){$newtid=$_POST['newtid'];}else{$newtid='';}
if(isset($_POST['newfid'])){$newfid=$_POST['newfid'];}else{$newfid='';}

include('scripts/sub_action.php');

if($newtid!='' AND $newfid!=''){
		$d_test=mysql_query("SELECT id, yeargroup_id FROM form WHERE teacher_id='$newtid'");
		$rows=mysql_num_rows($d_test);

		/*Check user has permission to edit*/
		$perm=getFormPerm($newfid,$respons);
		$neededperm='w';
		include('scripts/perm_action.php');

		if($rows==0){
			mysql_query("UPDATE form SET teacher_id='$newtid' WHERE id='$newfid'");
			$result[]='Teacher assigned to the form.';
			}
		else{$error[]='Teacher '.$newtid.' already has been assigned a form!';}
		}

include('scripts/results.php');
include('scripts/redirect.php');
?>
