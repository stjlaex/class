<?php

	if($action=='register'){$log='api;'.$action.':::'.$email;}
	else{$log='api;'.$action.':::'.$username.':::'.$token;}
	api_log_to_history($uid,$log,$device,$ip);

	if($errors and count($errors)>0){
		$result=array(
			'success'=>false,
			'errors'=>$errors
			);
		}

	header('Content-Type: application/json');
	echo json_encode($result);

?>
