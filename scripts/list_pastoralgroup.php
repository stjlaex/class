<?php
/**			       		list_pastoralgroup.php
 */

$required='yes';
if(sizeof($ryids)>0){
	$selyid=$ryids[0];
	include('list_year.php');
	}
elseif(sizeof($rfids)>0){
	$selfid=$rfids[0];
	include('list_form.php');
	}
else{
	}
?>