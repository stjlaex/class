<?php
/**
 *					curl_calls.php
 */

function feeder_fetch($scriptname,$feeder,$postdata){
	global $CFG;
	if(isset($CFG->feeder_code) and $CFG->feeder_code!=''){
		$postdata['feeder_code']=$CFG->feeder_code;
		}
	else{
		$postdata['feeder_code']='';
		}

	$username='class';
	//$ip=$_SERVER['SERVER_ADDR'];
	$ip='';
	$salt=$CFG->eportfolioshare;
	$secret=md5($salt . $ip);
	$token=md5($username . $secret);// This gets passed for authentication 
	$url=$feeder.'/class/admin/httpscripts/'.$scriptname.'.php?username=' 
			.$username.'&password='. $token;
	$curl=curl_init();
	curl_setopt($curl,CURLOPT_SSL_VERIFYPEER,0);
	curl_setopt($curl,CURLOPT_URL,$url);
	curl_setopt($curl,CURLOPT_POST,1);
	curl_setopt($curl,CURLOPT_RETURNTRANSFER,1);
	curl_setopt($curl,CURLOPT_POSTFIELDS, $postdata);
	$response=curl_exec($curl);
	curl_close($curl);

	//trigger_error($url,E_USER_WARNING);
	//trigger_error($response,E_USER_WARNING);
	
	if($response!=''){$Response=xmlreader($response);}
	else{$Response=array();}

	return $Response;
	}

?>