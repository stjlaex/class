<?php
/**			       		list_cohort.php
 *
 */

$required='yes';

if($r>-1){
	include('scripts/list_stage.php');
	include('scripts/list_calendar_year.php');
	}
else{
	print '<label>'.get_string('youhavenoacademicresponsibilities').'</label>';
	}
?>