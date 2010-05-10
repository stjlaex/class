<?php
/**			       		list_cohort.php
 *
 */

//if(!isset($required)){$required='yes';}
$required='yes';

if($r>-1){
	include('scripts/list_stage.php');
	include('scripts/list_calendar_year.php');
	}
else{
	print '<label>'.get_string('youhavenoacademicresponsibilities').'</label>';
	}
?>