<?php 
/**									 enrolments_edit_action.php
 */

$action='enrolments_matrix.php';
$action_post_vars=array('enrolyear');


if(isset($_POST['enrolyear'])){$enrolyear=$_POST['enrolyear'];}
if(isset($_POST['values'])){$values=(array)$_POST['values'];}
if(isset($_POST['comids'])){$comids=(array)$_POST['comids'];}
if(isset($_POST['enrolstatus'])){$enrolstatus=$_POST['enrolstatus'];}

include('scripts/sub_action.php');

if($sub=='Submit'){

	if($enrolstatus=='capacity'){
		$field='capacity';
		}
	else{
		$field='count';
		}

	while(list($cindex,$comid)=each($comids)){
		$value=$values[$cindex];
		mysql_query("UPDATE community SET $field='$value' WHERE id='$comid';");
		}
	}

include('scripts/redirect.php');
?>
