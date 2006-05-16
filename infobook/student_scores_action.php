<?php
/**									student_scores_action.php
 *
 */

$action='student_scores.php';

include('scripts/sub_action.php');

$key=array_search($sid,$sids);	
	
if($sub=='Previous'){
	if($key>1){$key=$key-1;}else{$key=0;}
	$_POST{'sid'}=$sids[$key];
	$sid=$sids[$key];
	}
elseif($sub=='Next'){
	$nosids=sizeof($sids);
	if($key<$nosids-1){$key=$key+1;}else{$key=$nosids-1;}
	$_POST{'sid'}=$sids[$key];
	$sid=$sids[$key];
	}

include("scripts/redirect.php");
?>





















