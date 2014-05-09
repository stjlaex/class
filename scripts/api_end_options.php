<?php

	api_log_to_history($uid,$action,$device,$ip);

	if($errors){
		$result=array(
			'success'=>'false',
			'errors'=>$errors
			);
		}

	header('Content-Type: application/json'); 
	echo json_encode($result);

?>
