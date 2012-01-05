<?php
/**			       		list_cohort.php
 *
 */

if(!isset($required)){$selrequired='yes';}else{$selrequired=$required;}
if(!isset($onchange)){$selonchange='no';}else{$selonchange=$onchange;}

if($r>-1){
	$onchange=$selonchange;
	$required=$selrequired;
	include('scripts/list_stage.php');

	$required=$selrequired;
	$onchange=$selonchange;
	include('scripts/list_calendar_year.php');
	}
else{
	print '<label>'.get_string('youhavenoacademicresponsibilities').'</label>';
	}
?>